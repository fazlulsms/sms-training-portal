{{--
    Block type-specific form fields.
    Variables: $type (string), $block (LessonBlock|null for new)
    CSS classes fi, fl, fg2, rep-row, btn-rmv, btn-addrow, kc-row, kc-chk are defined in the parent view.
--}}

@switch($type)

{{-- ══════════════════════════════════════════════════════
     RICH TEXT
══════════════════════════════════════════════════════ --}}
@case('rich_text')
    <label class="fl">Content <span style="color:#dc2626;">*</span></label>
    <textarea name="content" class="fi" rows="8"
              placeholder="Enter lesson content. HTML is supported.">{{ old('content', $block?->content) }}</textarea>
    <div style="font-size:11px; color:#9ca3af; margin-top:4px;">
        Tip: You can use basic HTML tags: &lt;b&gt;, &lt;i&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;, &lt;h3&gt;, &lt;a href=""&gt;, &lt;br&gt;
    </div>
    @break

{{-- ══════════════════════════════════════════════════════
     VIDEO
══════════════════════════════════════════════════════ --}}
@case('video')
    <label class="fl">Video URL <span style="color:#dc2626;">*</span></label>
    <input type="text" name="content" class="fi"
           value="{{ old('content', $block?->content) }}"
           placeholder="https://www.youtube.com/watch?v=... or Vimeo, Drive, direct MP4">
    <div style="font-size:11px; color:#9ca3af; margin-top:4px;">
        Supports: YouTube, Vimeo, Google Drive share links, direct video URLs
    </div>
    @break

{{-- ══════════════════════════════════════════════════════
     AUDIO
══════════════════════════════════════════════════════ --}}
@case('audio')
    <label class="fl">Audio URL <span style="color:#dc2626;">*</span></label>
    <input type="text" name="content" class="fi"
           value="{{ old('content', $block?->content) }}"
           placeholder="https://example.com/narration.mp3 or Google Drive audio link">
    <div style="font-size:11px; color:#9ca3af; margin-top:4px;">Direct MP3/WAV URL or Google Drive shareable link</div>
    @break

{{-- ══════════════════════════════════════════════════════
     IMAGE
══════════════════════════════════════════════════════ --}}
@case('image')
    <label class="fl">Image URL <span style="color:#dc2626;">*</span></label>
    <input type="text" name="content" class="fi"
           value="{{ old('content', $block?->content) }}"
           placeholder="https://example.com/image.jpg">
    <label class="fl">Caption <span style="color:#9ca3af;font-weight:400;">(optional)</span></label>
    <input type="text" name="caption" class="fi"
           value="{{ old('caption', $block?->settings_json['caption'] ?? '') }}"
           placeholder="Image description">
    @break

{{-- ══════════════════════════════════════════════════════
     IMAGE GALLERY
══════════════════════════════════════════════════════ --}}
@case('gallery')
    @php
        $galleryItems = [];
        if ($block) {
            $decoded = $block->getDecodedContent();
            $galleryItems = is_array($decoded) ? $decoded : [];
        }
        if (empty($galleryItems)) {
            $galleryItems = [['url'=>'','caption'=>''], ['url'=>'','caption'=>'']];
        }
    @endphp
    <div style="margin-bottom:8px; font-size:12px; font-weight:700; color:#374151; margin-top:13px;">
        Gallery Images
        <span style="color:#9ca3af; font-weight:400; margin-left:4px;">Add at least 2 images</span>
    </div>
    <div id="gallery-rows">
        @foreach($galleryItems as $gi)
            <div class="rep-row">
                <input type="text" name="gallery_url[]" class="fi"
                       value="{{ $gi['url'] ?? '' }}"
                       placeholder="Image URL">
                <input type="text" name="gallery_caption[]" class="fi"
                       value="{{ $gi['caption'] ?? '' }}"
                       placeholder="Caption (optional)" style="max-width:180px;">
                <button type="button" class="btn-rmv" onclick="removeRow(this)">✕</button>
            </div>
        @endforeach
    </div>
    <button type="button" class="btn-addrow" onclick="addGalleryRow()">+ Add Image</button>
    <script>
    function addGalleryRow() {
        const c = document.getElementById('gallery-rows');
        const r = document.createElement('div');
        r.className = 'rep-row';
        r.innerHTML = '<input type="text" name="gallery_url[]" class="fi" placeholder="Image URL">'
                    + '<input type="text" name="gallery_caption[]" class="fi" placeholder="Caption" style="max-width:180px;">'
                    + '<button type="button" class="btn-rmv" onclick="removeRow(this)">✕</button>';
        c.appendChild(r);
    }
    </script>
    @break

{{-- ══════════════════════════════════════════════════════
     PDF
══════════════════════════════════════════════════════ --}}
@case('pdf')
    <label class="fl">PDF URL <span style="color:#dc2626;">*</span></label>
    <input type="text" name="content" class="fi"
           value="{{ old('content', $block?->content) }}"
           placeholder="https://example.com/document.pdf or Google Drive PDF link">
    <label class="fl">Allow Download</label>
    <select name="allow_download" class="fi">
        <option value="1" {{ ($block?->settings_json['allow_download'] ?? true) ? 'selected' : '' }}>Yes — Show download button</option>
        <option value="0" {{ !($block?->settings_json['allow_download'] ?? true) ? 'selected' : '' }}>No — View only</option>
    </select>
    @break

{{-- ══════════════════════════════════════════════════════
     DOWNLOADABLE RESOURCES
══════════════════════════════════════════════════════ --}}
@case('download')
    @php
        $dlItems = [];
        if ($block) {
            $decoded = $block->getDecodedContent();
            $dlItems = is_array($decoded) ? $decoded : [];
        }
        if (empty($dlItems)) $dlItems = [['title'=>'','url'=>'','type'=>'pdf']];
    @endphp
    <div style="font-size:12px; font-weight:700; color:#374151; margin:13px 0 8px;">
        Files / Resources
    </div>
    <div id="dl-rows">
        @foreach($dlItems as $dl)
            <div class="rep-row" style="align-items:center;">
                <input type="text" name="dl_title[]" class="fi"
                       value="{{ $dl['title'] ?? '' }}" placeholder="File title">
                <input type="text" name="dl_url[]" class="fi"
                       value="{{ $dl['url'] ?? '' }}" placeholder="URL / link">
                <select name="dl_type[]" class="fi" style="max-width:90px;">
                    @foreach(['pdf','docx','xlsx','pptx','zip','link'] as $ft)
                        <option value="{{ $ft }}" {{ ($dl['type'] ?? 'pdf') === $ft ? 'selected' : '' }}>
                            {{ strtoupper($ft) }}
                        </option>
                    @endforeach
                </select>
                <button type="button" class="btn-rmv" onclick="removeRow(this)">✕</button>
            </div>
        @endforeach
    </div>
    <button type="button" class="btn-addrow" onclick="addDlRow()">+ Add File</button>
    <script>
    function addDlRow() {
        const c = document.getElementById('dl-rows');
        const r = document.createElement('div');
        r.className = 'rep-row';
        r.style.alignItems = 'center';
        r.innerHTML = '<input type="text" name="dl_title[]" class="fi" placeholder="File title">'
                    + '<input type="text" name="dl_url[]" class="fi" placeholder="URL">'
                    + '<select name="dl_type[]" class="fi" style="max-width:90px;">'
                    + ['pdf','docx','xlsx','pptx','zip','link'].map(t=>`<option value="${t}">${t.toUpperCase()}</option>`).join('')
                    + '</select>'
                    + '<button type="button" class="btn-rmv" onclick="removeRow(this)">✕</button>';
        c.appendChild(r);
    }
    </script>
    @break

{{-- ══════════════════════════════════════════════════════
     ACCORDION / FAQ
══════════════════════════════════════════════════════ --}}
@case('accordion')
    @php
        $acItems = [];
        if ($block) {
            $decoded = $block->getDecodedContent();
            $acItems = is_array($decoded) ? $decoded : [];
        }
        if (empty($acItems)) {
            $acItems = [['title'=>'','body'=>''], ['title'=>'','body'=>'']];
        }
    @endphp
    <div style="font-size:12px; font-weight:700; color:#374151; margin:13px 0 8px;">
        Accordion Sections
    </div>
    <div id="ac-rows">
        @foreach($acItems as $ac)
            <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:9px; padding:12px; margin-bottom:8px;">
                <input type="text" name="accordion_title[]" class="fi"
                       value="{{ $ac['title'] ?? '' }}"
                       placeholder="Section title (e.g. Integrity)" style="margin-bottom:8px;">
                <textarea name="accordion_content[]" class="fi" rows="3"
                          placeholder="Section content (can include HTML)">{{ $ac['body'] ?? '' }}</textarea>
                <button type="button" class="btn-rmv" onclick="this.parentElement.remove()" style="margin-top:6px; width:100%;">✕ Remove Section</button>
            </div>
        @endforeach
    </div>
    <button type="button" class="btn-addrow" onclick="addAcRow()">+ Add Section</button>
    <script>
    function addAcRow() {
        const c = document.getElementById('ac-rows');
        const d = document.createElement('div');
        d.style.cssText = 'background:#f8fafc;border:1px solid #e5e7eb;border-radius:9px;padding:12px;margin-bottom:8px;';
        d.innerHTML = '<input type="text" name="accordion_title[]" class="fi" placeholder="Section title" style="margin-bottom:8px;">'
                    + '<textarea name="accordion_content[]" class="fi" rows="3" placeholder="Section content"></textarea>'
                    + '<button type="button" class="btn-rmv" onclick="this.parentElement.remove()" style="margin-top:6px;width:100%;">✕ Remove Section</button>';
        c.appendChild(d);
    }
    </script>
    @break

{{-- ══════════════════════════════════════════════════════
     SLIDE PRESENTATION
══════════════════════════════════════════════════════ --}}
@case('slides')
    @php
        $slideItems = [];
        if ($block) {
            $decoded = $block->getDecodedContent();
            $slideItems = is_array($decoded) ? $decoded : [];
        }
        if (empty($slideItems)) {
            $slideItems = [
                ['title'=>'','text'=>'','image_url'=>''],
                ['title'=>'','text'=>'','image_url'=>''],
            ];
        }
    @endphp
    <div style="font-size:12px; font-weight:700; color:#374151; margin:13px 0 8px;">
        Slides <span style="font-weight:400; color:#9ca3af;">— Add at least 2 slides</span>
    </div>
    <div id="slide-rows">
        @foreach($slideItems as $si => $slide)
            <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:9px; padding:13px; margin-bottom:8px;">
                <div style="font-size:11px; font-weight:800; color:#6b7280; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Slide {{ $si + 1 }}</div>
                <input type="text" name="slide_title[]" class="fi"
                       value="{{ $slide['title'] ?? '' }}"
                       placeholder="Slide title" style="margin-bottom:7px;">
                <textarea name="slide_text[]" class="fi" rows="3"
                          placeholder="Slide body text (HTML ok)" style="margin-bottom:7px;">{{ $slide['text'] ?? '' }}</textarea>
                <input type="text" name="slide_image_url[]" class="fi"
                       value="{{ $slide['image_url'] ?? '' }}"
                       placeholder="Optional image URL for this slide">
                <button type="button" class="btn-rmv" onclick="this.parentElement.remove()" style="margin-top:8px; width:100%;">✕ Remove Slide</button>
            </div>
        @endforeach
    </div>
    <button type="button" class="btn-addrow" onclick="addSlideRow()">+ Add Slide</button>
    <script>
    let slideCount = {{ count($slideItems) }};
    function addSlideRow() {
        slideCount++;
        const c = document.getElementById('slide-rows');
        const d = document.createElement('div');
        d.style.cssText = 'background:#f8fafc;border:1px solid #e5e7eb;border-radius:9px;padding:13px;margin-bottom:8px;';
        d.innerHTML = `<div style="font-size:11px;font-weight:800;color:#6b7280;margin-bottom:8px;text-transform:uppercase;letter-spacing:.4px;">Slide ${slideCount}</div>`
                    + '<input type="text" name="slide_title[]" class="fi" placeholder="Slide title" style="margin-bottom:7px;">'
                    + '<textarea name="slide_text[]" class="fi" rows="3" placeholder="Slide body text" style="margin-bottom:7px;"></textarea>'
                    + '<input type="text" name="slide_image_url[]" class="fi" placeholder="Optional image URL">'
                    + '<button type="button" class="btn-rmv" onclick="this.parentElement.remove()" style="margin-top:8px;width:100%;">✕ Remove Slide</button>';
        c.appendChild(d);
    }
    </script>
    @break

{{-- ══════════════════════════════════════════════════════
     KNOWLEDGE CHECK
══════════════════════════════════════════════════════ --}}
@case('knowledge_check')
    @php
        $kc = ['question'=>'','type'=>'single','options'=>[],'explanation'=>''];
        if ($block) {
            $decoded = $block->getDecodedContent();
            if (is_array($decoded)) $kc = array_merge($kc, $decoded);
        }
        if (empty($kc['options'])) {
            $kc['options'] = [
                ['text'=>'','correct'=>false],
                ['text'=>'','correct'=>false],
                ['text'=>'','correct'=>false],
                ['text'=>'','correct'=>false],
            ];
        }
    @endphp

    <label class="fl">Question <span style="color:#dc2626;">*</span></label>
    <textarea name="kc_question" class="fi" rows="2"
              placeholder="e.g. Which of the following best describes Integrity?">{{ old('kc_question', $kc['question']) }}</textarea>

    <label class="fl">Question Type</label>
    <select name="kc_type" class="fi">
        <option value="single"   {{ ($kc['type'] ?? 'single') === 'single'   ? 'selected' : '' }}>Single Choice (one correct answer)</option>
        <option value="multiple" {{ ($kc['type'] ?? 'single') === 'multiple' ? 'selected' : '' }}>Multiple Choice (several correct)</option>
        <option value="truefalse"{{ ($kc['type'] ?? 'single') === 'truefalse'? 'selected' : '' }}>True / False</option>
    </select>

    <div style="font-size:12px; font-weight:700; color:#374151; margin:12px 0 6px;">
        Answer Options
        <span style="font-weight:400; color:#9ca3af;">— tick ✓ to mark correct answer(s)</span>
    </div>
    <div id="kc-opts">
        @foreach($kc['options'] as $oi => $opt)
            <div class="kc-row">
                <span class="kc-hint">{{ chr(65 + $oi) }}.</span>
                <input type="text" name="kc_option_text[]" class="fi"
                       value="{{ $opt['text'] ?? '' }}" placeholder="Option text">
                <input type="checkbox" name="kc_correct[]"
                       value="{{ $oi }}" class="kc-chk"
                       {{ ($opt['correct'] ?? false) ? 'checked' : '' }}
                       title="Mark as correct">
                <button type="button" class="btn-rmv" onclick="removeKcOpt(this)">✕</button>
            </div>
        @endforeach
    </div>
    <button type="button" class="btn-addrow" onclick="addKcOpt()" style="margin-bottom:10px;">+ Add Option</button>

    <label class="fl">Explanation <span style="color:#9ca3af;font-weight:400;">(shown after answer)</span></label>
    <textarea name="kc_explanation" class="fi" rows="2"
              placeholder="Explain the correct answer…">{{ old('kc_explanation', $kc['explanation']) }}</textarea>
    <script>
    let kcCount = {{ count($kc['options']) }};
    function addKcOpt() {
        const c = document.getElementById('kc-opts');
        const r = document.createElement('div');
        r.className = 'kc-row';
        const letter = String.fromCharCode(65 + kcCount);
        r.innerHTML = `<span class="kc-hint">${letter}.</span>`
                    + '<input type="text" name="kc_option_text[]" class="fi" placeholder="Option text">'
                    + `<input type="checkbox" name="kc_correct[]" value="${kcCount}" class="kc-chk" title="Mark as correct">`
                    + '<button type="button" class="btn-rmv" onclick="removeKcOpt(this)">✕</button>';
        c.appendChild(r);
        kcCount++;
    }
    function removeKcOpt(btn) {
        if (document.getElementById('kc-opts').children.length > 2) {
            btn.parentElement.remove();
        }
    }
    </script>
    @break

{{-- ══════════════════════════════════════════════════════
     SCENARIO EXERCISE
══════════════════════════════════════════════════════ --}}
@case('scenario')
    @php
        $sc = ['text'=>'','options'=>[]];
        if ($block) {
            $decoded = $block->getDecodedContent();
            if (is_array($decoded)) $sc = array_merge($sc, $decoded);
        }
        if (empty($sc['options'])) {
            $sc['options'] = [
                ['text'=>'','explanation'=>'','correct'=>false],
                ['text'=>'','explanation'=>'','correct'=>false],
            ];
        }
    @endphp

    <label class="fl">Scenario Text <span style="color:#dc2626;">*</span></label>
    <textarea name="sc_text" class="fi" rows="4"
              placeholder="Describe the situation the learner must respond to…">{{ old('sc_text', $sc['text']) }}</textarea>

    <div style="font-size:12px; font-weight:700; color:#374151; margin:12px 0 6px;">
        Response Options <span style="font-weight:400; color:#9ca3af;">— tick ✓ for the best/correct response</span>
    </div>
    <div id="sc-opts">
        @foreach($sc['options'] as $soi => $sopt)
            <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:9px; padding:12px; margin-bottom:8px;">
                <div style="display:flex; gap:8px; align-items:center; margin-bottom:7px;">
                    <input type="text" name="sc_option_text[]" class="fi"
                           value="{{ $sopt['text'] ?? '' }}"
                           placeholder="Response option text">
                    <input type="checkbox" name="sc_correct[]"
                           value="{{ $soi }}" class="kc-chk"
                           {{ ($sopt['correct'] ?? false) ? 'checked' : '' }}
                           title="Best / correct response">
                    <span style="font-size:11px; color:#9ca3af; flex-shrink:0;">Best?</span>
                </div>
                <textarea name="sc_option_explanation[]" class="fi" rows="2"
                          placeholder="Explanation shown after participant selects this option">{{ $sopt['explanation'] ?? '' }}</textarea>
                <button type="button" class="btn-rmv" onclick="this.parentElement.remove()" style="margin-top:6px; width:100%;">✕ Remove Option</button>
            </div>
        @endforeach
    </div>
    <button type="button" class="btn-addrow" onclick="addScOpt()">+ Add Option</button>
    <script>
    let scCount = {{ count($sc['options']) }};
    function addScOpt() {
        const c = document.getElementById('sc-opts');
        const d = document.createElement('div');
        d.style.cssText = 'background:#f8fafc;border:1px solid #e5e7eb;border-radius:9px;padding:12px;margin-bottom:8px;';
        d.innerHTML = '<div style="display:flex;gap:8px;align-items:center;margin-bottom:7px;">'
                    + '<input type="text" name="sc_option_text[]" class="fi" placeholder="Response option text">'
                    + `<input type="checkbox" name="sc_correct[]" value="${scCount}" class="kc-chk" title="Best response">`
                    + '<span style="font-size:11px;color:#9ca3af;flex-shrink:0;">Best?</span>'
                    + '</div>'
                    + '<textarea name="sc_option_explanation[]" class="fi" rows="2" placeholder="Explanation"></textarea>'
                    + '<button type="button" class="btn-rmv" onclick="this.parentElement.remove()" style="margin-top:6px;width:100%;">✕ Remove Option</button>';
        c.appendChild(d);
        scCount++;
    }
    </script>
    @break

{{-- ══════════════════════════════════════════════════════
     MATCHING ACTIVITY
══════════════════════════════════════════════════════ --}}
@case('matching')
    @php
        $matchData = ['pairs'=>[]];
        if ($block) {
            $decoded = $block->getDecodedContent();
            if (is_array($decoded)) $matchData = array_merge($matchData, $decoded);
        }
        if (empty($matchData['pairs'])) {
            $matchData['pairs'] = [
                ['left'=>'','right'=>''],
                ['left'=>'','right'=>''],
                ['left'=>'','right'=>''],
            ];
        }
    @endphp

    <div style="font-size:12px; font-weight:700; color:#374151; margin:13px 0 8px;">
        Matching Pairs <span style="font-weight:400; color:#9ca3af;">— Left term = Right definition</span>
    </div>
    <div id="match-rows">
        @foreach($matchData['pairs'] as $mi => $pair)
            <div class="rep-row" style="align-items:center;">
                <input type="text" name="match_left[]" class="fi"
                       value="{{ $pair['left'] ?? '' }}"
                       placeholder="Term (e.g. Integrity)">
                <span style="font-size:16px; flex-shrink:0; color:#9ca3af; font-weight:700; padding:0 4px;">=</span>
                <input type="text" name="match_right[]" class="fi"
                       value="{{ $pair['right'] ?? '' }}"
                       placeholder="Definition (e.g. Honest conduct)">
                <button type="button" class="btn-rmv" onclick="removeRow(this)">✕</button>
            </div>
        @endforeach
    </div>
    <button type="button" class="btn-addrow" onclick="addMatchRow()">+ Add Pair</button>
    <script>
    function addMatchRow() {
        const c = document.getElementById('match-rows');
        const r = document.createElement('div');
        r.className = 'rep-row';
        r.style.alignItems = 'center';
        r.innerHTML = '<input type="text" name="match_left[]" class="fi" placeholder="Term">'
                    + '<span style="font-size:16px;flex-shrink:0;color:#9ca3af;font-weight:700;padding:0 4px;">=</span>'
                    + '<input type="text" name="match_right[]" class="fi" placeholder="Definition">'
                    + '<button type="button" class="btn-rmv" onclick="removeRow(this)">✕</button>';
        c.appendChild(r);
    }
    </script>
    @break

@endswitch

{{-- Shared helper: remove a .rep-row --}}
<script>
if (typeof removeRow === 'undefined') {
    function removeRow(btn) { btn.closest('.rep-row').remove(); }
}
</script>
