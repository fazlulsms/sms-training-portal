@php $v = fn($f) => old($f, $record?->$f ?? ''); @endphp

<div style="margin-bottom:18px;">
    <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:6px;">Name <span style="color:#dc2626;">*</span></label>
    <input name="name" value="{{ $v('name') }}" required
           style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; box-sizing:border-box;"
           placeholder="e.g. Internal Auditors">
</div>

<div style="margin-bottom:18px;">
    <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:6px;">Description</label>
    <textarea name="description" rows="3"
              style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; box-sizing:border-box; resize:vertical;">{{ $v('description') }}</textarea>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
    <div>
        <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:6px;">Display Order</label>
        <input name="display_order" type="number" value="{{ $v('display_order') ?: 0 }}"
               style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; box-sizing:border-box;">
    </div>
    <div>
        <label style="display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:6px;">Status</label>
        <select name="status" style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; box-sizing:border-box; background:#fff;">
            <option value="active"   {{ $v('status') !== 'archived' ? 'selected' : '' }}>Active</option>
            <option value="archived" {{ $v('status') === 'archived'  ? 'selected' : '' }}>Archived</option>
        </select>
    </div>
</div>
