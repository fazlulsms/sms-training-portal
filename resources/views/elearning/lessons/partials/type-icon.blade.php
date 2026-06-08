@switch($type ?? '')
    @case('rich_text')       📝 @break
    @case('accordion')       📂 @break
    @case('video')           🎬 @break
    @case('audio')           🎧 @break
    @case('image')           🖼️ @break
    @case('gallery')         🎨 @break
    @case('pdf')             📄 @break
    @case('download')        📥 @break
    @case('slides')          🖥️ @break
    @case('knowledge_check') ❓ @break
    @case('scenario')        🎭 @break
    @case('matching')        🔗 @break
    @default                 📦 @break
@endswitch
