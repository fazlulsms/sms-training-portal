{{-- Coupon / Promo Code Section --}}
{{-- Required variables: $courseId (int), $formType ('elearning'|'ilt'), $originalAmount (float) --}}
<div class="reg-card" id="couponSection">
    <div class="reg-card-title">
        <div class="reg-card-num">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
        </div>
        Promo / Coupon Code <span style="font-size:12px;font-weight:600;color:#9ca3af;">(optional)</span>
    </div>

    <div style="display:flex;gap:10px;align-items:flex-start;flex-wrap:wrap;">
        <div style="flex:1;min-width:200px;">
            <input type="text" id="couponCodeInput" name="coupon_code"
                   value="{{ old('coupon_code') }}"
                   class="fi {{ $errors->has('coupon_code') ? 'is-err' : '' }}"
                   placeholder="Enter coupon code"
                   style="text-transform:uppercase;font-family:monospace;font-weight:700;letter-spacing:.5px;">
            @error('coupon_code')<div class="fe" style="margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        <button type="button" id="applyCouponBtn"
                onclick="applyCoupon()"
                style="padding:10px 20px;background:#1e3a8a;color:#fff;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;font-family:inherit;">
            Apply Code
        </button>
    </div>

    <div id="couponFeedback" style="margin-top:12px;display:none;"></div>

    {{-- Hidden inputs used by server to re-validate --}}
    <input type="hidden" id="couponCourseId" value="{{ $courseId }}">
    <input type="hidden" id="couponFormType" value="{{ $formType }}">
    <input type="hidden" id="couponOriginalAmount" value="{{ $originalAmount }}">
    <input type="hidden" id="emailForCoupon" value="">
</div>

<script>
function applyCoupon() {
    var code   = document.getElementById('couponCodeInput').value.trim();
    var email  = document.querySelector('input[name="email"], input[name="participant_name"]');
    var emailVal = document.querySelector('input[name="email"]')?.value?.trim() || '';
    var courseId = document.getElementById('couponCourseId').value;
    var formType = document.getElementById('couponFormType').value;
    var amount   = parseFloat(document.getElementById('couponOriginalAmount').value) || 0;
    var fb       = document.getElementById('couponFeedback');
    var btn      = document.getElementById('applyCouponBtn');

    if (!code) { showCouponMsg('error', 'Please enter a coupon code.'); return; }

    btn.textContent = 'Checking…';
    btn.disabled    = true;

    fetch('{{ route("coupon.validate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({ code: code, email: emailVal, course_id: courseId, type: formType, amount: amount })
    })
    .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
    .then(function(res) {
        btn.textContent = 'Apply Code';
        btn.disabled    = false;
        if (res.ok && res.data.valid) {
            var d = res.data;
            var msgHtml = d.is_complimentary
                ? '<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 16px;">'
                    + '<div style="font-size:14px;font-weight:800;color:#166534;margin-bottom:8px;">🎁 ' + d.message + '</div>'
                    + '<div style="font-size:13px;color:#166534;">No payment required — your enrollment will be confirmed immediately upon submission.</div>'
                    + '</div>'
                : '<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 16px;">'
                    + '<div style="font-size:14px;font-weight:800;color:#166534;margin-bottom:10px;">✅ ' + d.message + '</div>'
                    + '<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">'
                    + '<div style="text-align:center;background:#fff;border-radius:8px;padding:10px;">'
                    +   '<div style="font-size:11px;color:#9ca3af;font-weight:700;margin-bottom:3px;">ORIGINAL</div>'
                    +   '<div style="font-size:16px;font-weight:900;color:#374151;">BDT ' + Number(d.original_amount || amount).toLocaleString() + '</div>'
                    + '</div>'
                    + '<div style="text-align:center;background:#fef3c7;border-radius:8px;padding:10px;">'
                    +   '<div style="font-size:11px;color:#b45309;font-weight:700;margin-bottom:3px;">DISCOUNT</div>'
                    +   '<div style="font-size:16px;font-weight:900;color:#b45309;">- BDT ' + Number(d.discount_amount).toLocaleString() + '</div>'
                    + '</div>'
                    + '<div style="text-align:center;background:#dcfce7;border-radius:8px;padding:10px;">'
                    +   '<div style="font-size:11px;color:#166534;font-weight:700;margin-bottom:3px;">YOU PAY</div>'
                    +   '<div style="font-size:16px;font-weight:900;color:#166534;">BDT ' + Number(d.final_amount).toLocaleString() + '</div>'
                    + '</div>'
                    + '</div></div>';
            fb.innerHTML  = msgHtml;
            fb.style.display = 'block';

            // Update fee summary if present (ILT form)
            if (typeof updateFeeSummaryWithCoupon === 'function') {
                updateFeeSummaryWithCoupon(d.discount_amount, d.final_amount);
            }
        } else {
            var msg = (res.data && res.data.message) ? res.data.message
                : (res.data && res.data.errors && res.data.errors.code ? res.data.errors.code[0] : 'Invalid coupon code.');
            showCouponMsg('error', msg);
        }
    })
    .catch(function() {
        btn.textContent = 'Apply Code';
        btn.disabled    = false;
        showCouponMsg('error', 'Network error. Please try again.');
    });
}

function showCouponMsg(type, msg) {
    var fb = document.getElementById('couponFeedback');
    var bg = type === 'error' ? '#fee2e2' : '#dcfce7';
    var border = type === 'error' ? '#fca5a5' : '#bbf7d0';
    var color  = type === 'error' ? '#991b1b' : '#166534';
    fb.innerHTML = '<div style="background:' + bg + ';border:1px solid ' + border + ';border-radius:10px;padding:12px 14px;font-size:13.5px;font-weight:600;color:' + color + ';">' + msg + '</div>';
    fb.style.display = 'block';
}

document.getElementById('couponCodeInput')?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); applyCoupon(); }
});
</script>
