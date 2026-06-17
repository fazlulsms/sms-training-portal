@extends('layouts.public')

@section('page-title', 'Terms & Conditions — SMS Training Academy')
@section('seo-title', 'Terms & Conditions — SMS Training Academy')
@section('seo-desc', 'Terms and Conditions governing the use of SMS Training Academy training services, online learning platform, payments, certificates, and all related services.')
@section('seo-keys', 'SMS Training Academy terms and conditions, training platform terms, online learning terms, certificate terms, payment terms')

@section('content')
<style>
.legal-hero { background:linear-gradient(135deg,#060d2e 0%,#042C53 45%,#042C53 100%); padding:52px 0 60px; color:#fff; position:relative; overflow:hidden; }
.legal-hero::after { content:''; position:absolute; inset:0; background-image:radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px); background-size:24px 24px; pointer-events:none; }
.legal-hero-inner { position:relative; z-index:1; }
.legal-eyebrow { display:inline-flex; align-items:center; gap:7px; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); padding:4px 13px; border-radius:20px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; margin-bottom:14px; color:rgba(255,255,255,.8); }
.legal-hero h1 { font-size:36px; font-weight:900; margin:0 0 12px; }
.legal-hero-meta { display:flex; gap:20px; flex-wrap:wrap; }
.legal-meta-item { font-size:12.5px; color:rgba(255,255,255,.6); display:flex; align-items:center; gap:6px; }
@media(max-width:640px){ .legal-hero h1 { font-size:26px; } }

/* Layout */
.legal-body { padding:48px 0 72px; }
.legal-grid { display:grid; grid-template-columns:240px 1fr; gap:40px; align-items:start; }
@media(max-width:900px){ .legal-grid { grid-template-columns:1fr; } }

/* Sidebar TOC */
.legal-toc { background:#fff; border:1px solid #e9ecf0; border-radius:16px; padding:20px; position:sticky; top:24px; }
.legal-toc h3 { font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.7px; color:#9ca3af; margin:0 0 12px; }
.legal-toc ul { list-style:none; margin:0; padding:0; }
.legal-toc ul li a { display:block; font-size:13px; color:#374151; text-decoration:none; padding:5px 8px; border-radius:6px; margin-bottom:2px; line-height:1.5; transition:background .1s, color .1s; }
.legal-toc ul li a:hover { background:#eff6ff; color:#042C53; }
.legal-toc-num { color:#9ca3af; font-size:11px; margin-right:4px; }

/* Content */
.legal-content h2 { font-size:20px; font-weight:900; color:#111827; margin:40px 0 14px; padding-top:20px; border-top:2px solid #f1f5f9; }
.legal-content h2:first-child { margin-top:0; border-top:none; padding-top:0; }
.legal-content h3 { font-size:15px; font-weight:800; color:#042C53; margin:22px 0 8px; }
.legal-content p  { font-size:14.5px; color:#374151; line-height:1.85; margin:0 0 14px; }
.legal-content ul { margin:8px 0 16px 0; padding-left:20px; }
.legal-content ul li { font-size:14.5px; color:#374151; line-height:1.8; margin-bottom:5px; }
.legal-content strong { color:#111827; }

/* Highlight boxes */
.legal-note { background:#eff6ff; border-left:3px solid #378ADD; border-radius:0 10px 10px 0; padding:14px 18px; margin:16px 0; }
.legal-note p { margin:0; font-size:13.5px; color:#1e40af; line-height:1.7; }
.legal-warn { background:#fefce8; border-left:3px solid #f59e0b; border-radius:0 10px 10px 0; padding:14px 18px; margin:16px 0; }
.legal-warn p { margin:0; font-size:13.5px; color:#92400e; line-height:1.7; }

/* Payment table */
.legal-table { width:100%; border-collapse:collapse; margin:16px 0; font-size:14px; }
.legal-table th { background:#f8fafc; font-weight:800; color:#374151; text-align:left; padding:10px 14px; border:1px solid #e9ecf0; font-size:12.5px; }
.legal-table td { padding:10px 14px; border:1px solid #e9ecf0; color:#374151; vertical-align:top; }
.legal-table tr:nth-child(even) td { background:#fafafa; }

.legal-eff-banner { background:#f0f9ff; border:1.5px solid #bae6fd; border-radius:12px; padding:14px 20px; margin-bottom:28px; display:flex; align-items:center; gap:12px; }
.legal-eff-banner svg { flex-shrink:0; color:#0284c7; }
.legal-eff-banner p { margin:0; font-size:13.5px; color:#0c4a6e; }
</style>

<div class="legal-hero">
<div class="pub-container">
<div class="legal-hero-inner">
    <div class="legal-eyebrow">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Legal
    </div>
    <h1>Terms &amp; Conditions</h1>
    <div class="legal-hero-meta">
        <span class="legal-meta-item"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> Effective: June 2026</span>
        <span class="legal-meta-item"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> Sustainable Management System Inc., New York, USA</span>
        <span class="legal-meta-item"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Version 1.0</span>
    </div>
</div>
</div>
</div>

<div class="legal-body">
<div class="pub-container">
<div class="legal-grid">

    {{-- TOC --}}
    <div class="legal-toc">
        <h3>Contents</h3>
        <ul>
            <li><a href="#definitions"><span class="legal-toc-num">1.</span> Definitions</a></li>
            <li><a href="#acceptance"><span class="legal-toc-num">2.</span> Acceptance</a></li>
            <li><a href="#eligibility"><span class="legal-toc-num">3.</span> Eligibility</a></li>
            <li><a href="#registration"><span class="legal-toc-num">4.</span> Account Registration</a></li>
            <li><a href="#enrollment"><span class="legal-toc-num">5.</span> Enrolment</a></li>
            <li><a href="#payments"><span class="legal-toc-num">6.</span> Payments &amp; Fees</a></li>
            <li><a href="#refunds"><span class="legal-toc-num">7.</span> Refunds &amp; Cancellations</a></li>
            <li><a href="#ilt"><span class="legal-toc-num">8.</span> Instructor-Led Training</a></li>
            <li><a href="#elearning"><span class="legal-toc-num">9.</span> eLearning Access</a></li>
            <li><a href="#assessment"><span class="legal-toc-num">10.</span> Assessments</a></li>
            <li><a href="#certificates"><span class="legal-toc-num">11.</span> Certificates</a></li>
            <li><a href="#ip"><span class="legal-toc-num">12.</span> Intellectual Property</a></li>
            <li><a href="#conduct"><span class="legal-toc-num">13.</span> Acceptable Use</a></li>
            <li><a href="#liability"><span class="legal-toc-num">14.</span> Liability</a></li>
            <li><a href="#forcemajeure"><span class="legal-toc-num">15.</span> Force Majeure</a></li>
            <li><a href="#amendments"><span class="legal-toc-num">16.</span> Amendments</a></li>
            <li><a href="#governing"><span class="legal-toc-num">17.</span> Governing Law</a></li>
            <li><a href="#contact-legal"><span class="legal-toc-num">18.</span> Contact</a></li>
        </ul>
    </div>

    {{-- Content --}}
    <div class="legal-content">

        <div class="legal-eff-banner">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p>Please read these Terms and Conditions carefully before using any SMS Training Academy services. By accessing our website, registering an account, or enrolling in any training programme, you agree to be bound by these terms.</p>
        </div>

        <h2 id="definitions">1. Definitions</h2>
        <p>In these Terms and Conditions, the following definitions apply:</p>
        <ul>
            <li><strong>"SMS", "we", "us", "our"</strong> refers to Sustainable Management System Inc., incorporated in the State of New York, USA, and its affiliates, subsidiaries, and authorised operating entities including Sustainable Management System Bangladesh and Sustainable Management System UAE.</li>
            <li><strong>"SMS Training Academy"</strong> refers to the professional training and certification division of SMS operating at <strong>smscert.com</strong> and associated subdomains.</li>
            <li><strong>"Platform"</strong> refers to the SMS Training Academy website, learning management system (LMS), participant portal, and all associated digital services.</li>
            <li><strong>"Participant"</strong> refers to any individual who registers an account, enrols in a training programme, or accesses any service offered by SMS Training Academy.</li>
            <li><strong>"Client"</strong> refers to any organisation, employer, or sponsor that purchases training services on behalf of Participants.</li>
            <li><strong>"Programme"</strong> refers to any training course, module, workshop, webinar, eLearning course, or professional development offering made available through the Platform.</li>
            <li><strong>"Certificate"</strong> refers to any digital or physical certificate of completion, participation, or professional credential issued by SMS Training Academy.</li>
        </ul>

        <h2 id="acceptance">2. Acceptance of Terms</h2>
        <p>By accessing or using the Platform, registering an account, making a payment, or enrolling in any Programme, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions, together with our Privacy Policy and Refund and Cancellation Policy, which are incorporated by reference.</p>
        <p>If you do not agree to these Terms, you must not use the Platform or enrol in any Programme. If you are enrolling on behalf of an organisation, you represent and warrant that you have authority to bind that organisation to these Terms.</p>

        <h2 id="eligibility">3. Eligibility</h2>
        <p>The Platform is available to individuals who are at least 18 years of age. By using the Platform, you represent and warrant that you meet this requirement. Some Programmes may have additional prerequisites — these are listed on the relevant Programme page and must be satisfied before enrolment.</p>

        <h2 id="registration">4. Account Registration</h2>
        <p>To access training services, you must create a Participant account. You agree to:</p>
        <ul>
            <li>Provide accurate, current, and complete registration information;</li>
            <li>Maintain and promptly update your account information to keep it accurate and complete;</li>
            <li>Keep your login credentials confidential and not share access with any third party;</li>
            <li>Notify us immediately at <a href="mailto:training@smscert.com" style="color:#042C53;">training@smscert.com</a> if you suspect any unauthorised access to your account;</li>
            <li>Accept responsibility for all activities conducted through your account.</li>
        </ul>
        <p>SMS reserves the right to suspend or terminate accounts that contain inaccurate information or that are found to be in breach of these Terms.</p>

        <h2 id="enrollment">5. Enrolment</h2>
        <p>Enrolment in a Programme is confirmed only upon receipt of full payment (or confirmed purchase order for approved corporate clients) and issuance of a confirmation notice by SMS Training Academy. Until such confirmation is issued, a place on the Programme is not guaranteed.</p>
        <p>SMS reserves the right to refuse enrolment, limit class sizes, or cancel a Programme if minimum participant numbers are not met. In the event of cancellation by SMS, enrolled Participants will receive a full refund of fees paid.</p>

        <h2 id="payments">6. Payments &amp; Fees</h2>
        <h3>6.1 Accepted Payment Methods</h3>
        <p>SMS Training Academy accepts payment through the following methods:</p>
        <table class="legal-table">
            <thead><tr><th>Method</th><th>Provider</th><th>Currencies</th><th>Availability</th></tr></thead>
            <tbody>
                <tr><td>Credit / Debit Card</td><td>Stripe Inc.</td><td>USD, EUR, GBP, AUD, and others</td><td>International participants</td></tr>
                <tr><td>Online Banking (Bangladesh)</td><td>SSLCommerz</td><td>BDT (Bangladeshi Taka)</td><td>Bangladesh participants</td></tr>
                <tr><td>Bank Transfer</td><td>Direct / Wire</td><td>USD, GBP, EUR, BDT</td><td>Corporate clients &amp; approved participants</td></tr>
                <tr><td>Additional Methods</td><td>Future gateways</td><td>To be announced</td><td>Announced as available</td></tr>
            </tbody>
        </table>
        <h3>6.2 Payment Processing Entities</h3>
        <p>Payment may be processed by Sustainable Management System Inc. (USA), Sustainable Management System Bangladesh, Sustainable Management System UAE, or an authorised affiliate or partner, depending on the Participant's location, the Programme's originating entity, and the payment currency and gateway selected at checkout. The processing entity is identified on the payment confirmation and, where applicable, on the Stripe or SSLCommerz checkout page.</p>
        <h3>6.3 Currency and Taxes</h3>
        <p>Programme fees are displayed in the applicable currency at checkout. Where currency conversion is required, the rate applied is determined by the payment gateway at the time of transaction. SMS is not responsible for currency conversion losses or bank-imposed foreign transaction fees.</p>
        <p>Participants and Clients are responsible for determining and paying any applicable taxes, duties, levies, or VAT/GST arising from their enrolment, in accordance with the laws of their jurisdiction. SMS will issue tax invoices upon request.</p>
        <h3>6.4 Late Payment</h3>
        <p>For corporate clients with approved credit terms, invoices are payable within the period stated on the invoice. SMS reserves the right to withhold access to Programmes, certificates, and digital materials pending payment, and to charge interest on overdue balances at a rate of 1.5% per month or the maximum rate permitted by applicable law, whichever is lower.</p>

        <div class="legal-note">
            <p><strong>Stripe:</strong> Card payments are processed by Stripe Inc., a PCI-DSS Level 1 certified payment service provider. SMS does not store full card numbers on our servers. By proceeding with a card payment, you also agree to Stripe's terms of service available at stripe.com/legal.</p>
        </div>
        <div class="legal-note">
            <p><strong>SSLCommerz:</strong> Bangladesh taka payments are processed by SSLCommerz Ltd., a licensed payment service provider regulated in Bangladesh. By proceeding with payment through SSLCommerz, you also agree to SSLCommerz's terms of service.</p>
        </div>

        <h2 id="refunds">7. Refunds &amp; Cancellations</h2>
        <p>Refund eligibility depends on the type of Programme, the timing of the cancellation or withdrawal request, and the circumstances involved. Full details are set out in our <a href="{{ route('public.refund') }}" style="color:#042C53;font-weight:700;">Refund and Cancellation Policy</a>, which is incorporated into these Terms by reference.</p>
        <p>Payment gateway transaction charges (typically 2–4% of the transaction value) are non-refundable in all circumstances, as they represent costs incurred by SMS on processing the original payment. Where a refund is approved, the refunded amount will be net of these gateway charges unless SMS determines in its sole discretion that a full refund is appropriate.</p>

        <h2 id="ilt">8. Instructor-Led Training (ILT &amp; VILT)</h2>
        <p>Instructor-Led Training (ILT) programmes are delivered in person at designated venues. Virtual Instructor-Led Training (VILT) programmes are delivered live online via secure video conferencing platforms. For both formats:</p>
        <ul>
            <li>Participants are required to attend all scheduled sessions. Attendance records are maintained and may affect certificate eligibility.</li>
            <li>Participation in activities, exercises, and assessments is expected as part of the learning experience.</li>
            <li>SMS reserves the right to remove any Participant whose conduct disrupts the training environment or violates these Terms, without refund.</li>
            <li>Programmes, venues, times, and facilitators are subject to change. We will notify enrolled Participants of material changes as soon as practicable.</li>
            <li>Recording of sessions by Participants — by any means — is strictly prohibited without prior written consent from SMS.</li>
        </ul>

        <h2 id="elearning">9. eLearning Access</h2>
        <p>Upon successful enrolment and payment confirmation for self-paced eLearning Programmes, Participants are granted a personal, non-exclusive, non-transferable licence to access the Programme content for the access period specified at enrolment (typically 90 days or 12 months, as stated on the Programme page). This licence is strictly for the individual Participant's personal professional development and may not be shared, sublicensed, or transferred.</p>
        <ul>
            <li>Access periods commence from the date of enrolment confirmation, not from first login.</li>
            <li>Content may be updated periodically; updated content replaces prior versions without notice.</li>
            <li>SMS does not guarantee uninterrupted access and reserves the right to perform maintenance, updates, or modifications to the Platform at any time.</li>
        </ul>

        <h2 id="assessment">10. Assessments &amp; Examinations</h2>
        <p>Many Programmes include knowledge assessments or examinations as a condition for certificate issuance. Unless otherwise stated on the Programme page:</p>
        <ul>
            <li>Assessments must be completed within the Participant's access period.</li>
            <li>Minimum pass marks are specified on the Programme page or assessment instructions.</li>
            <li>The number of permitted attempts is specified per Programme. Additional attempts beyond the permitted number may incur a re-assessment fee.</li>
            <li>Assessments must be completed independently. Any evidence of academic dishonesty, collusion, or use of prohibited materials will result in immediate disqualification and may result in account suspension without refund.</li>
        </ul>

        <h2 id="certificates">11. Certificates</h2>
        <p>Certificates of completion or professional attendance are issued to Participants who fulfil all Programme requirements, including attendance (for ILT/VILT), completion of all required modules (for eLearning), and achievement of the required assessment score (where applicable).</p>
        <ul>
            <li>Certificates are issued digitally through the Platform and are verifiable at <strong>smscert.com/verify</strong>.</li>
            <li>Physical certificates may be available upon request; additional shipping costs may apply.</li>
            <li>The validity period of any certificate is specified on the certificate itself. SMS makes no representation that any certificate will be accepted by any third party, accreditation body, employer, or regulatory authority.</li>
            <li>In the event of suspected fraud, misrepresentation, or breach of these Terms, SMS reserves the right to revoke any certificate. Revoked certificates will be flagged as invalid in the verification registry.</li>
        </ul>

        <h2 id="ip">12. Intellectual Property</h2>
        <p>All content on the Platform — including but not limited to course materials, videos, assessments, study guides, templates, frameworks, graphics, text, branding, and software — is the proprietary intellectual property of Sustainable Management System Inc. or its licensors and is protected by copyright, trademark, and other applicable intellectual property laws.</p>
        <p>Participants are granted a limited licence to access and use Programme materials solely for their personal professional development. No content may be downloaded (except as explicitly permitted), reproduced, redistributed, published, resold, modified, or used to create derivative works without the express prior written consent of SMS.</p>
        <div class="legal-warn">
            <p>Sharing login credentials, downloading and distributing course content, or using SMS Training Academy materials for commercial purposes without authorisation constitutes a breach of these Terms and may result in legal action.</p>
        </div>

        <h2 id="conduct">13. Acceptable Use &amp; Prohibited Activities</h2>
        <p>Participants agree not to:</p>
        <ul>
            <li>Use the Platform for any unlawful purpose or in violation of any applicable laws or regulations;</li>
            <li>Impersonate any person, misrepresent professional credentials, or provide false information;</li>
            <li>Attempt to gain unauthorised access to the Platform, other user accounts, or SMS systems;</li>
            <li>Use automated tools, bots, scrapers, or other means to access or extract Platform content;</li>
            <li>Resell, sublicense, or commercially exploit access to the Platform or any Programme;</li>
            <li>Engage in any conduct that is abusive, harassing, defamatory, or harmful to other users, instructors, or SMS staff;</li>
            <li>Upload, transmit, or distribute any content containing viruses, malware, or other harmful code;</li>
            <li>Circumvent any technical measures implemented to protect Platform security or content integrity.</li>
        </ul>
        <p>SMS reserves the right to suspend or permanently terminate the account of any Participant found to be in breach of this section, without refund and without prior notice.</p>

        <h2 id="liability">14. Limitation of Liability</h2>
        <p>To the fullest extent permitted by applicable law:</p>
        <ul>
            <li>SMS Training Academy provides all Programmes and Platform services on an "as is" and "as available" basis, without warranties of any kind, express or implied;</li>
            <li>SMS does not warrant that Programmes will meet Participant-specific professional requirements, lead to employment outcomes, or satisfy the requirements of any third-party accreditation body;</li>
            <li>SMS's aggregate liability to any Participant or Client for any claim arising under or in connection with these Terms shall not exceed the total fees paid by that Participant or Client for the relevant Programme;</li>
            <li>In no event shall SMS be liable for any indirect, incidental, special, consequential, or punitive damages, including lost profits, lost data, or reputational harm.</li>
        </ul>
        <p>Nothing in these Terms shall limit liability for death or personal injury caused by negligence, fraud or fraudulent misrepresentation, or any other liability that cannot be lawfully excluded.</p>

        <h2 id="forcemajeure">15. Force Majeure</h2>
        <p>SMS shall not be liable for any failure or delay in performing its obligations where such failure or delay results from circumstances beyond its reasonable control, including but not limited to natural disasters, pandemics, government actions, civil unrest, terrorism, internet outages, supplier failures, or acts of God. In such circumstances, SMS will endeavour to reschedule affected Programmes and will notify enrolled Participants promptly. Where rescheduling is not practicable, affected Participants will receive a credit or refund at SMS's discretion.</p>

        <h2 id="amendments">16. Amendments to These Terms</h2>
        <p>SMS reserves the right to update or amend these Terms at any time. Material changes will be notified to registered Participants by email and/or via a notice on the Platform at least 14 days before the changes take effect. Your continued use of the Platform or enrolment in any Programme after the effective date of any changes constitutes your acceptance of the revised Terms.</p>

        <h2 id="governing">17. Governing Law &amp; Dispute Resolution</h2>
        <p>These Terms are governed by and construed in accordance with the laws of the State of New York, USA, without regard to conflict of law principles. Any disputes arising under these Terms shall first be subject to good-faith negotiation between the parties. If not resolved within 30 days, disputes shall be submitted to binding arbitration under the Commercial Arbitration Rules of the American Arbitration Association, in New York, New York.</p>
        <p>Nothing in this clause prevents either party from seeking urgent injunctive or interim relief from a court of competent jurisdiction. Participants in jurisdictions with mandatory consumer protection or local governing law provisions may retain rights under those local laws that cannot be excluded by contract.</p>

        <h2 id="contact-legal">18. Contact &amp; Legal Notices</h2>
        <p>For questions about these Terms, to request a copy, or to submit a legal notice, please contact:</p>
        <div style="background:#f8fafc;border:1.5px solid #e9ecf0;border-radius:12px;padding:20px;margin-top:10px;">
            <p style="margin:0 0 6px;"><strong>Sustainable Management System Inc.</strong></p>
            <p style="margin:0 0 4px;color:#6b7280;">277 Cherry Street, Suite-12N, New York, NY, USA</p>
            <p style="margin:0 0 4px;"><a href="mailto:info@smscert.com" style="color:#042C53;">info@smscert.com</a></p>
            <p style="margin:0;"><a href="{{ route('public.contact') }}" style="color:#042C53;">Contact Form</a></p>
        </div>
        <p style="margin-top:20px;font-size:13px;color:#9ca3af;">These Terms &amp; Conditions were last updated in June 2026. Previous versions are available upon written request.</p>
    </div>

</div>
</div>
</div>
@endsection
