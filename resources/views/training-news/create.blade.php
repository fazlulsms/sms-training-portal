@extends('layouts.app')
@section('page-title', 'Generate Training News — ' . ($data['course_name'] ?? ''))

@section('content')
<style>
.gen-section { background:#fff;border:1px solid #e9ecef;border-radius:14px;padding:24px;margin-bottom:20px; }
.gen-section-title { font-size:14px;font-weight:800;color:#111827;margin:0 0 16px;display:flex;align-items:center;gap:8px; }
.tab-btn { padding:8px 18px;border-radius:8px;border:1.5px solid #e5e7eb;background:#fff;font-size:13px;font-weight:600;color:#6b7280;cursor:pointer;transition:all .15s; }
.tab-btn.active { background:#1e3a8a;color:#fff;border-color:#1e3a8a; }
.tab-panel { display:none; }
.tab-panel.active { display:block; }
.ai-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:9px;border:none;font-size:13px;font-weight:700;cursor:pointer;transition:all .15s; }
.ai-btn-primary { background:linear-gradient(135deg,#6366f1,#7c3aed);color:#fff; }
.ai-btn-primary:hover { opacity:.9; }
.ai-btn-secondary { background:#f5f3ff;color:#5b21b6;border:1.5px solid #ddd6fe; }
.ai-btn-secondary:hover { background:#ede9fe; }
.ai-btn:disabled { opacity:.5;cursor:not-allowed; }
.spin { animation:spin .8s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }
.field-group { margin-bottom:16px; }
.field-group label { display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px; }
.field-group input, .field-group select, .field-group textarea { width:100%;padding:9px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:inherit;color:#111827;background:#fff;box-sizing:border-box; }
.field-group textarea { min-height:120px;resize:vertical; }
.field-group input:focus, .field-group select:focus, .field-group textarea:focus { outline:none;border-color:#6366f1; }
.seo-char { font-size:11px;color:#9ca3af;float:right; }
.social-platform { display:flex;align-items:center;gap:8px;padding:10px 0;border-bottom:1px solid #f0f2f5; }
.social-platform:last-child { border-bottom:none; }
.platform-icon { width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px; }
.data-badge { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;background:#f0f4ff;color:#1e3a8a;font-size:11px;font-weight:600;margin:2px; }
.stat-row { display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px; }
.workflow-step { display:flex;align-items:center;gap:8px;padding:8px 0;font-size:13px; }
.step-dot { width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;flex-shrink:0; }
</style>

<div class="d-flex align-items-center gap-3 mb-4">
    <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#1e3a8a,#6366f1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
    </div>
    <div>
        <h4 class="mb-0 fw-bold">AI News Generator</h4>
        <p class="text-muted mb-0 small">{{ $data['course_name'] }} · {{ $data['start_date'] }} – {{ $data['end_date'] }}</p>
    </div>
    <div class="ms-auto d-flex gap-2">
        <a href="{{ route('training-media.index', $schedule->id) }}" class="btn btn-outline-secondary btn-sm">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            Manage Photos
        </a>
        <a href="{{ route('training-news.index') }}" class="btn btn-ghost btn-sm">← Back</a>
    </div>
</div>

@if($existingArticles->count())
<div class="alert alert-info d-flex align-items-start gap-3 mb-4" style="border-radius:12px;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>
        <strong>{{ $existingArticles->count() }} existing article(s) for this training:</strong>
        <div class="mt-1">
            @foreach($existingArticles as $a)
            <a href="{{ route('training-news.edit', $a->id) }}" class="me-3" style="font-size:13px;">{{ Str::limit($a->title, 60) }} <span class="badge {{ $a->status_badge_class }} ms-1">{{ $a->status_label }}</span></a>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="row g-4">

{{-- LEFT: Training Data & Generation Controls --}}
<div class="col-lg-4">

    {{-- Training Data Summary --}}
    <div class="gen-section">
        <div class="gen-section-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Training Data
        </div>
        <div class="stat-row">
            <span class="data-badge">📅 {{ $data['start_date'] }}</span>
            <span class="data-badge">📍 {{ $data['city'] }}, {{ $data['country'] }}</span>
            <span class="data-badge">🎯 {{ $data['training_mode'] }}</span>
            @if($data['batch_code'])<span class="data-badge">🔢 {{ $data['batch_code'] }}</span>@endif
        </div>
        <div class="stat-row">
            <span class="data-badge" style="background:#dcfce7;color:#166534;">👥 {{ $data['total_participants'] }} Participants</span>
            <span class="data-badge" style="background:#dcfce7;color:#166534;">🏢 {{ $data['organizations'] }} Orgs</span>
            <span class="data-badge" style="background:#dcfce7;color:#166534;">✅ {{ $data['attendance_rate'] }}% Attendance</span>
        </div>
        @if($data['trainer_name'])
        <div style="padding:10px;background:#f8fafc;border-radius:8px;font-size:12px;">
            <strong>Trainer:</strong> {{ $data['trainer_name'] }}<br>
            <span style="color:#6b7280;">{{ $data['trainer_designation'] }}</span>
        </div>
        @endif
    </div>

    {{-- Article Type & Options --}}
    <div class="gen-section">
        <div class="gen-section-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Generation Options
        </div>
        <div class="field-group">
            <label>Article Type</label>
            <select id="articleType" class="fi">
                <option value="training_news">📰 Training News</option>
                <option value="success_story">⭐ Success Story</option>
                <option value="course_announcement">📢 Course Announcement</option>
            </select>
        </div>
        <div class="field-group">
            <label>Special Instructions (optional)</label>
            <textarea id="extraInstructions" placeholder="e.g. Focus on sustainability aspect, mention ISO 14001 context…" style="min-height:70px;"></textarea>
        </div>
        <button class="ai-btn ai-btn-primary w-100" id="generateArticleBtn" onclick="generateArticle()">
            <svg id="genSpinner" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;" class="spin"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
            <svg id="genIcon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            Generate Article with AI
        </button>
    </div>

    {{-- Workflow --}}
    <div class="gen-section">
        <div class="gen-section-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            Publishing Workflow
        </div>
        @foreach([
            ['dot'=>'#9ca3af','label'=>'Draft','desc'=>'Save and continue editing'],
            ['dot'=>'#d97706','label'=>'Submit for Review','desc'=>'Admin submits to Super Admin'],
            ['dot'=>'#3b82f6','label'=>'Approved','desc'=>'Super Admin approves'],
            ['dot'=>'#16a34a','label'=>'Published','desc'=>'Live on public website'],
        ] as $step)
        <div class="workflow-step">
            <div class="step-dot" style="background:{{ $step['dot'] }};color:#fff;">●</div>
            <div>
                <div style="font-weight:700;font-size:12px;">{{ $step['label'] }}</div>
                <div style="font-size:11px;color:#6b7280;">{{ $step['desc'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

</div>

{{-- RIGHT: Article Editor with Tabs --}}
<div class="col-lg-8">

    <div class="gen-section" style="min-height:600px;">

        {{-- Tab Nav --}}
        <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
            <button class="tab-btn active" onclick="switchTab('article',this)">📄 Article</button>
            <button class="tab-btn" onclick="switchTab('seo',this)">🔍 SEO</button>
            <button class="tab-btn" onclick="switchTab('social',this)">📱 Social Media</button>
            <button class="tab-btn" onclick="switchTab('publish',this)">🚀 Save & Publish</button>
        </div>

        {{-- ARTICLE TAB --}}
        <div id="tab-article" class="tab-panel active">
            <div class="field-group">
                <label>Headline / Title *</label>
                <input type="text" id="articleTitle" placeholder="Professional SEO-friendly headline…" />
            </div>
            <div class="field-group">
                <label>Excerpt <span class="seo-char" id="excerptCount">0/300</span></label>
                <textarea id="articleExcerpt" placeholder="Short compelling summary (2-3 sentences)…" style="min-height:80px;" oninput="document.getElementById('excerptCount').textContent=this.value.length+'/300'"></textarea>
            </div>
            <div class="field-group">
                <label>Full Article Content (HTML)</label>
                <textarea id="articleContent" placeholder="AI-generated article will appear here. You can edit freely…" style="min-height:360px;font-family:monospace;font-size:12px;"></textarea>
            </div>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <button class="ai-btn ai-btn-secondary" onclick="generateSeo()" id="genSeoBtn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Auto-Generate SEO
                </button>
                <button class="ai-btn ai-btn-secondary" onclick="generateSocial()" id="genSocialBtn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    Auto-Generate Social
                </button>
                <button class="ai-btn ai-btn-primary" onclick="switchTab('publish',null)">
                    Continue →
                </button>
            </div>
        </div>

        {{-- SEO TAB --}}
        <div id="tab-seo" class="tab-panel">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <span style="font-size:13px;color:#6b7280;">Optimize article for search engines</span>
                <button class="ai-btn ai-btn-secondary" onclick="generateSeo()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                    Regenerate SEO
                </button>
            </div>
            <div class="row g-3">
                <div class="col-12">
                    <div class="field-group">
                        <label>SEO Title <span class="seo-char" id="seoTitleCount">0/65</span></label>
                        <input type="text" id="seoTitle" maxlength="70" oninput="document.getElementById('seoTitleCount').textContent=this.value.length+'/65'" placeholder="55-65 characters · include main keyword" />
                    </div>
                </div>
                <div class="col-12">
                    <div class="field-group">
                        <label>Meta Description <span class="seo-char" id="seoDescCount">0/160</span></label>
                        <textarea id="seoDescription" maxlength="165" style="min-height:80px;" oninput="document.getElementById('seoDescCount').textContent=this.value.length+'/160'" placeholder="150-160 characters · compelling, includes keyword"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field-group">
                        <label>OG Title (Social Share)</label>
                        <input type="text" id="ogTitle" placeholder="Open Graph title for social sharing" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field-group">
                        <label>URL Slug</label>
                        <input type="text" id="articleSlug" placeholder="url-friendly-slug" />
                    </div>
                </div>
                <div class="col-12">
                    <div class="field-group">
                        <label>OG Description</label>
                        <textarea id="ogDescription" style="min-height:70px;" placeholder="Open Graph description for social sharing"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field-group">
                        <label>Focus Keywords</label>
                        <input type="text" id="focusKeywords" placeholder="ISO 9001, Internal Auditor, Dhaka…" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field-group">
                        <label>Tags (comma-separated)</label>
                        <input type="text" id="articleTags" placeholder="ISO 9001, Audit, Training…" />
                    </div>
                </div>
            </div>
        </div>

        {{-- SOCIAL MEDIA TAB --}}
        <div id="tab-social" class="tab-panel">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <span style="font-size:13px;color:#6b7280;">AI-generated social media posts ready for copy & paste</span>
                <button class="ai-btn ai-btn-secondary" onclick="generateSocial()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                    Regenerate All
                </button>
            </div>
            @foreach([
                ['id'=>'linkedin','icon'=>'💼','label'=>'LinkedIn','color'=>'#0077b5','bg'=>'#e7f1f8','hint'=>'150-300 words · professional audience'],
                ['id'=>'facebook','icon'=>'👥','label'=>'Facebook','color'=>'#1877f2','bg'=>'#e7f0fd','hint'=>'100-200 words · marketing focused'],
                ['id'=>'twitter','icon'=>'🐦','label'=>'X (Twitter)','color'=>'#000','bg'=>'#f7f7f7','hint'=>'Max 270 chars · punchy'],
                ['id'=>'instagram','icon'=>'📷','label'=>'Instagram','color'=>'#e1306c','bg'=>'#fce4ec','hint'=>'Caption with hashtags'],
            ] as $p)
            <div class="field-group">
                <label style="display:flex;align-items:center;gap:6px;">
                    <span style="background:{{ $p['bg'] }};color:{{ $p['color'] }};padding:3px 8px;border-radius:6px;font-size:13px;">{{ $p['icon'] }} {{ $p['label'] }}</span>
                    <span style="font-weight:400;color:#9ca3af;font-size:11px;">{{ $p['hint'] }}</span>
                </label>
                <textarea id="social{{ ucfirst($p['id']) }}" style="min-height:{{ $p['id']==='twitter'?60:110 }}px;" placeholder="{{ $p['label'] }} post will appear here…"></textarea>
            </div>
            @endforeach
            <div class="field-group">
                <label>Hashtags</label>
                <input type="text" id="articleHashtags" placeholder="#Training #ISO9001 #ProfessionalDevelopment…" />
            </div>
        </div>

        {{-- SAVE & PUBLISH TAB --}}
        <div id="tab-publish" class="tab-panel">
            <form method="POST" action="{{ route('training-news.store', $schedule->id) }}" enctype="multipart/form-data" id="saveForm">
                @csrf
                <input type="hidden" name="ai_generated" value="1" id="aiGeneratedFlag">
                <input type="hidden" name="title"             id="h_title">
                <input type="hidden" name="excerpt"           id="h_excerpt">
                <input type="hidden" name="content"           id="h_content">
                <input type="hidden" name="slug"              id="h_slug">
                <input type="hidden" name="seo_title"         id="h_seo_title">
                <input type="hidden" name="seo_description"   id="h_seo_description">
                <input type="hidden" name="og_title"          id="h_og_title">
                <input type="hidden" name="og_description"    id="h_og_description">
                <input type="hidden" name="focus_keywords"    id="h_focus_keywords">
                <input type="hidden" name="tags"              id="h_tags">
                <input type="hidden" name="social_linkedin"   id="h_linkedin">
                <input type="hidden" name="social_facebook"   id="h_facebook">
                <input type="hidden" name="social_twitter"    id="h_twitter">
                <input type="hidden" name="social_instagram"  id="h_instagram">
                <input type="hidden" name="hashtags"          id="h_hashtags">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="field-group">
                            <label>Article Type *</label>
                            <select name="article_type" id="h_article_type_sel">
                                <option value="training_news">Training News</option>
                                <option value="success_story">Success Story</option>
                                <option value="course_announcement">Course Announcement</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="field-group">
                            <label>Category</label>
                            <select name="blog_category_id">
                                <option value="">Auto (Training News)</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="field-group">
                            <label>Featured Image (optional — uses training cover photo if not set)</label>
                            <input type="file" name="featured_image" accept="image/*" class="fi">
                        </div>
                    </div>
                </div>

                {{-- Preview card --}}
                <div style="background:#f8fafc;border-radius:12px;padding:16px;margin:16px 0;" id="previewCard">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-bottom:10px;">Article Preview</div>
                    <div style="font-size:16px;font-weight:800;color:#111827;margin-bottom:6px;" id="previewTitle">—</div>
                    <div style="font-size:13px;color:#6b7280;line-height:1.5;" id="previewExcerpt">—</div>
                    <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;" id="previewMeta"></div>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <button type="submit" class="ai-btn ai-btn-primary" onclick="populateHiddenFields()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Save as Draft
                    </button>
                    <a href="{{ route('training-media.index', $schedule->id) }}" class="ai-btn ai-btn-secondary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        Add Photos First
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>
</div>

<script>
var GENERATE_URL  = "{{ route('training-news.generate-article', $schedule->id) }}";
var GENERATE_SEO  = "{{ route('training-news.generate-seo') }}";
var GENERATE_SOC  = "{{ route('training-news.generate-social') }}";
var CSRF          = "{{ csrf_token() }}";
var SCHEDULE_DATA = @json($data);

function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    if (btn) btn.classList.add('active');
    else {
        document.querySelectorAll('.tab-btn').forEach(b => {
            if (b.textContent.toLowerCase().includes('save')) b.classList.add('active');
        });
    }
    if (name === 'publish') updatePreview();
}

function setLoading(btnId, loading) {
    var btn = document.getElementById(btnId);
    if (!btn) return;
    btn.disabled = loading;
    var spinner = btn.querySelector('.spin') || document.getElementById('genSpinner');
    var icon    = document.getElementById('genIcon');
    if (btnId === 'generateArticleBtn') {
        document.getElementById('genSpinner').style.display = loading ? '' : 'none';
        document.getElementById('genIcon').style.display    = loading ? 'none' : '';
    }
}

function generateArticle() {
    var title = document.getElementById('articleTitle').value;
    if (title) {
        if (!confirm('This will replace the current title and content. Continue?')) return;
    }
    setLoading('generateArticleBtn', true);
    fetch(GENERATE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({
            article_type: document.getElementById('articleType').value,
            instructions: document.getElementById('extraInstructions').value,
        })
    })
    .then(r => r.json())
    .then(res => {
        setLoading('generateArticleBtn', false);
        if (!res.success) { alert('Error: ' + (res.error || 'Unknown error')); return; }
        document.getElementById('articleTitle').value   = res.data.title;
        document.getElementById('articleExcerpt').value = res.data.excerpt;
        document.getElementById('articleContent').value  = res.data.content;
        document.getElementById('articleSlug').value     = res.data.suggested_slug;
        document.getElementById('excerptCount').textContent = res.data.excerpt.length + '/300';
        switchTab('article', null);
        document.querySelectorAll('.tab-btn')[0].classList.add('active');
        // Auto-trigger SEO generation
        setTimeout(() => generateSeo(), 500);
    })
    .catch(() => { setLoading('generateArticleBtn', false); alert('Network error. Try again.'); });
}

function generateSeo() {
    var title   = document.getElementById('articleTitle').value;
    var content = document.getElementById('articleContent').value;
    if (!title) { alert('Generate or write an article first.'); return; }
    fetch(GENERATE_SEO, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ title, content, schedule_data: SCHEDULE_DATA })
    })
    .then(r => r.json())
    .then(res => {
        if (!res.success || !res.data) return;
        var d = res.data;
        if (d.seo_title)       { document.getElementById('seoTitle').value = d.seo_title; document.getElementById('seoTitleCount').textContent = d.seo_title.length + '/65'; }
        if (d.seo_description) { document.getElementById('seoDescription').value = d.seo_description; document.getElementById('seoDescCount').textContent = d.seo_description.length + '/160'; }
        if (d.og_title)        document.getElementById('ogTitle').value        = d.og_title;
        if (d.og_description)  document.getElementById('ogDescription').value  = d.og_description;
        if (d.focus_keywords)  document.getElementById('focusKeywords').value  = d.focus_keywords;
        if (d.slug)            document.getElementById('articleSlug').value    = d.slug;
        if (d.tags) {
            var tags = Array.isArray(d.tags) ? d.tags.join(', ') : d.tags;
            document.getElementById('articleTags').value = tags;
        }
    })
    .catch(() => {});
}

function generateSocial() {
    var title   = document.getElementById('articleTitle').value;
    var excerpt = document.getElementById('articleExcerpt').value;
    if (!title) { alert('Generate or write an article first.'); return; }
    fetch(GENERATE_SOC, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ title, excerpt, schedule_data: SCHEDULE_DATA })
    })
    .then(r => r.json())
    .then(res => {
        if (!res.success || !res.data) return;
        var d = res.data;
        if (d.linkedin)  document.getElementById('socialLinkedin').value  = d.linkedin;
        if (d.facebook)  document.getElementById('socialFacebook').value  = d.facebook;
        if (d.twitter)   document.getElementById('socialTwitter').value   = d.twitter;
        if (d.instagram) document.getElementById('socialInstagram').value = d.instagram;
        if (d.hashtags)  document.getElementById('articleHashtags').value = d.hashtags;
    })
    .catch(() => {});
}

function updatePreview() {
    var title   = document.getElementById('articleTitle').value;
    var excerpt = document.getElementById('articleExcerpt').value;
    document.getElementById('previewTitle').textContent   = title   || '(No title yet)';
    document.getElementById('previewExcerpt').textContent = excerpt || '(No excerpt yet)';
    var meta = document.getElementById('previewMeta');
    meta.innerHTML = '';
    if (document.getElementById('seoTitle').value) {
        meta.innerHTML += '<span class="data-badge">🔍 SEO ready</span>';
    }
    if (document.getElementById('socialLinkedin').value) {
        meta.innerHTML += '<span class="data-badge" style="background:#e7f1f8;color:#0077b5;">💼 LinkedIn ready</span>';
    }
    if (document.getElementById('socialFacebook').value) {
        meta.innerHTML += '<span class="data-badge" style="background:#e7f0fd;color:#1877f2;">👥 Facebook ready</span>';
    }
}

function populateHiddenFields() {
    document.getElementById('h_title').value          = document.getElementById('articleTitle').value;
    document.getElementById('h_excerpt').value        = document.getElementById('articleExcerpt').value;
    document.getElementById('h_content').value        = document.getElementById('articleContent').value;
    document.getElementById('h_slug').value           = document.getElementById('articleSlug').value;
    document.getElementById('h_seo_title').value      = document.getElementById('seoTitle').value;
    document.getElementById('h_seo_description').value = document.getElementById('seoDescription').value;
    document.getElementById('h_og_title').value       = document.getElementById('ogTitle').value;
    document.getElementById('h_og_description').value = document.getElementById('ogDescription').value;
    document.getElementById('h_focus_keywords').value = document.getElementById('focusKeywords').value;
    document.getElementById('h_linkedin').value       = document.getElementById('socialLinkedin').value;
    document.getElementById('h_facebook').value       = document.getElementById('socialFacebook').value;
    document.getElementById('h_twitter').value        = document.getElementById('socialTwitter').value;
    document.getElementById('h_instagram').value      = document.getElementById('socialInstagram').value;
    document.getElementById('h_hashtags').value       = document.getElementById('articleHashtags').value;
    // Tags: store as JSON array
    var tagsRaw = document.getElementById('articleTags').value;
    var tagsArr = tagsRaw ? tagsRaw.split(',').map(t => t.trim()).filter(Boolean) : [];
    document.getElementById('h_tags').value = JSON.stringify(tagsArr);
    // Sync article type
    document.getElementById('h_article_type_sel').value = document.getElementById('articleType').value;
}
</script>
@endsection
