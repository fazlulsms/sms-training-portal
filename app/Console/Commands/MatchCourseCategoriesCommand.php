<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Console\Command;

/**
 * Backfills course.category_id by matching the legacy course.category text
 * field against seeded CourseCategory records.
 *
 * Usage:
 *   php artisan ltf:match-categories            — preview + confirm
 *   php artisan ltf:match-categories --dry-run  — preview only, no writes
 *   php artisan ltf:match-categories --force    — apply without confirmation
 */
class MatchCourseCategoriesCommand extends Command
{
    protected $signature = 'ltf:match-categories
                            {--dry-run : Show matches without saving}
                            {--force   : Apply without confirmation prompt}';

    protected $description = 'Backfill course category_id from legacy category text field using fuzzy matching';

    public function handle(): int
    {
        $isDry = $this->option('dry-run');

        // ── Load all active CourseCategory records ────────────────────────────
        $categories = CourseCategory::orderBy('display_order')->get();

        if ($categories->isEmpty()) {
            $this->error('No CourseCategory records found. Run: php artisan db:seed --class=CourseCategorySeeder');
            return self::FAILURE;
        }

        // ── Load courses that need backfilling ────────────────────────────────
        $courses = Course::whereNotNull('category')
            ->where('category', '!=', '')
            ->whereNull('category_id')
            ->get(['id', 'name', 'category']);

        if ($courses->isEmpty()) {
            $this->info('All courses already have category_id set — nothing to do.');
            return self::SUCCESS;
        }

        $this->line('');
        $this->line(" Found <comment>{$courses->count()}</comment> course(s) with legacy category text and no category_id.");
        $this->line('');

        // ── Build match results ───────────────────────────────────────────────
        $matches   = [];
        $unmatched = [];

        foreach ($courses as $course) {
            $best = $this->findBestMatch($course->category, $categories);

            if ($best !== null) {
                $matches[] = [
                    'course'    => $course,
                    'matched'   => $best['category'],
                    'score'     => $best['score'],
                    'method'    => $best['method'],
                ];
            } else {
                $unmatched[] = $course;
            }
        }

        // ── Display matched results ───────────────────────────────────────────
        if ($matches) {
            $this->line(' <info>MATCHED</info>');
            $this->table(
                ['Course', 'Legacy Category Text', 'Matched To', 'Method'],
                array_map(fn($m) => [
                    $m['course']->name,
                    $m['course']->category,
                    $m['matched']->name,
                    $m['method'],
                ], $matches)
            );
        }

        // ── Display unmatched results ─────────────────────────────────────────
        if ($unmatched) {
            $this->line('');
            $this->line(' <comment>UNMATCHED</comment> — these will be skipped (set category_id manually in admin):');
            foreach ($unmatched as $c) {
                $this->line("   • [{$c->id}] {$c->name}  →  \"{$c->category}\"");
            }
        }

        $this->line('');

        if ($isDry) {
            $this->warn('Dry-run mode — no changes saved.');
            return self::SUCCESS;
        }

        if (empty($matches)) {
            $this->warn('No matches found — nothing to save.');
            return self::SUCCESS;
        }

        // ── Confirm before applying ───────────────────────────────────────────
        if (! $this->option('force')) {
            if (! $this->confirm("Apply " . count($matches) . " match(es)?")) {
                $this->line('Aborted.');
                return self::SUCCESS;
            }
        }

        // ── Apply updates ─────────────────────────────────────────────────────
        $applied = 0;
        foreach ($matches as $m) {
            $m['course']->update(['category_id' => $m['matched']->id]);
            $applied++;
        }

        $this->info("Done — {$applied} course(s) updated.");

        if ($unmatched) {
            $skipped = count($unmatched);
            $this->warn("{$skipped} course(s) were not matched and remain unset.");
        }

        return self::SUCCESS;
    }

    // ── Hard overrides for known ambiguous legacy values ─────────────────────
    // Map normalised legacy text → CourseCategory slug (or null = leave unmatched).
    // Add entries here whenever the algorithm can't resolve a text unambiguously.

    // Keys must be the OUTPUT of normalise() — punctuation stripped, lowercased.
    private const OVERRIDES = [
        'hr compliance'           => null,               // too ambiguous — set in admin
        'auditing and compliance' => 'audit-assurance',  // prefer Audit & Assurance over Social Compliance
    ];

    // ── Matching logic ────────────────────────────────────────────────────────

    /**
     * Finds the best CategoryMatch for a given legacy category text string.
     * Returns null if no reasonable match is found.
     *
     * Match priority:
     *   1. Hard override (OVERRIDES constant)
     *   2. Exact case-insensitive match on name
     *   3. Substring containment (one name fully inside the other)
     *   4. Normalised word-overlap (≥ 1 shared root word), tiebroken by
     *      first-meaningful-word match and then by overlap count
     */
    private function findBestMatch(string $text, $categories): ?array
    {
        $textNorm = $this->normalise($text);

        // Pass 1 — hard override ───────────────────────────────────────────────
        if (array_key_exists($textNorm, self::OVERRIDES)) {
            $slug = self::OVERRIDES[$textNorm];
            if ($slug === null) {
                return null; // explicitly unmatched
            }
            $cat = $categories->firstWhere('slug', $slug);
            return $cat ? ['category' => $cat, 'score' => 100, 'method' => 'override'] : null;
        }

        // Pass 2 — exact match ─────────────────────────────────────────────────
        foreach ($categories as $cat) {
            if ($this->normalise($cat->name) === $textNorm) {
                return ['category' => $cat, 'score' => 100, 'method' => 'exact'];
            }
        }

        // Pass 3 — substring containment ──────────────────────────────────────
        foreach ($categories as $cat) {
            $catNorm = $this->normalise($cat->name);
            if (str_contains($textNorm, $catNorm) || str_contains($catNorm, $textNorm)) {
                return ['category' => $cat, 'score' => 90, 'method' => 'substring'];
            }
        }

        // Pass 4 — word-overlap with stemming ─────────────────────────────────
        $textWords      = $this->words($text);
        $textFirstStem  = $textWords ? $this->stemWord($textWords[0]) : '';
        $scored         = [];

        foreach ($categories as $cat) {
            $catWords      = $this->words($cat->name);
            $catFirstStem  = $catWords ? $this->stemWord($catWords[0]) : '';
            $overlap       = 0;
            $firstWordHit  = false;

            foreach ($textWords as $tw) {
                foreach ($catWords as $cw) {
                    if ($tw === $cw || $this->stemWord($tw) === $this->stemWord($cw)) {
                        $overlap++;
                        if ($this->stemWord($tw) === $catFirstStem || $this->stemWord($cw) === $textFirstStem) {
                            $firstWordHit = true;
                        }
                        break;
                    }
                }
            }

            if ($overlap > 0) {
                $scored[] = [
                    'category'     => $cat,
                    'score'        => $overlap,
                    'first_word'   => $firstWordHit,
                    'method'       => 'word-overlap',
                ];
            }
        }

        if (empty($scored)) {
            return null;
        }

        // Sort: highest overlap first, then prefer first-word matches
        usort($scored, function ($a, $b) {
            if ($b['score'] !== $a['score']) {
                return $b['score'] <=> $a['score'];
            }
            return ($b['first_word'] ? 1 : 0) <=> ($a['first_word'] ? 1 : 0);
        });

        return $scored[0];
    }

    /** Lowercase, remove punctuation, collapse whitespace. */
    private function normalise(string $s): string
    {
        return trim(preg_replace('/\s+/', ' ', strtolower(preg_replace('/[^a-z0-9\s]/i', ' ', $s))));
    }

    /**
     * Tokenise into lowercase words, filtering out common stop words that
     * would cause false-positive matches in this domain.
     */
    private function words(string $s): array
    {
        $stop = ['and', 'the', 'of', 'a', 'an', 'in', 'for', 'to', 'by', 'vs', 'with'];
        $raw  = preg_split('/[\s\W]+/', strtolower($s), -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_diff($raw, $stop));
    }

    /**
     * Minimal suffix stripper: removes -ing, -tion, -ance, -ment, -ity, -al
     * so "Auditing" matches "Audit", "Sustainability" matches "Sustainable", etc.
     */
    private function stemWord(string $w): string
    {
        $suffixes = ['ation', 'tion', 'ment', 'ance', 'ence', 'ity', 'ing', 'al', 'ic', 's'];
        foreach ($suffixes as $sfx) {
            if (strlen($w) > strlen($sfx) + 3 && str_ends_with($w, $sfx)) {
                return substr($w, 0, -strlen($sfx));
            }
        }
        return $w;
    }
}
