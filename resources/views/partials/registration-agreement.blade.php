@include('partials.registration-policies')

<div class="agreement-card">
    <div class="agreement-row">
        <input type="checkbox" id="agree_terms" name="agree_terms" required value="1">
        <label for="agree_terms">
            I have read and agree to the <strong>Terms &amp; Conditions</strong> and <strong>Refund Policy</strong> of SMS Training Services.
        </label>
    </div>
    <div class="agreement-row">
        <input type="checkbox" id="agree_privacy" name="agree_privacy" required value="1">
        <label for="agree_privacy">
            I consent to SMS Training Services collecting and processing my personal data as described in the <strong>Privacy Policy</strong>.
        </label>
    </div>
    <div class="agreement-row">
        <input type="checkbox" id="agree_comms" name="agree_comms" value="1">
        <label for="agree_comms">
            I agree to receive course updates, training news, and relevant communications from SMS Training Services. (Optional)
        </label>
    </div>
</div>
