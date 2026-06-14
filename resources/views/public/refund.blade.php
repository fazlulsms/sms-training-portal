@extends('layouts.public')

@section('page-title', 'Refund & Cancellation Policy — SMS Training Academy')
@section('seo-title', 'Refund & Cancellation Policy — SMS Training Academy')
@section('seo-desc', 'Refund and cancellation policy for SMS Training Academy. Full details of refund entitlements for instructor-led training, virtual training, eLearning, corporate programmes, and subscriptions.')
@section('seo-keys', 'SMS Training Academy refund policy, training cancellation policy, course refund, eLearning refund, ILT cancellation, corporate training refund')

@section('content')
<style>
.legal-hero { background:linear-gradient(135deg,#060d2e 0%,#0f2470 45%,#1e3a8a 100%); padding:52px 0 60px; color:#fff; position:relative; overflow:hidden; }
.legal-hero::after { content:''; position:absolute; inset:0; background-image:radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px); background-size:24px 24px; pointer-events:none; }
.legal-hero-inner { position:relative; z-index:1; }
.legal-eyebrow { display:inline-flex; align-items:center; gap:7px; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); padding:4px 13px; border-radius:20px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; margin-bottom:14px; color:rgba(255,255,255,.8); }
.legal-hero h1 { font-size:36px; font-weight:900; margin:0 0 12px; }
.legal-hero-meta { display:flex; gap:20px; flex-wrap:wrap; }
.legal-meta-item { font-size:12.5px; color:rgba(255,255,255,.6); display:flex; align-items:center; gap:6px; }
@media(max-width:640px){ .legal-hero h1 { font-size:26px; } }
.legal-body { padding:48px 0 72px; }
.legal-grid { display:grid; grid-template-columns:240px 1fr; gap:40px; align-items:start; }
@media(max-width:900px){ .legal-grid { grid-template-columns:1fr; } }
.legal-toc { background:#fff; border:1px solid #e9ecf0; border-radius:16px; padding:20px; position:sticky; top:24px; }
.legal-toc h3 { font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.7px; color:#9ca3af; margin:0 0 12px; }
.legal-toc ul { list-style:none; margin:0; padding:0; }
.legal-toc ul li a { display:block; font-size:13px; color:#374151; text-decoration:none; padding:5px 8px; border-radius:6px; margin-bottom:2px; line-height:1.5; transition:background .1s, color .1s; }
.legal-toc ul li a:hover { background:#eff6ff; color:#1e3a8a; }
.legal-toc-num { color:#9ca3af; font-size:11px; margin-right:4px; }
.legal-content h2 { font-size:20px; font-weight:900; color:#111827; margin:40px 0 14px; padding-top:20px; border-top:2px solid #f1f5f9; }
.legal-content h2:first-child { margin-top:0; border-top:none; padding-top:0; }
.legal-content h3 { font-size:15px; font-weight:800; color:#1e3a8a; margin:22px 0 8px; }
.legal-content p { font-size:14.5px; color:#374151; line-height:1.85; margin:0 0 14px; }
.legal-content ul { margin:8px 0 16px 0; padding-left:20px; }
.legal-content ul li { font-size:14.5px; color:#374151; line-height:1.8; margin-bottom:5px; }
.legal-content strong { color:#111827; }
.legal-note { background:#eff6ff; border-left:3px solid #2563eb; border-radius:0 10px 10px 0; padding:14px 18px; margin:16px 0; }
.legal-note p { margin:0; font-size:13.5px; color:#1e40af; line-height:1.7; }
.legal-warn { background:#fff7ed; border-left:3px solid #f97316; border-radius:0 10px 10px 0; padding:14px 18px; margin:16px 0; }
.legal-warn p { margin:0; font-size:13.5px; color:#9a3412; line-height:1.7; }
.legal-eff-banner { background:#f0f9ff; border:1.5px solid #bae6fd; border-radius:12px; padding:14px 20px; margin-bottom:28px; display:flex; align-items:center; gap:12px; }
.legal-eff-banner svg { flex-shrink:0; color:#0284c7; }
.legal-eff-banner p { margin:0; font-size:13.5px; color:#0c4a6e; }

/* Refund tiers table */
.rp-tier-table { width:100%; border-collapse:collapse; margin:16px 0; font-size:13.5px; }
.rp-tier-table th { background:#f1f5f9; font-weight:800; color:#374151; text-align:left; padding:10px 14px; border:1px solid #e2e8f0; font-size:12.5px; }
.rp-tier-table td { padding:10px 14px; border:1px solid #e9ecf0; color:#374151; vertical-align:top; }
.rp-tier-table tr:nth-child(even) td { background:#fafafa; }
.rp-tier-pct { font-weight:800; }
.pct-90 { color:#16a34a; }
.pct-75 { color:#2563eb; }
.pct-50 { color:#f59e0b; }
.pct-25 { color:#f97316; }
.pct-0  { color:#dc2626; }

/* Summary cards */
.rp-summary-cards { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin:20px 0; }
@media(max-width:680px){ .rp-summary-cards { grid-template-columns:1fr 1fr; } }
@media(max-width:420px){ .rp-summary-cards { grid-template-columns:1fr; } }
.rp-sum-card { background:#fff; border:1.5px solid #e9ecf0; border-radius:14px; padding:18px; text-align:center; }
.rp-sum-card h4 { font-size:13px; font-weight:800; color:#374151; margin:0 0 6px; }
.rp-sum-card p  { font-size:12px; color:#6b7280; margin:0; line-height:1.5; }

/* Process section */
.rp-process-steps { display:flex; flex-direction:column; gap:12px; margin:16px 0; }
.rp-step { display:flex; align-items:flex-start; gap:14px; background:#f8fafc; border:1px solid #e9ecf0; border-radius:12px; padding:16px; }
.rp-step-num { width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg,#0f2470,#1e3a8a); color:#fff; font-size:12px; font-weight:900; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rp-step h4 { font-size:14px; font-weight:800; color:#111827; margin:0 0 3px; }
.rp-step p  { font-size:13px; color:#6b7280; margin:0; line-height:1.5; }
</style>

<div class="legal-hero">
<div class="pub-container">
<div class="legal-hero-inner">
    <div class="legal-eyebrow">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        Legal
    </div>
    <h1>Refund &amp; Cancellation Policy</h1>
    <div class="legal-hero-meta">
        <span class="legal-meta-item"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Effective: June 2026</span>
        <span class="legal-meta-item"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> Sustainable Management System Inc., New York, USA</span>
    </div>
</div>
</div>
</div>

<div class="legal-body">
<div class="pub-container">
<div class="legal-grid">

    <div class="legal-toc">
        <h3>Contents</h3>
        <ul>
            <li><a href="#rf-overview"><span class="legal-toc-num">1.</span> Overview</a></li>
            <li><a href="#rf-ilt"><span class="legal-toc-num">2.</span> Instructor-Led Training</a></li>
            <li><a href="#rf-vilt"><span class="legal-toc-num">3.</span> Virtual Training (VILT)</a></li>
            <li><a href="#rf-elearning"><span class="legal-toc-num">4.</span> eLearning Courses</a></li>
            <li><a href="#rf-corporate"><span class="legal-toc-num">5.</span> Corporate Programmes</a></li>
            <li><a href="#rf-subscription"><span class="legal-toc-num">6.</span> Subscriptions</a></li>
            <li><a href="#rf-gateway"><span class="legal-toc-num">7.</span> Gateway Charges</a></li>
            <li><a href="#rf-currency"><span class="legal-toc-num">8.</span> Currency &amp; FX</a></li>
            <li><a href="#rf-exceptional"><span class="legal-toc-num">9.</span> Exceptional Circumstances</a></li>
            <li><a href="#rf-sms-cancel"><span class="legal-toc-num">10.</span> SMS Cancellations</a></li>
            <li><a href="#rf-transfer"><span class="legal-toc-num">11.</span> Transfers</a></li>
            <li><a href="#rf-process"><span class="legal-toc-num">12.</span> Refund Process</a></li>
            <li><a href="#rf-contact"><span class="legal-toc-num">13.</span> Contact</a></li>
        </ul>
    </div>

    <div class="legal-content">

        <div class="legal-eff-banner">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p>This policy sets out the refund and cancellation terms for all training programmes and services offered by SMS Training Academy. Please read it carefully before enrolment. Separate terms may apply to corporate contracts — refer to your contract or contact us for details.</p>
        </div>

        <h2 id="rf-overview">1. Overview</h2>
        <p>SMS Training Academy is committed to delivering high-quality training experiences and to treating all refund and cancellation requests fairly. Our refund entitlements depend on the type of training programme, how far in advance a cancellation is made, and whether the programme has been accessed or commenced.</p>

        <div class="rp-summary-cards">
            <div class="rp-sum-card">
                <h4>ILT / VILT</h4>
                <p>Tiered refunds based on days before course start date</p>
            </div>
            <div class="rp-sum-card">
                <h4>eLearning</h4>
                <p>Full refund within 48 hours if course not accessed</p>
            </div>
            <div class="rp-sum-card">
                <h4>Corporate</h4>
                <p>Per contractual agreement — contact for details</p>
            </div>
        </div>

        <div class="legal-warn">
            <p><strong>Important:</strong> Payment gateway transaction charges are non-refundable in all cases. All refund amounts quoted in this Policy are net of applicable gateway charges. See Section 7 for full details.</p>
        </div>

        <h2 id="rf-ilt">2. Instructor-Led Training (ILT)</h2>
        <p>The following refund schedule applies to all in-person Instructor-Led Training programmes. Cancellations must be submitted in writing to <a href="mailto:training@smscert.com" style="color:#1e3a8a;">training@smscert.com</a>. The number of calendar days is calculated from the date the written cancellation request is received to the scheduled first day of the programme.</p>

        <table class="rp-tier-table">
            <thead>
                <tr>
                    <th>Notice Period</th>
                    <th>Refund Entitlement</th>
                    <th>Processing Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>21 or more calendar days before start</strong></td>
                    <td><span class="rp-tier-pct pct-90">90% of fees paid</span></td>
                    <td>7–10 business days</td>
                </tr>
                <tr>
                    <td><strong>14–20 calendar days before start</strong></td>
                    <td><span class="rp-tier-pct pct-75">75% of fees paid</span></td>
                    <td>7–10 business days</td>
                </tr>
                <tr>
                    <td><strong>7–13 calendar days before start</strong></td>
                    <td><span class="rp-tier-pct pct-50">50% of fees paid</span></td>
                    <td>7–10 business days</td>
                </tr>
                <tr>
                    <td><strong>1–6 calendar days before start</strong></td>
                    <td><span class="rp-tier-pct pct-25">25% of fees paid</span></td>
                    <td>7–10 business days</td>
                </tr>
                <tr>
                    <td><strong>No-show / cancellation on programme day</strong></td>
                    <td><span class="rp-tier-pct pct-0">No refund</span></td>
                    <td>—</td>
                </tr>
            </tbody>
        </table>

        <p>All refund amounts are gross — gateway charges as described in Section 7 are deducted before processing. Participants who withdraw after the programme has commenced are not entitled to a refund for any portion of the programme not attended.</p>

        <h2 id="rf-vilt">3. Virtual Instructor-Led Training (VILT)</h2>
        <p>The same tiered refund schedule as Section 2 applies to Virtual Instructor-Led Training (VILT) programmes:</p>

        <table class="rp-tier-table">
            <thead>
                <tr><th>Notice Period</th><th>Refund Entitlement</th><th>Processing Time</th></tr>
            </thead>
            <tbody>
                <tr><td><strong>21+ calendar days before start</strong></td><td><span class="rp-tier-pct pct-90">90% of fees paid</span></td><td>7–10 business days</td></tr>
                <tr><td><strong>14–20 calendar days before start</strong></td><td><span class="rp-tier-pct pct-75">75% of fees paid</span></td><td>7–10 business days</td></tr>
                <tr><td><strong>7–13 calendar days before start</strong></td><td><span class="rp-tier-pct pct-50">50% of fees paid</span></td><td>7–10 business days</td></tr>
                <tr><td><strong>1–6 calendar days before start</strong></td><td><span class="rp-tier-pct pct-25">25% of fees paid</span></td><td>7–10 business days</td></tr>
                <tr><td><strong>No-show / cancellation on session day</strong></td><td><span class="rp-tier-pct pct-0">No refund</span></td><td>—</td></tr>
            </tbody>
        </table>

        <p>For VILT programmes, "commencement" is defined as the scheduled start time of the first live session. Technical difficulties experienced by the Participant (such as internet connectivity, device compatibility, or platform access issues) do not qualify as grounds for a refund once the programme has commenced.</p>

        <h2 id="rf-elearning">4. Self-Paced eLearning Courses</h2>
        <h3>4.1 Full Refund Period</h3>
        <p>A full refund (less gateway charges) is available for self-paced eLearning courses if both of the following conditions are met:</p>
        <ul>
            <li>The refund request is submitted within <strong>48 hours</strong> of enrolment confirmation; <em>and</em></li>
            <li>The Participant has <strong>not accessed</strong> any course content (no module openings, video plays, or assessment attempts logged in the system).</li>
        </ul>
        <p>If either condition is not met, no refund is available. Access to the Platform triggers the system log regardless of whether content was formally "viewed" — the system log is the authoritative record.</p>
        <h3>4.2 No Refund After Access</h3>
        <p>Once any content within an eLearning course has been accessed, no refund is available under any circumstances. This is because the intellectual property — the course content — has been delivered and accessed by the Participant at that point.</p>
        <h3>4.3 Exceptions</h3>
        <p>Where a technical fault on SMS's Platform prevents access to course content for a sustained period (48 hours or more), SMS will, at its discretion, offer an extended access period or a credit, in lieu of a refund.</p>

        <h2 id="rf-corporate">5. Corporate Training Programmes</h2>
        <p>Refund and cancellation terms for corporate training programmes — including customised in-house training, group bookings, and blended learning contracts — are governed by the written agreement between SMS and the Client organisation.</p>
        <p>Where no specific contractual terms are in place, the standard ILT or VILT refund tiers (Sections 2 and 3) apply to individually enrolled corporate Participants. For group bookings, the cancellation notice period is calculated from the total group booking contract date, not from individual enrolments.</p>
        <p>For enquiries about corporate programme cancellations or adjustments, contact: <a href="mailto:info@smscert.com" style="color:#1e3a8a;">info@smscert.com</a></p>

        <h2 id="rf-subscription">6. Subscriptions &amp; Access Plans</h2>
        <p>Subscription-based access plans to SMS Training Academy content are not currently available. This section will be updated when subscription products are launched. When available, subscription terms — including free trial conditions, billing cycles, and cancellation terms — will be published on the relevant subscription product page and incorporated into this Policy.</p>

        <h2 id="rf-gateway">7. Payment Gateway Charges</h2>
        <p>Payment gateway transaction fees charged by Stripe Inc. (for card payments) and SSLCommerz Ltd. (for Bangladesh taka payments) are incurred by SMS at the time of the original transaction and are <strong>non-refundable in all cases</strong>, regardless of the reason for cancellation or the approved refund percentage.</p>
        <p>These charges typically represent 2–4% of the gross transaction value, depending on the payment method, currency, and gateway used. The exact deduction will be calculated and communicated to the Participant or Client at the time a refund is approved.</p>
        <p>In exceptional circumstances — for example, where a programme is cancelled by SMS for reasons within SMS's control — SMS may elect to absorb gateway charges and process a full gross refund. This is at SMS's sole discretion.</p>

        <h2 id="rf-currency">8. Currency Conversion &amp; Foreign Exchange</h2>
        <p>Where payment was made in a currency other than the currency in which fees are quoted, the refunded amount will be calculated in the original currency of the transaction. SMS is not responsible for any difference in the value of a refund due to exchange rate fluctuations between the date of original payment and the date of refund processing.</p>
        <p>Foreign transaction fees, bank charges, or currency conversion costs imposed by the Participant's bank or card issuer are not borne by SMS and will not be refunded.</p>

        <h2 id="rf-exceptional">9. Exceptional Circumstances</h2>
        <p>SMS Training Academy recognises that exceptional personal circumstances may arise that prevent participation in a programme. Requests for refunds or credits outside the standard tiers may be considered on a case-by-case basis in the following circumstances:</p>
        <ul>
            <li>Serious illness or medical emergency of the Participant or an immediate family member (supporting documentation required);</li>
            <li>Bereavement of an immediate family member;</li>
            <li>Natural disasters or force majeure events directly affecting the Participant;</li>
            <li>Visa or travel document refusal where in-person attendance was required (documentation of refusal required);</li>
            <li>Significant, documented change in employment status directly affecting eligibility or the relevance of the programme.</li>
        </ul>
        <p>In these circumstances, SMS may offer one of the following at its discretion: a transfer to the next available programme cohort at no additional cost, a credit note valid for 12 months towards any SMS Training Academy programme, or a partial or full refund less gateway charges. Approval of exceptional circumstance requests is not guaranteed and is subject to SMS's review of the supporting documentation.</p>
        <p>To submit an exceptional circumstances request, contact <a href="mailto:training@smscert.com" style="color:#1e3a8a;">training@smscert.com</a> with the subject line "Exceptional Circumstances Request" and attach relevant supporting documents.</p>

        <h2 id="rf-sms-cancel">10. Cancellations by SMS Training Academy</h2>
        <p>SMS Training Academy reserves the right to cancel, reschedule, or modify any Programme at any time. If SMS cancels a scheduled Programme:</p>
        <ul>
            <li>Enrolled Participants will be notified by email as soon as practicable;</li>
            <li>Participants will be offered the choice of: a transfer to the next available cohort of the same Programme, a credit note for the full amount paid (valid 12 months), or a full refund of fees paid — including any gateway charges that SMS elects to absorb in these circumstances;</li>
            <li>SMS is not liable for any consequential costs incurred by Participants in connection with the cancelled Programme, including but not limited to non-refundable travel costs, accommodation, or visa fees.</li>
        </ul>
        <p>Where a Programme is rescheduled (not cancelled), enrolled Participants who cannot attend the rescheduled date will be treated as having cancelled on the date of the rescheduling notification, with the standard cancellation tiers applied as at that date relative to the original programme date.</p>

        <h2 id="rf-transfer">11. Programme Transfers</h2>
        <p>Subject to availability and programme type, Participants may request a transfer to a different date or cohort of the same Programme in lieu of cancellation. Transfer requests must be submitted in writing to <a href="mailto:training@smscert.com" style="color:#1e3a8a;">training@smscert.com</a>. The following conditions apply:</p>
        <ul>
            <li>Transfers are subject to availability and are not guaranteed;</li>
            <li>A maximum of one complimentary transfer per enrolment is permitted if requested at least 14 days before the original programme start date;</li>
            <li>A second or subsequent transfer, or a transfer requested within 14 days of the programme, may be subject to an administration fee;</li>
            <li>Transfers to a higher-priced cohort or programme require payment of the price difference;</li>
            <li>Transfers to a lower-priced cohort do not entitle the Participant to a partial refund of the difference.</li>
        </ul>
        <p>Substitution of one Participant for another from the same organisation is permitted with advance written notice, provided the substitute Participant meets any applicable prerequisites.</p>

        <h2 id="rf-process">12. Refund Request Process</h2>
        <div class="rp-process-steps">
            <div class="rp-step">
                <div class="rp-step-num">1</div>
                <div>
                    <h4>Submit Your Request in Writing</h4>
                    <p>Email <a href="mailto:training@smscert.com" style="color:#1e3a8a;">training@smscert.com</a> with the subject "Refund Request — [Your Full Name] — [Programme Name]". Include your enrolment date, programme start date, reason for cancellation, and any supporting documents.</p>
                </div>
            </div>
            <div class="rp-step">
                <div class="rp-step-num">2</div>
                <div>
                    <h4>Acknowledgement</h4>
                    <p>We will acknowledge receipt of your request within 2 business days and confirm eligibility under this Policy based on the date of your written request and programme type.</p>
                </div>
            </div>
            <div class="rp-step">
                <div class="rp-step-num">3</div>
                <div>
                    <h4>Review &amp; Approval</h4>
                    <p>Approved refunds and the applicable amount (net of gateway charges) will be confirmed to you in writing within 5 business days of your request. Exceptional circumstance requests may take up to 10 business days.</p>
                </div>
            </div>
            <div class="rp-step">
                <div class="rp-step-num">4</div>
                <div>
                    <h4>Refund Processing</h4>
                    <p>Approved refunds are processed to the original payment method within 7–10 business days of approval. Card refunds (Stripe) and gateway refunds (SSLCommerz) may take an additional 3–7 banking days to appear on your statement, depending on your bank.</p>
                </div>
            </div>
        </div>

        <div class="legal-note">
            <p><strong>Refund method:</strong> Refunds are returned to the original payment method. We cannot refund to a different card, account, or method than was used for the original transaction. Bank transfer refunds are processed within the standard international wire processing timelines.</p>
        </div>

        <h2 id="rf-contact">13. Contact for Refund &amp; Cancellation Enquiries</h2>
        <p>All cancellation and refund requests must be submitted in writing. Verbal requests cannot be accepted.</p>
        <div style="background:#f8fafc;border:1.5px solid #e9ecf0;border-radius:12px;padding:20px;margin-top:10px;">
            <p style="margin:0 0 6px;"><strong>SMS Training Academy — Training Administration</strong></p>
            <p style="margin:0 0 4px;"><a href="mailto:training@smscert.com" style="color:#1e3a8a;">training@smscert.com</a> <span style="color:#9ca3af;font-size:12px;">(preferred — for refund requests, include programme name and enrolment date)</span></p>
            <p style="margin:0 0 4px;color:#6b7280;">Sustainable Management System Inc., 277 Cherry Street, Suite-12N, New York, NY, USA</p>
            <p style="margin:0;"><a href="{{ route('public.contact') }}" style="color:#1e3a8a;">Contact Form</a></p>
        </div>
        <p style="margin-top:20px;font-size:13px;color:#9ca3af;">This Refund and Cancellation Policy was last updated in June 2026 and applies to all enrolments from that date. Previous versions are available upon written request. SMS Training Academy reserves the right to update this Policy at any time, with reasonable notice.</p>
    </div>

</div>
</div>
</div>
@endsection
