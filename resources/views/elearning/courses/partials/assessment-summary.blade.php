{{--
  Assessment Summary panel — included in course edit/show pages.
  Requires: $course (Course model with course_id).
--}}
@php
    use App\Models\ElearningLesson;
    use App\Models\ElearningQuiz;
    use App\Models\QuizAttempt;
    use App\Models\QuizAttemptOverride;

    $assessmentLessons = ElearningLesson::where('course_id', $course->id)
        ->where('lesson_type', 'assessment')
        ->orderBy('lesson_order')
        ->with('quizzes.questions')
        ->get();

    $moduleChecks   = $assessmentLessons->filter(fn($l) => str_contains($l->title, 'Knowledge Check'));
    $finalAssessment= $assessmentLessons->first(fn($l) => str_contains($l->title, 'Final Course Assessment'));

    // For each quiz, gather aggregate attempt stats
    $quizStats = [];
    foreach ($assessmentLessons as $al) {
        foreach ($al->quizzes as $quiz) {
            $totalAttempts = QuizAttempt::where('quiz_id', $quiz->id)->count();
            $passedCount   = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('score', '>=', $quiz->pass_mark)->distinct('elearning_enrollment_id')->count('elearning_enrollment_id');

            // Blocked = took all standard+override attempts and never passed
            $blockedCount  = 0;
            $enrollIds = QuizAttempt::where('quiz_id', $quiz->id)
                ->whereNotNull('elearning_enrollment_id')
                ->distinct('elearning_enrollment_id')
                ->pluck('elearning_enrollment_id');

            foreach ($enrollIds as $eid) {
                $hasPassed   = QuizAttempt::where('quiz_id', $quiz->id)->where('elearning_enrollment_id', $eid)->where('score', '>=', $quiz->pass_mark)->exists();
                if ($hasPassed) continue;
                $override    = QuizAttemptOverride::where('enrollment_id', $eid)->where('quiz_id', $quiz->id)->first();
                $effectiveMax= $quiz->max_attempt + ($override?->extra_attempts ?? 0);
                $taken       = QuizAttempt::where('quiz_id', $quiz->id)->where('elearning_enrollment_id', $eid)->count();
                if ($effectiveMax > 0 && $taken >= $effectiveMax) $blockedCount++;
            }

            $quizStats[$quiz->id] = [
                'total_attempts' => $totalAttempts,
                'passed_learners'=> $passedCount,
                'blocked_learners'=> $blockedCount,
            ];
        }
    }
@endphp

<div style="background:#fff; border:1.5px solid #e2e8f0; border-radius:12px; overflow:hidden; margin-top:28px;">
    <div style="padding:14px 20px; font-size:14px; font-weight:800; color:#1e3a8a; border-bottom:1px solid #f1f5f9;
                display:flex; align-items:center; justify-content:space-between; background:#f8fafc;">
        <span>📋 Assessment Summary</span>
        <span style="font-size:11.5px; font-weight:400; color:#6b7280;">
            {{ $moduleChecks->count() }} module check{{ $moduleChecks->count() !== 1 ? 's' : '' }}
            · Final assessment: {{ $finalAssessment ? 'Yes' : 'No' }}
        </span>
    </div>

    {{-- ILT vs eLearning note (Phase 7 documentation) --}}
    <div style="padding:10px 20px; background:#fef9c3; border-bottom:1px solid #fde68a; font-size:12px; color:#78350f;">
        <strong>Note:</strong> These quizzes are course-specific (eLearning only).
        ILT Question Bank exams are managed separately under <em>Training Exams</em>.
        A shared question library may be introduced in a future phase.
    </div>

    @if($assessmentLessons->isEmpty())
    <div style="padding:32px; text-align:center; color:#6b7280; font-size:13px;">
        <div style="font-size:28px; margin-bottom:8px;">📭</div>
        <p>No assessment lessons found. Run Mode B generation to create module knowledge checks and final assessment.</p>
    </div>
    @else

    {{-- Blocked learner warning --}}
    @php $totalBlocked = collect($quizStats)->sum('blocked_learners'); @endphp
    @if($totalBlocked > 0)
    <div style="padding:10px 20px; background:#fef2f2; border-bottom:1px solid #fecaca; font-size:12.5px; color:#991b1b; display:flex; align-items:center; gap:10px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <strong>Warning:</strong> {{ $totalBlocked }} learner(s) are blocked across this course's assessments.
        Learners are permanently blocked if they exhaust all attempts without passing.
        Use the Enrollment detail page to grant extra attempts or reset.
    </div>
    @endif

    <div style="overflow-x:auto;">
    <table style="width:100%; font-size:12.5px; border-collapse:collapse;">
        <thead>
            <tr style="background:#f8fafc;">
                <th style="padding:9px 16px; text-align:left; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#64748b; border-bottom:1px solid #e2e8f0;">Assessment</th>
                <th style="padding:9px 12px; text-align:left; color:#64748b; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid #e2e8f0;">Type</th>
                <th style="padding:9px 12px; text-align:center; color:#64748b; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid #e2e8f0;">Questions</th>
                <th style="padding:9px 12px; text-align:center; color:#64748b; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid #e2e8f0;">Pass Mark</th>
                <th style="padding:9px 12px; text-align:center; color:#64748b; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid #e2e8f0;">Max Attempts</th>
                <th style="padding:9px 12px; text-align:center; color:#64748b; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid #e2e8f0;">Attempts Taken</th>
                <th style="padding:9px 12px; text-align:center; color:#64748b; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid #e2e8f0;">Passed</th>
                <th style="padding:9px 12px; text-align:center; color:#64748b; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid #e2e8f0;">Blocked</th>
                <th style="padding:9px 12px; text-align:center; color:#64748b; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid #e2e8f0;"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($assessmentLessons as $al)
                @foreach($al->quizzes as $quiz)
                @php
                    $stats  = $quizStats[$quiz->id];
                    $isFinal= str_contains($al->title, 'Final Course Assessment');
                    $isMod  = str_contains($al->title, 'Knowledge Check');
                @endphp
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:10px 16px; font-weight:600; max-width:240px;">
                        {{ $al->title }}
                        <div style="font-size:11px; color:#64748b; font-weight:400; margin-top:2px;">
                            @if($al->completion_rule === 'pass_quiz')
                                Pass required for completion
                            @else
                                No pass requirement
                            @endif
                        </div>
                    </td>
                    <td style="padding:10px 12px;">
                        @if($isFinal)
                            <span style="font-size:10.5px;font-weight:700;background:#fef3c7;color:#78350f;padding:2px 7px;border-radius:4px;">Final Assessment</span>
                        @elseif($isMod)
                            <span style="font-size:10.5px;font-weight:700;background:#ede9fe;color:#4c1d95;padding:2px 7px;border-radius:4px;">Module Check</span>
                        @else
                            <span style="font-size:10.5px;font-weight:700;background:#f1f5f9;color:#475569;padding:2px 7px;border-radius:4px;">Assessment</span>
                        @endif
                    </td>
                    <td style="padding:10px 12px; text-align:center; font-weight:700;">{{ $quiz->questions->count() }}</td>
                    <td style="padding:10px 12px; text-align:center; font-weight:700;">{{ $quiz->pass_mark }}%</td>
                    <td style="padding:10px 12px; text-align:center;">{{ $quiz->max_attempt ?: '∞' }}</td>
                    <td style="padding:10px 12px; text-align:center;">{{ $stats['total_attempts'] }}</td>
                    <td style="padding:10px 12px; text-align:center;">
                        <span style="color:#15803d; font-weight:700;">{{ $stats['passed_learners'] }}</span>
                    </td>
                    <td style="padding:10px 12px; text-align:center;">
                        @if($stats['blocked_learners'] > 0)
                            <span style="background:#fee2e2;color:#dc2626;font-size:11px;font-weight:700;padding:2px 7px;border-radius:4px;">
                                {{ $stats['blocked_learners'] }} blocked
                            </span>
                        @else
                            <span style="color:#64748b;">0</span>
                        @endif
                    </td>
                    <td style="padding:10px 12px; text-align:center;">
                        <a href="{{ route('elearning.quizzes.preview', [$course, $al, $quiz]) }}"
                           style="font-size:11.5px; font-weight:700; color:#1e3a8a; text-decoration:none; white-space:nowrap;">Preview →</a>
                    </td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
    </div>
    @endif

    {{-- Assessment type key (Phase 5) --}}
    <div style="padding:14px 20px; background:#f8fafc; border-top:1px solid #e2e8f0; display:grid; grid-template-columns:repeat(3,1fr); gap:10px; font-size:11.5px;">
        <div>
            <span style="font-weight:700; color:#475569;">🔷 Inline Knowledge Check</span><br>
            <span style="color:#6b7280;">Used for learning engagement only. Not scored. Does not affect completion.</span>
        </div>
        <div>
            <span style="font-weight:700; color:#4c1d95;">📋 Module Knowledge Check</span><br>
            <span style="color:#6b7280;">Scored assessment. Learner must pass to complete this module.</span>
        </div>
        <div>
            <span style="font-weight:700; color:#78350f;">🏆 Final Course Assessment</span><br>
            <span style="color:#6b7280;">Scored final assessment. Required for course completion and certificate eligibility.</span>
        </div>
    </div>
</div>
