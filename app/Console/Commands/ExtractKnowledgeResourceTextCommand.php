<?php

namespace App\Console\Commands;

use App\Models\KnowledgeResource;
use App\Services\KnowledgeResourceTextExtractor;
use Illuminate\Console\Command;

class ExtractKnowledgeResourceTextCommand extends Command
{
    protected $signature = 'knowledge:extract {--force : Re-extract resources that already have text}';
    protected $description = 'Extract machine-readable text from Knowledge Hub files for AI Course Generator V2';

    public function handle(KnowledgeResourceTextExtractor $extractor): int
    {
        $query = KnowledgeResource::query()->where('status', 'approved');
        if (!$this->option('force')) {
            $query->where(fn ($q) => $q->whereNull('extracted_text')->orWhere('extraction_status', '!=', 'ready'));
        }

        $count = 0;
        $query->orderBy('id')->each(function (KnowledgeResource $resource) use ($extractor, &$count) {
            $this->line("Extracting #{$resource->id}: {$resource->title}");
            if ($this->option('force')) {
                $resource->update(['extracted_text' => null, 'extraction_status' => 'pending']);
            }
            $extractor->extract($resource->refresh());
            $count++;
        });
        $this->info("Processed {$count} resource(s).");
        return self::SUCCESS;
    }
}
