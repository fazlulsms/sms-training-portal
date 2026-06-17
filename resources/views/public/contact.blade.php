@extends('layouts.public')

@section('page-title', 'Contact SMS Training Academy')
@section('seo-title', 'Contact Us — SMS Training Academy')
@section('seo-desc', 'Get in touch with SMS Training Academy for training enquiries, enrolment support, payment assistance, or corporate programme development. Offices in New York, Bangladesh, and UAE.')
@section('seo-keys', 'contact SMS Training Academy, training enquiry, course enrolment support, corporate training inquiry, SMS training contact')

@section('content')
<style>
.ct-hero {
    background: linear-gradient(135deg, #060d2e 0%, #042C53 45%, #042C53 80%, #378ADD 100%);
    padding: 64px 0 72px; color: #fff; position: relative; overflow: hidden;
}
.ct-hero::after { content:''; position:absolute; inset:0; background-image:radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px); background-size:26px 26px; pointer-events:none; }
.ct-hero-inner { position:relative; z-index:1; text-align:center; }
.ct-eyebrow { display:inline-flex; align-items:center; gap:7px; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); padding:5px 14px; border-radius:20px; font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; margin-bottom:16px; color:rgba(255,255,255,.85); }
.ct-hero h1 { font-size:40px; font-weight:900; margin:0 0 12px; }
.ct-hero p  { font-size:16px; opacity:.72; max-width:600px; margin:0 auto; line-height:1.75; }
@media(max-width:640px){ .ct-hero h1 { font-size:28px; } }

/* Contact cards */
.ct-cards-sec { padding:56px 0 0; }
.ct-cards-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
@media(max-width:900px){ .ct-cards-grid { grid-template-columns:1fr 1fr; } }
@media(max-width:500px){ .ct-cards-grid { grid-template-columns:1fr; } }
.ct-card { background:#fff; border:1px solid #e9ecf0; border-radius:16px; padding:24px 20px; text-align:center; transition:box-shadow .15s; }
.ct-card:hover { box-shadow:0 8px 28px rgba(30,58,138,.09); border-color:#bfdbfe; }
.ct-card-icon { width:48px; height:48px; border-radius:14px; background:linear-gradient(135deg,#eff6ff,#dbeafe); display:flex; align-items:center; justify-content:center; margin:0 auto 14px; }
.ct-card h3 { font-size:14.5px; font-weight:800; color:#111827; margin:0 0 6px; }
.ct-card p  { font-size:13px; color:#6b7280; margin:0 0 14px; line-height:1.6; }
.ct-card-link { display:inline-block; font-size:13px; font-weight:700; color:#042C53; text-decoration:none; }
.ct-card-link:hover { text-decoration:underline; }

/* Main two-column layout */
.ct-main { padding:40px 0 72px; }
.ct-main-grid { display:grid; grid-template-columns:1fr 400px; gap:40px; align-items:start; }
@media(max-width:900px){ .ct-main-grid { grid-template-columns:1fr; } }

/* Form */
.ct-form-panel { background:#fff; border:1px solid #e9ecf0; border-radius:20px; padding:36px; }
.ct-form-panel h2 { font-size:22px; font-weight:900; color:#111827; margin:0 0 6px; }
.ct-form-panel > p { font-size:14px; color:#6b7280; margin:0 0 28px; }
.ct-form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media(max-width:580px){ .ct-form-row { grid-template-columns:1fr; } }
.ct-field { margin-bottom:18px; }
.ct-field label { display:block; font-size:12.5px; font-weight:700; color:#374151; margin-bottom:6px; }
.ct-field input, .ct-field select, .ct-field textarea {
    width:100%; padding:11px 14px; border:1.5px solid #d1d5db; border-radius:10px;
    font-size:14px; color:#111827; background:#fff;
    transition:border-color .15s, box-shadow .15s; box-sizing:border-box;
    font-family:inherit;
}
.ct-field input:focus, .ct-field select:focus, .ct-field textarea:focus { outline:none; border-color:#378ADD; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
.ct-field textarea { resize:vertical; min-height:130px; }
.ct-submit-btn { width:100%; padding:13px; background:linear-gradient(135deg,#042C53,#042C53); color:#fff; border:none; border-radius:11px; font-size:15px; font-weight:800; cursor:pointer; transition:opacity .13s; }
.ct-submit-btn:hover { opacity:.9; }

/* Success alert */
.ct-success { background:#f0fdf4; border:1.5px solid #86efac; border-radius:12px; padding:16px 20px; margin-bottom:24px; display:flex; align-items:flex-start; gap:12px; }
.ct-success svg { flex-shrink:0; color:#16a34a; margin-top:1px; }
.ct-success p { margin:0; font-size:14px; color:#15803d; font-weight:600; }

/* Sidebar */
.ct-sidebar { display:flex; flex-direction:column; gap:16px; }
.ct-info-panel { background:#fff; border:1px solid #e9ecf0; border-radius:16px; padding:24px; }
.ct-info-panel h3 { font-size:14.5px; font-weight:800; color:#111827; margin:0 0 16px; padding-bottom:10px; border-bottom:1.5px solid #f1f5f9; }
.ct-info-item { display:flex; align-items:flex-start; gap:12px; margin-bottom:16px; }
.ct-info-item:last-child { margin-bottom:0; }
.ct-info-icon { width:34px; height:34px; border-radius:9px; background:linear-gradient(135deg,#eff6ff,#dbeafe); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.ct-info-label { font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; margin:0 0 3px; }
.ct-info-val { font-size:13.5px; color:#111827; font-weight:600; margin:0; line-height:1.5; }

.ct-hours-panel { background:linear-gradient(135deg,#042C53,#042C53); border-radius:16px; padding:24px; color:#fff; }
.ct-hours-panel h3 { font-size:14.5px; font-weight:800; color:#fff; margin:0 0 16px; }
.ct-hour-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid rgba(255,255,255,.1); font-size:13px; }
.ct-hour-row:last-child { border-bottom:none; }
.ct-hour-day { color:rgba(255,255,255,.7); font-weight:600; }
.ct-hour-time { color:#fff; font-weight:700; }
.ct-hour-badge { background:rgba(255,255,255,.15); color:rgba(255,255,255,.8); font-size:11px; font-weight:700; padding:2px 9px; border-radius:20px; }

.ct-global-panel { background:#f8fafc; border:1px solid #e9ecf0; border-radius:16px; padding:20px; }
.ct-global-panel p { font-size:12.5px; color:#6b7280; margin:0; line-height:1.7; }
</style>

{{-- Hero --}}
<div class="ct-hero">
<div class="pub-container">
<div class="ct-hero-inner">
    <div class="ct-eyebrow">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Contact Us
    </div>
    <h1>We're Here to Help</h1>
    <p>Whether you have a question about a training programme, need enrolment support, or want to discuss a corporate training solution — our team is ready to assist.</p>
</div>
</div>
</div>

{{-- Contact type cards --}}
<div class="ct-cards-sec">
<div class="pub-container">
    <div class="ct-cards-grid">
        <div class="ct-card">
            <div class="ct-card-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <h3>General Enquiries</h3>
            <p>Questions about SMS Training Academy, our programmes, or the SMS Group.</p>
            <a href="mailto:info@smscert.com" class="ct-card-link">info@smscert.com</a>
        </div>
        <div class="ct-card">
            <div class="ct-card-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="1.8"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <h3>Training & Enrolment</h3>
            <p>Course recommendations, registration, scheduling, and post-enrolment support.</p>
            <a href="mailto:training@smscert.com" class="ct-card-link">training@smscert.com</a>
        </div>
        <div class="ct-card">
            <div class="ct-card-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <h3>Payment & Billing</h3>
            <p>Invoice requests, payment issues, refund enquiries, and billing assistance.</p>
            <a href="mailto:training@smscert.com" class="ct-card-link">training@smscert.com</a>
        </div>
        <div class="ct-card">
            <div class="ct-card-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="1.8"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
            </div>
            <h3>Corporate Programmes</h3>
            <p>Customised in-house training, group bookings, and long-term partnership enquiries.</p>
            <a href="mailto:info@smscert.com" class="ct-card-link">info@smscert.com</a>
        </div>
    </div>
</div>
</div>

{{-- Main: form + sidebar --}}
<div class="ct-main">
<div class="pub-container">
    <div class="ct-main-grid">

        {{-- Contact Form --}}
        <div class="ct-form-panel">
            <h2>Send Us a Message</h2>
            <p>Fill in the form below and a member of our team will respond within 1–2 business days.</p>

            @if(session('contact_success'))
            <div class="ct-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <p>{{ session('contact_success') }}</p>
            </div>
            @endif

            <form method="POST" action="{{ route('public.contact.submit') }}">
                @csrf
                <div class="ct-form-row">
                    <div class="ct-field">
                        <label for="name">Full Name <span style="color:#ef4444">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Your full name" required>
                        @error('name')<p style="font-size:12px;color:#ef4444;margin:4px 0 0">{{ $message }}</p>@enderror
                    </div>
                    <div class="ct-field">
                        <label for="email">Email Address <span style="color:#ef4444">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required>
                        @error('email')<p style="font-size:12px;color:#ef4444;margin:4px 0 0">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="ct-field">
                    <label for="phone">Phone / WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 (000) 000 0000 (optional)">
                </div>
                <div class="ct-field">
                    <label for="organisation">Organisation / Company</label>
                    <input type="text" id="organisation" name="organisation" value="{{ old('organisation') }}" placeholder="Your organisation name (optional)">
                </div>
                <div class="ct-field">
                    <label for="subject">Subject <span style="color:#ef4444">*</span></label>
                    <select id="subject" name="subject" required>
                        <option value="" disabled selected>Select a subject…</option>
                        <option value="Training Enquiry" {{ old('subject')=='Training Enquiry'?'selected':'' }}>Training Enquiry</option>
                        <option value="Enrolment Support" {{ old('subject')=='Enrolment Support'?'selected':'' }}>Enrolment Support</option>
                        <option value="Corporate Programme" {{ old('subject')=='Corporate Programme'?'selected':'' }}>Corporate Programme</option>
                        <option value="Payment / Billing" {{ old('subject')=='Payment / Billing'?'selected':'' }}>Payment / Billing</option>
                        <option value="Certificate Verification" {{ old('subject')=='Certificate Verification'?'selected':'' }}>Certificate Verification</option>
                        <option value="Refund Request" {{ old('subject')=='Refund Request'?'selected':'' }}>Refund Request</option>
                        <option value="Partnership / Franchise" {{ old('subject')=='Partnership / Franchise'?'selected':'' }}>Partnership / Franchise</option>
                        <option value="Other" {{ old('subject')=='Other'?'selected':'' }}>Other</option>
                    </select>
                    @error('subject')<p style="font-size:12px;color:#ef4444;margin:4px 0 0">{{ $message }}</p>@enderror
                </div>
                <div class="ct-field">
                    <label for="message">Message <span style="color:#ef4444">*</span></label>
                    <textarea id="message" name="message" placeholder="Please describe your enquiry in detail…" required>{{ old('message') }}</textarea>
                    @error('message')<p style="font-size:12px;color:#ef4444;margin:4px 0 0">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="ct-submit-btn">Send Message</button>
            </form>
        </div>

        {{-- Sidebar --}}
        <div class="ct-sidebar">
            <div class="ct-info-panel">
                <h3>Office Contacts</h3>
                <div class="ct-info-item">
                    <div class="ct-info-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div>
                        <p class="ct-info-label">Headquarters (USA)</p>
                        <p class="ct-info-val">277 Cherry Street, Suite-12N<br>New York, NY, USA</p>
                    </div>
                </div>
                <div class="ct-info-item">
                    <div class="ct-info-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div>
                        <p class="ct-info-label">Training Enquiries</p>
                        <p class="ct-info-val">training@smscert.com</p>
                    </div>
                </div>
                <div class="ct-info-item">
                    <div class="ct-info-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div>
                        <p class="ct-info-label">General</p>
                        <p class="ct-info-val">info@smscert.com</p>
                    </div>
                </div>
                <div class="ct-info-item">
                    <div class="ct-info-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div>
                        <p class="ct-info-label">South Asia</p>
                        <p class="ct-info-val">Sustainable Management System Bangladesh</p>
                    </div>
                </div>
                <div class="ct-info-item" style="margin-bottom:0">
                    <div class="ct-info-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#042C53" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div>
                        <p class="ct-info-label">Middle East</p>
                        <p class="ct-info-val">Sustainable Management System UAE</p>
                    </div>
                </div>
            </div>

            <div class="ct-hours-panel">
                <h3>Business Hours</h3>
                <div class="ct-hour-row"><span class="ct-hour-day">Monday – Thursday</span><span class="ct-hour-time">9:00 AM – 5:00 PM (EST)</span></div>
                <div class="ct-hour-row"><span class="ct-hour-day">Friday</span><span class="ct-hour-time">9:00 AM – 4:00 PM (EST)</span></div>
                <div class="ct-hour-row"><span class="ct-hour-day">Saturday</span><span class="ct-hour-badge">By Appointment</span></div>
                <div class="ct-hour-row"><span class="ct-hour-day">Sunday</span><span class="ct-hour-badge">Closed</span></div>
                <p style="font-size:12px;color:rgba(255,255,255,.55);margin:14px 0 0;line-height:1.6;">Response time: 1–2 business days. Urgent training support is monitored via email during business hours.</p>
            </div>

            <div class="ct-global-panel">
                <p><strong style="color:#374151;">Global Operations:</strong> SMS Training Academy operates internationally through Sustainable Management System Inc. (USA), Sustainable Management System Bangladesh, Sustainable Management System UAE, and an authorised franchise and partner network. Training programmes, certificates, and payment processing may be managed by the entity best placed to serve your region and currency. All enquiries to the contact details above are routed to the appropriate team.</p>
            </div>
        </div>

    </div>
</div>
</div>
@endsection
