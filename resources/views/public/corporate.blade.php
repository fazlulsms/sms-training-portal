@extends('layouts.public')

@section('page-title', 'Corporate Training Solutions')
@section('seo-title', 'Corporate Training Programs – SMS Training Services')
@section('seo-desc', 'Customized corporate training programs for organizations. ISO standards, quality management, compliance, and professional development for your team.')

@section('content')
<style>
.corp-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #1d4ed8 100%);
    padding: 72px 0 80px; color: #fff;
}
.corp-hero-inner { max-width: 1100px; margin: 0 auto; padding: 0 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: center; }
@media (max-width: 860px) { .corp-hero-inner { grid-template-columns: 1fr; } .corp-hero-form { display: none; } }
.corp-hero h1 { font-size: 40px; font-weight: 900; line-height: 1.2; margin: 0 0 16px; }
.corp-hero p { font-size: 16px; opacity: .82; line-height: 1.75; margin: 0 0 28px; }
.benefit-list { list-style: none; margin: 0; padding: 0; }
.benefit-list li { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px; font-size: 14.5px; opacity: .9; }
.benefit-list li:before { content: '✓'; background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3); border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 900; flex-shrink: 0; margin-top: 1px; }
.corp-form-card { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.15); border-radius: 20px; padding: 28px; backdrop-filter: blur(8px); }
.corp-form-card h3 { font-size: 18px; font-weight: 800; margin: 0 0 20px; }
.section-wrap { max-width: 1100px; margin: 0 auto; padding: 0 24px; }
.why-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px; padding: 56px 0; }
.why-card { background: #fff; border-radius: 14px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.why-icon { font-size: 36px; margin-bottom: 12px; }
.why-title { font-size: 16px; font-weight: 800; color: #111827; margin: 0 0 8px; }
.why-text { font-size: 13.5px; color: #6b7280; line-height: 1.65; margin: 0; }
.form-section { background: #f8fafc; padding: 60px 0; }
.form-card { background: #fff; border-radius: 18px; box-shadow: 0 6px 28px rgba(0,0,0,.08); padding: 40px; max-width: 780px; margin: 0 auto; }
.form-card h2 { font-size: 26px; font-weight: 900; color: #111827; margin: 0 0 8px; }
.form-card .sub { color: #6b7280; font-size: 15px; margin: 0 0 28px; }
.fg { margin-bottom:18px; }
.fg label { display:block; font-weight:600; font-size:13.5px; color:#374151; margin-bottom:6px; }
.fg input,.fg select,.fg textarea { width:100%; padding:11px 14px; border:1.5px solid #d1d5db; border-radius:9px; font-size:14px; font-family:inherit; outline:none; box-sizing:border-box; }
.fg input:focus,.fg select:focus,.fg textarea:focus { border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.08); }
.frow { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
@media (max-width:600px) { .frow { grid-template-columns:1fr; } }
.submit-btn { width:100%; background:#1e3a8a; color:#fff; padding:15px; border:none; border-radius:10px; font-weight:800; font-size:16px; cursor:pointer; transition:background .15s; }
.submit-btn:hover { background:#1e40af; }
/* Mobile form */
.mobile-cta { display: none; text-align: center; padding: 40px 24px; background: #f0f4ff; }
@media (max-width: 860px) { .mobile-cta { display: block; } }
</style>

{{-- Hero --}}
<section class="corp-hero">
    <div class="corp-hero-inner">
        <div>
            <div style="display:inline-flex; align-items:center; gap:7px; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); padding:5px 14px; border-radius:20px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; margin-bottom:16px;">
                🏢 Corporate Training
            </div>
            <h1>Customized Training for Your Organization</h1>
            <p>We design and deliver tailored training programs that align with your organization's specific goals, industry requirements, and compliance standards.</p>
            <ul class="benefit-list">
                <li>Fully customized curriculum for your industry</li>
                <li>Delivered at your premises or online</li>
                <li>Internationally certified trainers</li>
                <li>Group rates and flexible scheduling</li>
                <li>Post-training assessment and certificates</li>
            </ul>
            <a href="#inquiry-form" style="display:inline-flex; align-items:center; gap:8px; background:#fff; color:#1e3a8a; padding:14px 28px; border-radius:12px; font-weight:800; font-size:15px; text-decoration:none; margin-top:8px; box-shadow:0 6px 20px rgba(0,0,0,.2);">
                Submit an Inquiry ↓
            </a>
        </div>
        <div class="corp-hero-form">
            <div class="corp-form-card">
                <h3>Quick Inquiry</h3>
                <form method="POST" action="{{ route('public.corporate.submit') }}">
                    @csrf
                    <div class="fg" style="margin-bottom:12px;">
                        <input type="text" name="company_name" placeholder="Company name *" required style="background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.2); color:#fff;">
                    </div>
                    <div class="fg" style="margin-bottom:12px;">
                        <input type="text" name="contact_person" placeholder="Your name *" required style="background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.2); color:#fff;">
                    </div>
                    <div class="fg" style="margin-bottom:12px;">
                        <input type="email" name="email" placeholder="Email address *" required style="background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.2); color:#fff;">
                    </div>
                    <div class="fg" style="margin-bottom:16px;">
                        <textarea name="training_requirement" rows="3" placeholder="Training requirement *" required style="background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.2); color:#fff; resize:vertical;"></textarea>
                    </div>
                    <button type="submit" style="width:100%; background:#fff; color:#1e3a8a; padding:12px; border:none; border-radius:9px; font-weight:800; font-size:15px; cursor:pointer;">
                        Send Inquiry →
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- Mobile CTA --}}
<div class="mobile-cta">
    <p style="font-size:16px; color:#374151; margin:0 0 14px;">Ready to upskill your team?</p>
    <a href="#inquiry-form" style="background:#1e3a8a; color:#fff; padding:13px 28px; border-radius:10px; font-weight:700; text-decoration:none; font-size:15px;">Submit an Inquiry</a>
</div>

{{-- Why Choose Us --}}
<section>
    <div class="section-wrap">
        <div style="text-align:center; margin-bottom:12px; padding-top:56px;">
            <h2 style="font-size:30px; font-weight:900; color:#111827; margin:0 0 10px;">Why Choose SMS Training?</h2>
            <p style="font-size:16px; color:#6b7280; max-width:560px; margin:0 auto; line-height:1.7;">We've delivered corporate training to hundreds of organizations across Bangladesh and the region.</p>
        </div>
        <div class="why-grid">
            <div class="why-card">
                <div class="why-icon">🎓</div>
                <h4 class="why-title">Certified Expert Trainers</h4>
                <p class="why-text">Our trainers hold international certifications from IRCA, BSI, NEBOSH, and other globally recognized bodies.</p>
            </div>
            <div class="why-card">
                <div class="why-icon">🎯</div>
                <h4 class="why-title">Tailored Curriculum</h4>
                <p class="why-text">Every corporate program is customized to your industry, team size, and specific training objectives.</p>
            </div>
            <div class="why-card">
                <div class="why-icon">📍</div>
                <h4 class="why-title">Flexible Delivery</h4>
                <p class="why-text">Training can be delivered at your premises, at our facility, or online — wherever is most convenient for your team.</p>
            </div>
            <div class="why-card">
                <div class="why-icon">📜</div>
                <h4 class="why-title">Recognized Certificates</h4>
                <p class="why-text">Participants receive certificates recognized by industry bodies and regulatory authorities upon successful completion.</p>
            </div>
            <div class="why-card">
                <div class="why-icon">📊</div>
                <h4 class="why-title">Assessment & Reporting</h4>
                <p class="why-text">We provide pre and post assessments, attendance records, and detailed reports for your HR and compliance teams.</p>
            </div>
            <div class="why-card">
                <div class="why-icon">💰</div>
                <h4 class="why-title">Group Pricing</h4>
                <p class="why-text">Competitive group rates with significant savings compared to public schedule enrollment.</p>
            </div>
        </div>
    </div>
</section>

{{-- Inquiry Form --}}
<section class="form-section" id="inquiry-form">
    <div class="section-wrap">
        <div class="form-card">
            <h2>Request Corporate Training</h2>
            <p class="sub">Fill in your requirements and our team will respond within 24 hours with a proposal.</p>

            @if(session('success'))
            <div style="background:#dcfce7; border:1px solid #86efac; border-radius:10px; padding:16px 20px; margin-bottom:24px; color:#166534; font-weight:600;">
                ✓ {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:10px; padding:16px 20px; margin-bottom:24px;">
                <ul style="margin:0; padding-left:18px; color:#b91c1c; font-size:13.5px;">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('public.corporate.submit') }}">
                @csrf
                <div class="frow">
                    <div class="fg">
                        <label>Company Name <span style="color:red">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" required>
                    </div>
                    <div class="fg">
                        <label>Contact Person <span style="color:red">*</span></label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}" required>
                    </div>
                </div>
                <div class="frow">
                    <div class="fg">
                        <label>Email Address <span style="color:red">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="fg">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}">
                    </div>
                </div>
                <div class="frow">
                    <div class="fg">
                        <label>Country</label>
                        <input type="text" name="country" value="{{ old('country', 'Bangladesh') }}">
                    </div>
                    <div class="fg">
                        <label>Number of Participants</label>
                        <input type="number" name="participants_count" value="{{ old('participants_count') }}" min="1" placeholder="e.g. 20">
                    </div>
                </div>
                <div class="frow">
                    <div class="fg">
                        <label>Preferred Start Date</label>
                        <input type="date" name="preferred_date" value="{{ old('preferred_date') }}">
                    </div>
                    <div class="fg">
                        <label>Preferred Mode</label>
                        <select name="preferred_mode">
                            <option value="Physical" {{ old('preferred_mode')=='Physical'?'selected':'' }}>Physical (at your premises or our facility)</option>
                            <option value="Online" {{ old('preferred_mode')=='Online'?'selected':'' }}>Online / Virtual</option>
                            <option value="Hybrid" {{ old('preferred_mode')=='Hybrid'?'selected':'' }}>Hybrid</option>
                        </select>
                    </div>
                </div>
                <div class="fg">
                    <label>Training Requirement <span style="color:red">*</span></label>
                    <textarea name="training_requirement" rows="4" required placeholder="Describe the training topics, standards (e.g. ISO 9001, NEBOSH), or skills you need covered...">{{ old('training_requirement') }}</textarea>
                </div>
                <div class="fg">
                    <label>Additional Message</label>
                    <textarea name="message" rows="3" placeholder="Any other details, constraints, or questions...">{{ old('message') }}</textarea>
                </div>
                <button type="submit" class="submit-btn">Submit Inquiry →</button>
            </form>
        </div>
    </div>
</section>
@endsection
