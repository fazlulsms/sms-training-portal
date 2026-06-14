@extends('layouts.public')

@section('page-title', 'About SMS Training Academy')
@section('seo-title', 'About SMS Training Academy — International Professional Training')
@section('seo-desc', 'SMS Training Academy is the professional training and credential development division of Sustainable Management System (SMS) — delivering internationally recognised training across management systems, ESG, social compliance, health & safety, and professional development.')
@section('seo-keys', 'about SMS Training Academy, Sustainable Management System, professional training, ESG training, ISO training, social compliance, management systems, international training')

@section('content')
<style>
/* ── About page ── */
.abt-hero {
    background: linear-gradient(135deg, #060d2e 0%, #0f2470 40%, #1e3a8a 75%, #1d4ed8 100%);
    padding: 72px 0 80px; color: #fff; position: relative; overflow: hidden;
}
.abt-hero::after {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.045) 1px, transparent 1px);
    background-size: 28px 28px; pointer-events: none;
}
.abt-hero-inner { position: relative; z-index: 1; }
.abt-eyebrow {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
    padding: 5px 14px; border-radius: 20px;
    font-size: 11.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px;
    margin-bottom: 18px; color: rgba(255,255,255,.85);
}
.abt-hero h1 { font-size: 44px; font-weight: 900; margin: 0 0 16px; line-height: 1.15; max-width: 680px; }
@media(max-width:640px){ .abt-hero h1 { font-size:30px; } }
.abt-hero p { font-size: 16.5px; opacity: .75; margin: 0 0 32px; line-height: 1.75; max-width: 620px; }
.abt-hero-stats { display: flex; gap: 36px; flex-wrap: wrap; }
.abt-stat { }
.abt-stat-val { font-size: 30px; font-weight: 900; color: #fff; line-height: 1; }
.abt-stat-label { font-size: 12px; opacity: .6; font-weight: 600; margin-top: 3px; }

/* ── Section headers ── */
.abt-sec-hd { text-align: center; margin-bottom: 40px; }
.abt-sec-eyebrow { display: inline-block; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .9px; color: #1e3a8a; background: #eff6ff; border: 1px solid #bfdbfe; padding: 4px 14px; border-radius: 20px; margin-bottom: 10px; }
.abt-sec-title { font-size: 28px; font-weight: 900; color: #111827; margin: 0 0 10px; }
.abt-sec-sub   { font-size: 15px; color: #6b7280; margin: 0; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.7; }

/* ── Intro section ── */
.abt-intro { padding: 64px 0; }
.abt-intro-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: start; }
@media(max-width:800px){ .abt-intro-grid { grid-template-columns: 1fr; gap: 40px; } }
.abt-intro-lead { font-size: 17px; font-weight: 700; color: #111827; line-height: 1.7; margin: 0 0 18px; }
.abt-intro-body { font-size: 15px; color: #374151; line-height: 1.85; margin: 0 0 14px; }
.abt-intro-body:last-child { margin-bottom: 0; }

/* Mission / Vision */
.abt-mv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media(max-width:500px){ .abt-mv-grid { grid-template-columns: 1fr; } }
.abt-mv-card { border-radius: 16px; padding: 24px; }
.abt-mv-card.mission { background: linear-gradient(135deg, #0f2470, #1e3a8a); color: #fff; }
.abt-mv-card.vision  { background: #f0f4ff; border: 1px solid #bfdbfe; color: #111827; }
.abt-mv-icon { width: 40px; height: 40px; border-radius: 11px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
.abt-mv-card.mission .abt-mv-icon { background: rgba(255,255,255,.15); }
.abt-mv-card.vision  .abt-mv-icon { background: rgba(30,58,138,.12); }
.abt-mv-card h3 { font-size: 15px; font-weight: 900; margin: 0 0 8px; }
.abt-mv-card.mission h3 { color: #fff; }
.abt-mv-card.vision  h3 { color: #1e3a8a; }
.abt-mv-card p { font-size: 13.5px; line-height: 1.7; margin: 0; }
.abt-mv-card.mission p { color: rgba(255,255,255,.8); }
.abt-mv-card.vision  p { color: #374151; }

/* ── SMS Group section ── */
.abt-group { background: #f8fafc; padding: 64px 0; }
.abt-group-body { font-size: 15px; color: #374151; line-height: 1.85; margin: 0 0 40px; }
.abt-entities { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 40px; }
@media(max-width:700px){ .abt-entities { grid-template-columns: 1fr; } }
.abt-entity {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 14px;
    padding: 22px; border-top: 3px solid #1e3a8a;
}
.abt-entity-flag { font-size: 22px; margin-bottom: 10px; }
.abt-entity h4 { font-size: 14.5px; font-weight: 800; color: #111827; margin: 0 0 6px; }
.abt-entity p  { font-size: 13px; color: #6b7280; margin: 0; line-height: 1.6; }
.abt-services-grid { display: flex; flex-wrap: wrap; gap: 8px; }
.abt-service-pill {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 20px;
    padding: 6px 16px; font-size: 13px; font-weight: 600; color: #374151;
    display: flex; align-items: center; gap: 6px;
}
.abt-service-pill svg { color: #1e3a8a; flex-shrink: 0; }

/* ── Delivery methods ── */
.abt-methods { padding: 64px 0; }
.abt-methods-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
@media(max-width:800px){ .abt-methods-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width:480px){ .abt-methods-grid { grid-template-columns: 1fr; } }
.abt-method {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 16px;
    padding: 24px 20px; text-align: center;
    transition: box-shadow .15s, border-color .15s;
}
.abt-method:hover { box-shadow: 0 8px 28px rgba(30,58,138,.09); border-color: #bfdbfe; }
.abt-method-icon {
    width: 52px; height: 52px; border-radius: 14px; margin: 0 auto 16px;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    display: flex; align-items: center; justify-content: center;
}
.abt-method h3 { font-size: 15px; font-weight: 800; color: #111827; margin: 0 0 8px; }
.abt-method p  { font-size: 13px; color: #6b7280; margin: 0; line-height: 1.65; }

/* ── Training categories ── */
.abt-cats { background: #f8fafc; padding: 64px 0; }
.abt-cats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
@media(max-width:700px){ .abt-cats-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width:420px){ .abt-cats-grid { grid-template-columns: 1fr; } }
.abt-cat {
    background: #fff; border: 1px solid #e9ecf0; border-radius: 14px;
    padding: 20px; display: flex; align-items: flex-start; gap: 14px;
    transition: box-shadow .15s; cursor: default;
}
.abt-cat:hover { box-shadow: 0 6px 24px rgba(0,0,0,.07); }
.abt-cat-icon {
    width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
}
.abt-cat h4 { font-size: 13.5px; font-weight: 800; color: #111827; margin: 0 0 4px; }
.abt-cat p  { font-size: 12px; color: #6b7280; margin: 0; line-height: 1.5; }

/* ── Values / Why SMS ── */
.abt-values { padding: 64px 0; }
.abt-values-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
@media(max-width:700px){ .abt-values-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width:420px){ .abt-values-grid { grid-template-columns: 1fr; } }
.abt-value { text-align: center; padding: 8px 4px; }
.abt-value-icon { width: 48px; height: 48px; border-radius: 14px; margin: 0 auto 14px; background: linear-gradient(135deg, #0f2470, #1e3a8a); display: flex; align-items: center; justify-content: center; }
.abt-value h4 { font-size: 14px; font-weight: 800; color: #111827; margin: 0 0 7px; }
.abt-value p  { font-size: 13px; color: #6b7280; margin: 0; line-height: 1.65; }

/* ── CTA ── */
.abt-cta { background: linear-gradient(135deg, #0a1854, #1e3a8a, #2563eb); padding: 56px 0; text-align: center; position: relative; overflow: hidden; }
.abt-cta::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px); background-size: 22px 22px; }
.abt-cta-inner { position: relative; z-index: 1; }
.abt-cta h2 { font-size: 30px; font-weight: 900; color: #fff; margin: 0 0 10px; }
.abt-cta p  { font-size: 16px; color: rgba(255,255,255,.7); margin: 0 0 28px; }
.abt-cta-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.abt-cta-btn-primary   { background: #fff; color: #0f2470; padding: 13px 26px; border-radius: 11px; font-weight: 900; font-size: 14.5px; text-decoration: none; transition: opacity .13s; }
.abt-cta-btn-primary:hover { opacity: .92; }
.abt-cta-btn-ghost { background: rgba(255,255,255,.1); color: #fff; border: 1px solid rgba(255,255,255,.25); padding: 13px 26px; border-radius: 11px; font-weight: 700; font-size: 14.5px; text-decoration: none; transition: background .13s; }
.abt-cta-btn-ghost:hover { background: rgba(255,255,255,.18); }
</style>

{{-- Hero --}}
<div class="abt-hero">
<div class="pub-container">
<div class="abt-hero-inner">
    <div class="abt-eyebrow">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        About Us
    </div>
    <h1>Building Professional Competence for a Sustainable World</h1>
    <p>SMS Training Academy delivers internationally recognised professional training, eLearning, and certification programs across management systems, sustainability, compliance, and professional development — trusted by organisations in over 35 countries.</p>
    <div class="abt-hero-stats">
        <div class="abt-stat"><div class="abt-stat-val">25,000+</div><div class="abt-stat-label">Certificates Issued</div></div>
        <div class="abt-stat"><div class="abt-stat-val">120+</div><div class="abt-stat-label">Training Programmes</div></div>
        <div class="abt-stat"><div class="abt-stat-val">35+</div><div class="abt-stat-label">Countries Reached</div></div>
        <div class="abt-stat"><div class="abt-stat-val">10+</div><div class="abt-stat-label">Years of Excellence</div></div>
    </div>
</div>
</div>
</div>

{{-- Introduction --}}
<div class="abt-intro">
<div class="pub-container">
    <div class="abt-intro-grid">
        <div>
            <p class="abt-intro-lead">SMS Training Academy is the professional training and credential development division of Sustainable Management System (SMS) — an international professional services organisation dedicated to advancing responsible business, environmental stewardship, and social accountability.</p>
            <p class="abt-intro-body">We design and deliver training programs that build real-world professional competence across management systems, sustainability, social compliance, occupational health and safety, ESG reporting, supply chain accountability, and auditor development. Our programs are structured for working professionals, practitioners, and organisations seeking internationally credible credentials and practical skills they can apply immediately.</p>
            <p class="abt-intro-body">Every program we offer reflects the standards, frameworks, and international expectations that our participants will encounter in their professional roles — from ISO management system requirements to APSCA-aligned social compliance auditing, Higg Index assessments, ESG reporting frameworks, and NEBOSH-aligned health and safety principles.</p>
        </div>
        <div>
            <div class="abt-mv-grid">
                <div class="abt-mv-card mission">
                    <div class="abt-mv-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h3>Our Mission</h3>
                    <p>To advance professional competence, ethical business practice, and sustainable development by delivering accessible, credible, and internationally relevant training for individuals and organisations worldwide.</p>
                </div>
                <div class="abt-mv-card vision">
                    <div class="abt-mv-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </div>
                    <h3>Our Vision</h3>
                    <p>To be the most trusted international training platform for sustainability, compliance, and management systems — empowering professionals in every region to drive measurable positive change in their organisations and supply chains.</p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- SMS Group / Ecosystem --}}
<div class="abt-group">
<div class="pub-container">
    <div class="abt-sec-hd">
        <div class="abt-sec-eyebrow">The SMS Group</div>
        <div class="abt-sec-title">Part of the Sustainable Management System Network</div>
        <div class="abt-sec-sub">SMS Training Academy operates as the professional training and learning division within the wider Sustainable Management System (SMS) global network.</div>
    </div>
    <p class="abt-group-body">Sustainable Management System (SMS) is an international professional services group providing consulting, auditing, verification, certification support, and capacity building services to businesses, brands, factories, and supply chains worldwide. SMS supports clients in meeting international standards across environmental management, social compliance, labour rights, occupational health and safety, quality management, ESG, and corporate sustainability.<br><br>SMS Training Academy was established to provide structured, credentialed training that complements and strengthens this wider professional services ecosystem — ensuring that practitioners, auditors, compliance managers, HR professionals, and sustainability officers have access to world-class learning opportunities without barriers of geography or scale.</p>

    <div class="abt-entities">
        <div class="abt-entity">
            <div class="abt-entity-flag">🇺🇸</div>
            <h4>Sustainable Management System Inc.</h4>
            <p>Incorporated in the State of New York, USA. The principal operating entity and headquarters of the SMS group, managing global training programs, international partnerships, and corporate client engagements.</p>
        </div>
        <div class="abt-entity">
            <div class="abt-entity-flag">🇧🇩</div>
            <h4>Sustainable Management System Bangladesh</h4>
            <p>Operational office serving South and South-East Asian markets, with particular expertise in RMG sector compliance training, supply chain auditing, and factory-level capacity building programs.</p>
        </div>
        <div class="abt-entity">
            <div class="abt-entity-flag">🇦🇪</div>
            <h4>Sustainable Management System UAE</h4>
            <p>Middle East regional operations, serving clients across the GCC and broader MENA region, supporting training programs aligned with UAE and regional regulatory frameworks.</p>
        </div>
    </div>

    <div style="background:#fff;border:1px solid #e9ecf0;border-radius:16px;padding:24px;">
        <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;margin-bottom:14px;">SMS Professional Services — Beyond Training</div>
        <div class="abt-services-grid">
            @foreach(['Management Systems Consulting','Social Compliance Auditing','ESG Advisory','Supply Chain Verification','Certification Preparation Support','Occupational Health & Safety','Environmental Management','Corporate Sustainability','Auditor Development','Factory & Supplier Assessments'] as $svc)
            <span class="abt-service-pill">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $svc }}
            </span>
            @endforeach
        </div>
    </div>
</div>
</div>

{{-- Delivery Methods --}}
<div class="abt-methods">
<div class="pub-container">
    <div class="abt-sec-hd">
        <div class="abt-sec-eyebrow">Training Delivery</div>
        <div class="abt-sec-title">Flexible Learning Formats for Every Professional</div>
        <div class="abt-sec-sub">We deliver training in formats designed to accommodate the schedules, locations, and learning preferences of working professionals worldwide.</div>
    </div>
    <div class="abt-methods-grid">
        <div class="abt-method">
            <div class="abt-method-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h3>Instructor-Led Training</h3>
            <p>Classroom-based programs delivered by subject-matter experts at scheduled venues across our operating regions. Ideal for immersive, interactive learning and professional networking.</p>
        </div>
        <div class="abt-method">
            <div class="abt-method-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <h3>Virtual Training (VILT)</h3>
            <p>Live, instructor-led sessions delivered via secure video platforms — providing the same structured learning and interaction as in-person training, accessible from anywhere in the world.</p>
        </div>
        <div class="abt-method">
            <div class="abt-method-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <h3>Self-Paced eLearning</h3>
            <p>Structured online courses accessible 24/7 through our learning management system. Participants progress at their own pace with assessments, knowledge checks, and digital certificates.</p>
        </div>
        <div class="abt-method">
            <div class="abt-method-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
            </div>
            <h3>Corporate Programs</h3>
            <p>Customised in-house training programs designed for organisations seeking to build capability across teams — delivered on-site, virtually, or as blended programs tailored to specific business needs.</p>
        </div>
    </div>
</div>
</div>

{{-- Training Categories --}}
<div class="abt-cats">
<div class="pub-container">
    <div class="abt-sec-hd">
        <div class="abt-sec-eyebrow">Subject Areas</div>
        <div class="abt-sec-title">What We Teach</div>
        <div class="abt-sec-sub">Our training portfolio spans the disciplines most critical to responsible business operations and internationally recognised professional credentials.</div>
    </div>
    <div class="abt-cats-grid">
        @php
        $categories = [
            ['icon'=>'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>', 'title'=>'Environmental Management', 'desc'=>'ISO 14001, environmental auditing, EMS implementation, carbon management, and sustainability reporting.'],
            ['icon'=>'<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>', 'title'=>'Occupational Health & Safety', 'desc'=>'ISO 45001, NEBOSH-aligned programs, risk assessment, workplace safety management, and incident investigation.'],
            ['icon'=>'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/>', 'title'=>'Social Compliance', 'desc'=>'APSCA-aligned auditor training, SLCP, social auditing fundamentals, labour rights, and ethical trade compliance.'],
            ['icon'=>'<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>', 'title'=>'ESG & Sustainability', 'desc'=>'ESG reporting frameworks, GRI Standards, Higg Index, sustainability strategy, and stakeholder reporting.'],
            ['icon'=>'<rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/>', 'title'=>'Quality Management', 'desc'=>'ISO 9001, quality auditing, QMS implementation, process improvement, and customer focus principles.'],
            ['icon'=>'<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>', 'title'=>'Supply Chain Sustainability', 'desc'=>'Responsible sourcing, supplier due diligence, supply chain risk, Higg FEM, and supply chain transparency.'],
            ['icon'=>'<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>', 'title'=>'Management Systems (ISO)', 'desc'=>'Multi-standard management systems, integrated IMS, ISO internal auditor and lead auditor programs.'],
            ['icon'=>'<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>', 'title'=>'Auditor Development', 'desc'=>'Professional auditor skills, auditing techniques, APSCA registered auditor pathways, and audit reporting.'],
            ['icon'=>'<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>', 'title'=>'Professional Development', 'desc'=>'Leadership, communication, HR management, business ethics, and cross-functional professional skills programs.'],
        ];
        @endphp
        @foreach($categories as $cat)
        <div class="abt-cat">
            <div class="abt-cat-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8">{!! $cat['icon'] !!}</svg>
            </div>
            <div>
                <h4>{{ $cat['title'] }}</h4>
                <p>{{ $cat['desc'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
</div>

{{-- Values --}}
<div class="abt-values">
<div class="pub-container">
    <div class="abt-sec-hd">
        <div class="abt-sec-eyebrow">Why SMS Training Academy</div>
        <div class="abt-sec-title">Our Commitment to You</div>
        <div class="abt-sec-sub">We are committed to delivering learning experiences that meet the highest professional standards — not just in content, but in delivery, support, and outcomes.</div>
    </div>
    <div class="abt-values-grid">
        @php $values = [
            ['icon'=>'<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>','title'=>'Internationally Recognised Certificates','desc'=>'Every certificate is registered in our secure digital registry and verifiable online — credible to employers, auditors, and certification bodies worldwide.'],
            ['icon'=>'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>','title'=>'Expert Instructors','desc'=>'Our trainers are practicing professionals with real-world experience in auditing, consulting, and management systems — not just academic instructors.'],
            ['icon'=>'<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10"/>','title'=>'Global Reach, Local Relevance','desc'=>'Operating across three continents with content adapted to regional regulatory environments, industry structures, and professional standards.'],
            ['icon'=>'<rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/>','title'=>'Practitioner-Focused Content','desc'=>'Every program is built around practical, applied skills. Participants leave with knowledge they can deploy in their roles from day one.'],
            ['icon'=>'<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>','title'=>'Continuous Programme Development','desc'=>'Our training content is regularly reviewed and updated to reflect evolving international standards, regulatory changes, and industry best practice.'],
            ['icon'=>'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>','title'=>'Aligned with Global Frameworks','desc'=>'Our programmes reference ISO standards, APSCA guidelines, GRI frameworks, Higg Index, SLCP, and other internationally recognised benchmarks.'],
        ]; @endphp
        @foreach($values as $v)
        <div class="abt-value">
            <div class="abt-value-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.88)" stroke-width="1.8">{!! $v['icon'] !!}</svg>
            </div>
            <h4>{{ $v['title'] }}</h4>
            <p>{{ $v['desc'] }}</p>
        </div>
        @endforeach
    </div>
</div>
</div>

{{-- CTA --}}
<div class="abt-cta">
<div class="pub-container">
<div class="abt-cta-inner">
    <h2>Start Your Professional Development Journey</h2>
    <p>Browse our full training catalogue and find the programme that fits your career goals and schedule.</p>
    <div class="abt-cta-btns">
        <a href="{{ route('public.courses') }}" class="abt-cta-btn-primary">Browse Courses</a>
        <a href="{{ route('public.contact') }}" class="abt-cta-btn-ghost">Contact Us</a>
    </div>
</div>
</div>
</div>
@endsection
