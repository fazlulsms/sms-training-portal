@extends('layouts.app')
@section('page-title', 'Review Course Blueprint')

@section('content')
<div class="page-wrap" style="max-width:1100px;margin:auto;">
    <x-page-header :title="'Blueprint: '.$course->name" desc="Review the source mapping before any lesson content or assessments are generated." />

    @if($course->blueprint_status === 'awaiting_approval')
        <div class="alert alert-info">This V2 course is paused. Generation starts only after blueprint approval.</div>
    @else
        <div class="alert alert-success">Blueprint approved {{ $course->blueprint_approved_at?->format('d M Y, g:i A') }}. Content remains traceable to the sources below.</div>
    @endif

    <div class="stat-grid-3">
        <div class="stat-card"><div><div class="stat-label">Modules</div><div class="stat-value">{{ $course->blueprintModules->count() }}</div></div></div>
        <div class="stat-card"><div><div class="stat-label">Lessons</div><div class="stat-value">{{ $course->elearningLessons->where('lesson_type','!=','assessment')->count() }}</div></div></div>
        <div class="stat-card"><div><div class="stat-label">Approved Sources</div><div class="stat-value">{{ $course->knowledgeResources->count() }}</div></div></div>
    </div>

    @if($course->blueprint_status === 'approved')
    <div class="stat-grid-3">
        <div class="stat-card"><div><div class="stat-label">Generation Status</div><div class="stat-value-sm">{{ ucfirst($course->gen_status) }}</div></div></div>
        <div class="stat-card"><div><div class="stat-label">Estimated Learning Time</div><div class="stat-value-sm">{{ intdiv($course->estimated_learning_minutes, 60) }}h {{ $course->estimated_learning_minutes % 60 }}m</div><div class="stat-sub">Target: {{ $course->target_learning_minutes ? intdiv($course->target_learning_minutes,60).'h '.($course->target_learning_minutes%60).'m' : 'not set' }}</div></div></div>
        <div class="stat-card"><div><div class="stat-label">Content Quality Score</div><div class="stat-value">{{ $course->content_quality_score ?? '—' }}{{ $course->content_quality_score !== null ? '%' : '' }}</div><div class="stat-sub">90% plus all mandatory checks</div></div></div>
    </div>
    @if(is_array($course->content_quality_report))
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><h3>Quality & Traceability Checks</h3></div>
            <div class="card-body" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px 18px;">
                @foreach($course->content_quality_report as $check => $passed)
                    <div style="font-size:13px;color:{{ $passed ? '#166534' : '#991b1b' }};"><strong>{{ $passed ? '✓' : '✕' }}</strong> {{ ucwords(str_replace('_',' ',$check)) }}</div>
                @endforeach
            </div>
        </div>
    @endif
    @endif

    @foreach($course->blueprintModules as $module)
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header">
                <h3>Module {{ $module->module_order }} · {{ $module->title }}</h3>
                <span class="badge badge-blue">{{ $module->knowledgeResources->count() }} source(s)</span>
            </div>
            <div class="card-body">
                @if($module->learning_outcomes)
                    <div style="font-size:13px;color:#374151;margin-bottom:14px;white-space:pre-line;"><strong>Learning outcomes:</strong> {{ $module->learning_outcomes }}</div>
                @endif
                <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px;">
                    @forelse($module->knowledgeResources as $source)
                        <a href="{{ route('knowledge-hub.show', $source) }}" target="_blank" class="badge badge-teal" style="text-decoration:none;">
                            {{ $source->clause_number ? $source->clause_number.' · ' : '' }}{{ $source->title }}
                        </a>
                    @empty
                        <span class="badge badge-danger">Missing source</span>
                    @endforelse
                </div>
                <table class="dt">
                    <thead><tr><th>Lesson</th><th>Objectives</th><th>Permanent Sources</th><th>Est. Time</th></tr></thead>
                    <tbody>
                    @foreach($module->lessons as $lesson)
                        <tr>
                            <td class="td-main">{{ $lesson->title }}</td>
                            <td>{{ $lesson->learning_objectives ?: '—' }}</td>
                            <td>
                                @forelse($lesson->knowledgeResources as $source)
                                    <span class="badge badge-secondary">{{ $source->clause_number ?: $source->title }}</span>
                                @empty
                                    <span class="badge badge-danger">Missing</span>
                                @endforelse
                            </td>
                            <td>{{ $lesson->duration_minutes ?: '—' }} min</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <div class="card">
        <div class="card-body" style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
            <div>
                <strong>{{ $course->blueprint_status === 'awaiting_approval' ? 'Ready to generate?' : 'V2 administration' }}</strong>
                <div style="font-size:12px;color:#6b7280;margin-top:4px;">Source mappings are permanent audit references.</div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
            @if($course->blueprint_status === 'awaiting_approval' && auth()->user()?->isSuperAdmin())
            <form method="POST" action="{{ route('ai.course-generator.blueprint.approve', $course) }}" onsubmit="return confirm('Approve this blueprint and start grounded course generation?')">
                @csrf
                <button class="btn btn-success" type="submit">Approve Blueprint & Generate</button>
            </form>
            @elseif($course->blueprint_status === 'awaiting_approval')
                <span class="badge badge-warning">Awaiting Super Admin approval</span>
            @else
                <a class="btn btn-view" href="{{ route('ai.question-bank.index', ['course_id'=>$course->id]) }}">Review Question Bank</a>
                <a class="btn btn-primary" href="{{ route('elearning.courses.edit', $course) }}">Open Course Editor</a>
            @endif
            </div>
        </div>
    </div>
</div>
@endsection
