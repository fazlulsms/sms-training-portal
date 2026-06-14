@extends('layouts.public')

@section('page-title', 'Privacy Policy — SMS Training Academy')
@section('seo-title', 'Privacy Policy — SMS Training Academy')
@section('seo-desc', 'Privacy Policy for SMS Training Academy. Learn how we collect, use, protect, and share your personal data, and what rights you have under applicable data protection laws including GDPR.')
@section('seo-keys', 'SMS Training Academy privacy policy, data protection, GDPR, personal data, privacy rights, data processing')

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
.legal-content p  { font-size:14.5px; color:#374151; line-height:1.85; margin:0 0 14px; }
.legal-content ul { margin:8px 0 16px 0; padding-left:20px; }
.legal-content ul li { font-size:14.5px; color:#374151; line-height:1.8; margin-bottom:5px; }
.legal-content strong { color:#111827; }
.legal-note { background:#eff6ff; border-left:3px solid #2563eb; border-radius:0 10px 10px 0; padding:14px 18px; margin:16px 0; }
.legal-note p { margin:0; font-size:13.5px; color:#1e40af; line-height:1.7; }
.legal-rights-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin:16px 0; }
@media(max-width:580px){ .legal-rights-grid { grid-template-columns:1fr; } }
.legal-right-card { background:#f8fafc; border:1px solid #e9ecf0; border-radius:12px; padding:16px; }
.legal-right-card h4 { font-size:13.5px; font-weight:800; color:#111827; margin:0 0 5px; }
.legal-right-card p { font-size:13px; color:#6b7280; margin:0; line-height:1.6; }
.legal-eff-banner { background:#f0f9ff; border:1.5px solid #bae6fd; border-radius:12px; padding:14px 20px; margin-bottom:28px; display:flex; align-items:center; gap:12px; }
.legal-eff-banner svg { flex-shrink:0; color:#0284c7; }
.legal-eff-banner p { margin:0; font-size:13.5px; color:#0c4a6e; }
</style>

<div class="legal-hero">
<div class="pub-container">
<div class="legal-hero-inner">
    <div class="legal-eyebrow">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Legal
    </div>
    <h1>Privacy Policy</h1>
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
            <li><a href="#priv-intro"><span class="legal-toc-num">1.</span> Introduction</a></li>
            <li><a href="#priv-controller"><span class="legal-toc-num">2.</span> Data Controller</a></li>
            <li><a href="#priv-collect"><span class="legal-toc-num">3.</span> Data We Collect</a></li>
            <li><a href="#priv-howuse"><span class="legal-toc-num">4.</span> How We Use Your Data</a></li>
            <li><a href="#priv-basis"><span class="legal-toc-num">5.</span> Legal Basis (GDPR)</a></li>
            <li><a href="#priv-sharing"><span class="legal-toc-num">6.</span> Data Sharing</a></li>
            <li><a href="#priv-transfer"><span class="legal-toc-num">7.</span> International Transfers</a></li>
            <li><a href="#priv-retention"><span class="legal-toc-num">8.</span> Data Retention</a></li>
            <li><a href="#priv-security"><span class="legal-toc-num">9.</span> Security</a></li>
            <li><a href="#priv-cookies"><span class="legal-toc-num">10.</span> Cookies</a></li>
            <li><a href="#priv-rights"><span class="legal-toc-num">11.</span> Your Rights</a></li>
            <li><a href="#priv-children"><span class="legal-toc-num">12.</span> Children's Privacy</a></li>
            <li><a href="#priv-changes"><span class="legal-toc-num">13.</span> Changes to Policy</a></li>
            <li><a href="#priv-contact"><span class="legal-toc-num">14.</span> Contact Us</a></li>
        </ul>
    </div>

    <div class="legal-content">

        <div class="legal-eff-banner">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p>This Privacy Policy explains how SMS Training Academy collects, uses, stores, and shares your personal information. We are committed to protecting your privacy and handling your data transparently and in compliance with applicable data protection laws.</p>
        </div>

        <h2 id="priv-intro">1. Introduction</h2>
        <p>SMS Training Academy ("we", "us", "our") operates as the professional training division of Sustainable Management System Inc. ("SMS"), incorporated in New York, USA. This Privacy Policy governs the collection, processing, storage, and sharing of personal data in connection with your use of the SMS Training Academy website (<strong>smscert.com</strong>), learning management system, participant portal, and all associated services (collectively, the "Platform").</p>
        <p>We respect your privacy and are committed to complying with applicable data protection laws, including the EU General Data Protection Regulation (GDPR), the UK GDPR, and other applicable regional data protection frameworks where required by law.</p>

        <h2 id="priv-controller">2. Data Controller</h2>
        <p>The data controller responsible for your personal information is:</p>
        <div style="background:#f8fafc;border:1.5px solid #e9ecf0;border-radius:12px;padding:20px;margin:10px 0 16px;">
            <p style="margin:0 0 4px;"><strong>Sustainable Management System Inc.</strong></p>
            <p style="margin:0 0 4px;color:#6b7280;">277 Cherry Street, Suite-12N, New York, NY, USA</p>
            <p style="margin:0;"><a href="mailto:info@smscert.com" style="color:#1e3a8a;">info@smscert.com</a></p>
        </div>
        <p>For individuals in the European Economic Area or United Kingdom, SMS acts as the data controller and has implemented appropriate safeguards for international data transfers as described in Section 7.</p>

        <h2 id="priv-collect">3. Data We Collect</h2>
        <h3>3.1 Account and Registration Data</h3>
        <p>When you create a Participant account, we collect: full name, email address, phone number (optional), country/region, organisation or employer name (optional), professional title, and any other information you provide in your profile.</p>
        <h3>3.2 Training and Learning Data</h3>
        <p>Through your use of the Platform and training programmes, we collect: courses enrolled, progress and completion records, assessment scores and attempt history, attendance records (for ILT and VILT), completion dates, certificate numbers and issuance records, and learning activity logs (time spent, pages accessed, videos viewed).</p>
        <h3>3.3 Payment and Transaction Data</h3>
        <p>When you make a payment, we collect transaction information including: the amount paid, currency, date and method of payment, invoice and receipt details, and transaction reference numbers. We do not store full payment card numbers on our servers. Card payment data is processed and stored securely by our payment processor, Stripe Inc. Bangladesh taka payments are processed by SSLCommerz Ltd.</p>
        <h3>3.4 Technical and Usage Data</h3>
        <p>We automatically collect certain technical data when you access the Platform, including: IP address, browser type and version, operating system, device type, pages visited, time and duration of visits, referral URLs, and session identifiers. This data is collected through server logs and analytics tools.</p>
        <h3>3.5 Communications Data</h3>
        <p>We retain records of communications you initiate with us via contact forms, email, or support channels, including the content of those communications, for the purposes of responding to enquiries and maintaining records.</p>
        <h3>3.6 Cookies and Tracking</h3>
        <p>We use cookies and similar tracking technologies as described in Section 10.</p>

        <h2 id="priv-howuse">4. How We Use Your Personal Data</h2>
        <p>We use personal data collected through the Platform for the following purposes:</p>
        <ul>
            <li><strong>Account management:</strong> Creating and maintaining your Participant account, enabling login and access to Programme content.</li>
            <li><strong>Programme delivery:</strong> Providing access to enrolled courses, tracking progress, administering assessments, and issuing certificates.</li>
            <li><strong>Payment processing:</strong> Processing enrolment fees, issuing invoices, managing refunds, and maintaining financial records.</li>
            <li><strong>Communication:</strong> Sending enrolment confirmations, certificates, receipts, course updates, technical alerts, and responses to enquiries.</li>
            <li><strong>Service improvement:</strong> Analysing usage patterns to improve Platform functionality, content quality, and user experience.</li>
            <li><strong>Legal compliance:</strong> Meeting legal and regulatory obligations, including financial record-keeping, anti-fraud measures, and responding to lawful requests from authorities.</li>
            <li><strong>Marketing (with consent):</strong> Where you have opted in, sending information about new programmes, offers, and professional development resources. You may opt out at any time.</li>
        </ul>

        <h2 id="priv-basis">5. Legal Basis for Processing (GDPR)</h2>
        <p>For individuals subject to the GDPR or UK GDPR, we process personal data on the following legal bases:</p>
        <ul>
            <li><strong>Contract performance (Art. 6(1)(b)):</strong> Processing necessary to provide training services following enrolment, including account management, programme access, and certificate issuance.</li>
            <li><strong>Legitimate interests (Art. 6(1)(f)):</strong> Platform security, fraud prevention, service improvement, and alumni communications, where our interests are not overridden by your data protection rights.</li>
            <li><strong>Legal obligation (Art. 6(1)(c)):</strong> Maintaining financial records, responding to lawful authority requests, and meeting tax and regulatory reporting requirements.</li>
            <li><strong>Consent (Art. 6(1)(a)):</strong> Marketing communications and non-essential cookies, where your opt-in consent has been obtained.</li>
        </ul>

        <h2 id="priv-sharing">6. Data Sharing</h2>
        <h3>6.1 Within the SMS Group</h3>
        <p>Your personal data may be shared with other entities within the Sustainable Management System group — including Sustainable Management System Bangladesh and Sustainable Management System UAE — where necessary for operational purposes, programme delivery in your region, or regional compliance requirements. All group entities are bound by equivalent data protection standards.</p>
        <h3>6.2 Payment Processors</h3>
        <p><strong>Stripe Inc.</strong> — For international card payments. Stripe is a PCI-DSS Level 1 compliant payment processor. Data shared with Stripe includes: name, email, billing address, and transaction details. Stripe's privacy practices are governed by Stripe's Privacy Policy.</p>
        <p><strong>SSLCommerz Ltd.</strong> — For Bangladesh taka payments. Data shared includes transaction details necessary to complete the payment. SSLCommerz is a regulated payment service provider in Bangladesh.</p>
        <h3>6.3 Service Providers</h3>
        <p>We engage trusted third-party service providers to assist in operating the Platform and delivering training, including: learning management system (LMS) infrastructure providers, video conferencing platforms (for VILT delivery), email and communication service providers, cloud hosting and storage providers, and analytics service providers. All service providers are contractually obligated to process personal data only on our instructions and to maintain appropriate security measures.</p>
        <h3>6.4 Authorised Training Partners</h3>
        <p>Where a Programme is delivered in partnership with an authorised franchise partner or regional training centre, relevant training and certificate data may be shared with that partner for the purposes of co-delivering or administering the Programme.</p>
        <h3>6.5 Legal and Regulatory Disclosure</h3>
        <p>We may disclose personal data to law enforcement, regulatory authorities, or courts where required by applicable law, or where necessary to protect the rights, safety, or property of SMS, Participants, or third parties.</p>
        <h3>6.6 Business Transfers</h3>
        <p>In the event of a merger, acquisition, restructuring, or sale of all or part of SMS's business, personal data may be transferred to the successor entity as part of that transaction, subject to equivalent privacy protections.</p>
        <p>We do not sell personal data to third parties for marketing purposes.</p>

        <h2 id="priv-transfer">7. International Data Transfers</h2>
        <p>SMS Training Academy operates internationally. Your personal data may be transferred to and processed in countries outside your country of residence, including the United States, Bangladesh, the UAE, and other countries where our service providers operate. Some of these countries may have data protection laws that differ from those in your jurisdiction.</p>
        <p>Where personal data is transferred outside the European Economic Area or United Kingdom, we ensure appropriate safeguards are in place, including: Standard Contractual Clauses approved by the European Commission, adequacy decisions where applicable, and equivalent transfer mechanisms recognised under applicable law.</p>

        <h2 id="priv-retention">8. Data Retention</h2>
        <p>We retain personal data only for as long as necessary for the purposes described in this Policy, or as required by applicable law. Our general retention guidelines are:</p>
        <ul>
            <li><strong>Training records, certificates, and assessment data:</strong> 7 years from the date of completion, to support verification, re-issuance, and regulatory audit requirements.</li>
            <li><strong>Payment and financial records:</strong> 7 years from the date of transaction, in compliance with US federal and state tax retention requirements.</li>
            <li><strong>Account data (active participants):</strong> For the duration of your account plus 2 years after your last login or activity.</li>
            <li><strong>Communication records:</strong> 3 years from the date of last communication.</li>
            <li><strong>Marketing consent records:</strong> Until you withdraw consent, plus a reasonable period thereafter.</li>
        </ul>
        <p>After the applicable retention period, personal data is securely deleted or anonymised.</p>

        <h2 id="priv-security">9. Data Security</h2>
        <p>We implement appropriate technical and organisational security measures to protect your personal data against unauthorised access, loss, destruction, alteration, or disclosure. These measures include: encrypted data transmission (TLS/SSL), secure password hashing, role-based access controls, regular security reviews, and contractual data security obligations with service providers.</p>
        <p>However, no method of data transmission or storage is entirely secure. While we take reasonable steps to protect your data, we cannot guarantee absolute security. If you have reason to believe your account has been compromised, please contact us immediately.</p>

        <h2 id="priv-cookies">10. Cookies &amp; Tracking Technologies</h2>
        <p>We use cookies and similar technologies to operate and improve the Platform. Categories of cookies used:</p>
        <ul>
            <li><strong>Essential cookies:</strong> Required for login sessions, security, and basic Platform functionality. Cannot be disabled without impacting Platform operation.</li>
            <li><strong>Performance cookies:</strong> Collect anonymised data on how visitors use the Platform, used to improve content and navigation. Enabled by default; may be opted out.</li>
            <li><strong>Analytics cookies:</strong> Used to understand usage patterns and measure the effectiveness of content and marketing campaigns (e.g., Google Analytics). Requires consent.</li>
            <li><strong>Marketing cookies:</strong> Used to deliver relevant advertisements and track campaign effectiveness. Requires consent.</li>
        </ul>
        <p>You may manage cookie preferences through your browser settings or our cookie consent tool where available. Note that disabling certain cookies may affect Platform functionality.</p>

        <h2 id="priv-rights">11. Your Rights</h2>
        <p>Depending on your location and applicable law, you may have the following rights regarding your personal data:</p>
        <div class="legal-rights-grid">
            <div class="legal-right-card">
                <h4>Right of Access</h4>
                <p>Request a copy of the personal data we hold about you and information on how it is processed.</p>
            </div>
            <div class="legal-right-card">
                <h4>Right of Rectification</h4>
                <p>Request correction of inaccurate or incomplete personal data held about you.</p>
            </div>
            <div class="legal-right-card">
                <h4>Right of Erasure</h4>
                <p>Request deletion of your personal data, subject to our legal obligations to retain certain records.</p>
            </div>
            <div class="legal-right-card">
                <h4>Right of Portability</h4>
                <p>Request a copy of data you provided to us in a commonly used, machine-readable format.</p>
            </div>
            <div class="legal-right-card">
                <h4>Right to Object</h4>
                <p>Object to processing based on legitimate interests or for direct marketing purposes.</p>
            </div>
            <div class="legal-right-card">
                <h4>Right to Restriction</h4>
                <p>Request that we restrict processing of your data in certain circumstances.</p>
            </div>
        </div>
        <p>To exercise any of these rights, please contact us at <a href="mailto:info@smscert.com" style="color:#1e3a8a;">info@smscert.com</a> or via our <a href="{{ route('public.contact') }}" style="color:#1e3a8a;">contact form</a>. We will respond to verified requests within 30 days (or within the timeframe required by applicable law). We may request identity verification before processing your request.</p>
        <p>If you are in the EEA or UK and are not satisfied with our response, you have the right to lodge a complaint with your local data protection supervisory authority.</p>
        <div class="legal-note">
            <p><strong>Marketing opt-out:</strong> If you have opted in to marketing communications and wish to unsubscribe, you may click the unsubscribe link in any marketing email or contact us directly. Opting out of marketing does not affect transactional or service communications related to your account or enrolled programmes.</p>
        </div>

        <h2 id="priv-children">12. Children's Privacy</h2>
        <p>The Platform is not directed at individuals under the age of 18. We do not knowingly collect personal data from children. If you believe that a child under 18 has provided personal data to us without appropriate parental consent, please contact us and we will take prompt steps to delete that information.</p>

        <h2 id="priv-changes">13. Changes to This Privacy Policy</h2>
        <p>We may update this Privacy Policy periodically to reflect changes in our data practices, services, or applicable law. We will notify registered Participants of material changes by email and/or by posting a notice on the Platform at least 14 days before changes take effect. The "Effective" date at the top of this Policy reflects the date of the most recent update.</p>

        <h2 id="priv-contact">14. Contact &amp; Privacy Enquiries</h2>
        <p>For questions, concerns, or requests relating to this Privacy Policy or your personal data, please contact:</p>
        <div style="background:#f8fafc;border:1.5px solid #e9ecf0;border-radius:12px;padding:20px;margin-top:10px;">
            <p style="margin:0 0 6px;"><strong>Sustainable Management System Inc. — Privacy</strong></p>
            <p style="margin:0 0 4px;color:#6b7280;">277 Cherry Street, Suite-12N, New York, NY, USA</p>
            <p style="margin:0 0 4px;"><a href="mailto:info@smscert.com" style="color:#1e3a8a;">info@smscert.com</a></p>
            <p style="margin:0;"><a href="{{ route('public.contact') }}" style="color:#1e3a8a;">Contact Form</a></p>
        </div>
        <p style="margin-top:20px;font-size:13px;color:#9ca3af;">This Privacy Policy was last updated in June 2026. Previous versions are available upon written request.</p>
    </div>

</div>
</div>
</div>
@endsection
