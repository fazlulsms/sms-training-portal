<div class="payment-info-box">
    <div class="payment-info-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        Payment Information
    </div>
    <div class="payment-method-grid">
        <div class="payment-method-chip">🏦 Bank Transfer</div>
        <div class="payment-method-chip">📱 bKash / Nagad</div>
        <div class="payment-method-chip">💳 Online Gateway</div>
        <div class="payment-method-chip">🏢 Office Payment</div>
    </div>
    <div class="payment-info-note">
        @if(isset($type) && $type === 'elearning')
        Course fee is payable after registration is confirmed. You will receive detailed payment instructions via email within 24 hours of submitting this form. Course access will be granted once payment is verified.
        @else
        Course fee is payable upon enrollment confirmation. You will receive payment instructions via email after your enrollment is reviewed. Payment can be made via bank transfer, bKash, Nagad, or our online payment gateway.
        @endif
    </div>
</div>
