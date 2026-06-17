<style>
/* ── Registration shared styles ── */
.reg-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #0f766e 100%);
    padding: 48px 0 56px; color: #fff; position: relative; overflow: hidden;
}
.reg-hero::before {
    content: ''; position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");
}
.reg-hero-inner { position: relative; }
.reg-breadcrumb { font-size: 13px; color: rgba(255,255,255,.55); display: flex; align-items: center; gap: 6px; margin-bottom: 14px; flex-wrap: wrap; }
.reg-breadcrumb a { color: rgba(255,255,255,.7); text-decoration: none; transition: color .15s; }
.reg-breadcrumb a:hover { color: #fff; }
.reg-breadcrumb span { opacity: .4; }
.reg-hero-type { font-size: 11.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px; color: rgba(255,255,255,.55); margin-bottom: 10px; }
.reg-hero h1 { font-size: 30px; font-weight: 900; margin: 0 0 14px; line-height: 1.25; }
@media(max-width:768px){ .reg-hero h1 { font-size: 22px; } }
.reg-hero-badges { display: flex; flex-wrap: wrap; gap: 10px; }
.reg-hero-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 13px; border-radius: 20px;
    background: rgba(255,255,255,.12); color: rgba(255,255,255,.9);
    font-size: 12px; font-weight: 600; border: 1px solid rgba(255,255,255,.15);
}

/* Layout */
.reg-body { display: grid; grid-template-columns: 1fr 360px; gap: 32px; padding: 40px 0 64px; align-items: start; }
@media(max-width:920px) { .reg-body { grid-template-columns: 1fr; } .reg-sidebar { order: -1; } }
.reg-main {}

/* Cards */
.reg-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
    padding: 24px 28px; margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(15,23,42,.05);
}
.reg-card-title {
    font-size: 15px; font-weight: 800; color: #111827;
    margin: 0 0 20px; display: flex; align-items: center; gap: 10px;
}
.reg-card-num {
    width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg,#1e3a8a,#2563eb); color: #fff;
    font-size: 12px; font-weight: 900;
    display: flex; align-items: center; justify-content: center;
}

/* Form grid */
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media(max-width:600px){ .form-grid-2 { grid-template-columns: 1fr; } }
.fg { display: flex; flex-direction: column; gap: 5px; }
.fg.full { grid-column: 1 / -1; }
.fl { font-size: 12px; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: .4px; }
.req { color: #dc2626; margin-left: 2px; }
.fi {
    padding: 10px 13px; border: 1.5px solid #e5e7eb; border-radius: 9px;
    font-size: 14px; font-family: inherit; color: #111827; background: #fff;
    width: 100%; transition: border-color .15s, box-shadow .15s;
}
.fi:focus { outline: none; border-color: #1e3a8a; box-shadow: 0 0 0 3px rgba(30,58,138,.1); }
.fi.is-err { border-color: #ef4444; }
textarea.fi { resize: vertical; }
.fe { color: #dc2626; font-size: 11.5px; }
.fh { color: #9ca3af; font-size: 11.5px; }

/* Mode selector cards (ILT) */
.mode-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media(max-width:420px){ .mode-cards { grid-template-columns: 1fr; } }
.mode-card-label { display: block; position: relative; cursor: pointer; }
.mode-card-label input[type=radio] { position: absolute; opacity: 0; width: 0; height: 0; }
.mode-card {
    border: 2px solid #e5e7eb; border-radius: 12px; padding: 18px;
    transition: all .14s; text-align: center;
}
.mode-card-label input:checked + .mode-card { border-color: #1e3a8a; background: #f0f4ff; }
.mode-card-icon { font-size: 30px; margin-bottom: 8px; }
.mode-card-name { font-size: 14px; font-weight: 800; color: #111827; }
.mode-card-desc { font-size: 12px; color: #6b7280; margin-top: 3px; }
.mode-card-fee  { font-size: 14px; font-weight: 800; color: #1e3a8a; margin-top: 6px; }

/* Fee summary */
.fee-summary {
    background: linear-gradient(135deg,#1e3a8a,#2563eb);
    border-radius: 14px; padding: 22px; color: #fff; margin-bottom: 20px;
}
.fee-row { display: flex; justify-content: space-between; align-items: baseline; font-size: 14px; padding: 7px 0; border-bottom: 1px solid rgba(255,255,255,.15); }
.fee-row:last-child { border-bottom: none; font-size: 18px; font-weight: 900; padding-top: 14px; }
.fee-label { opacity: .8; }
.fee-value { font-weight: 700; }

/* Payment info box */
.payment-info-box {
    background: #fffbeb; border: 1px solid #fcd34d;
    border-radius: 12px; padding: 18px 20px; margin-bottom: 20px;
}
.payment-info-title {
    font-size: 13.5px; font-weight: 800; color: #92400e;
    margin-bottom: 10px; display: flex; align-items: center; gap: 7px;
}
.payment-method-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px,1fr)); gap: 8px; margin-bottom: 12px; }
.payment-method-chip {
    background: #fff; border: 1px solid #fcd34d; border-radius: 8px;
    padding: 8px 10px; font-size: 12px; font-weight: 700; color: #78350f;
    text-align: center;
}
.payment-info-note { font-size: 12.5px; color: #92400e; line-height: 1.6; }

/* Policy accordion */
.policy-section { border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-bottom: 20px; }
.policy-tab {
    display: flex; align-items: center; justify-content: space-between;
    padding: 13px 18px; cursor: pointer; font-size: 13.5px; font-weight: 700;
    color: #374151; background: #f9fafb; border-bottom: 1px solid #e5e7eb;
    user-select: none;
}
.policy-tab:last-of-type { border-bottom: none; }
.policy-tab svg { transition: transform .2s; flex-shrink: 0; }
.policy-tab.open svg { transform: rotate(180deg); }
.policy-body { display: none; padding: 16px 18px; font-size: 13px; color: #374151; line-height: 1.7; border-bottom: 1px solid #e5e7eb; }
.policy-body:last-child { border-bottom: none; }
.policy-body ul { margin: 6px 0 0; padding-left: 20px; }
.policy-body ul li { margin-bottom: 4px; }

/* Agreement checkbox */
.agreement-card {
    background: #f0f4ff; border: 1px solid #bfdbfe;
    border-radius: 12px; padding: 18px 20px; margin-bottom: 20px;
}
.agreement-row { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px; }
.agreement-row:last-child { margin-bottom: 0; }
.agreement-row input[type=checkbox] { margin-top: 2px; width: 16px; height: 16px; flex-shrink: 0; accent-color: #042C53; cursor: pointer; }
.agreement-row label { font-size: 13px; color: #042C53; line-height: 1.5; cursor: pointer; }
.agreement-row label a { color: #378ADD; font-weight: 700; }

/* Submit button */
.btn-reg-submit {
    width: 100%; padding: 15px; background: linear-gradient(135deg,#1e3a8a,#0f766e);
    color: #fff; border: none; border-radius: 11px; font-size: 16px; font-weight: 800;
    cursor: pointer; font-family: inherit; letter-spacing: .2px;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: opacity .2s, transform .1s; margin-bottom: 14px;
}
.btn-reg-submit:hover { opacity: .92; transform: translateY(-1px); }
.reg-back-link {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: 600; color: #6b7280; text-decoration: none;
}
.reg-back-link:hover { color: #374151; }

/* Success card */
.reg-success-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
    padding: 48px 36px; text-align: center;
    box-shadow: 0 4px 24px rgba(15,23,42,.08);
}
.reg-success-icon {
    width: 72px; height: 72px; background: #dcfce7; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
}
.reg-success-card h2 { font-size: 24px; font-weight: 900; color: #111827; margin: 0 0 10px; }
.reg-success-card p  { font-size: 15px; color: #6b7280; line-height: 1.6; margin: 0 0 24px; }
.reg-next-steps { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 16px 18px; text-align: left; margin-bottom: 24px; }
.reg-next-title { font-size: 13px; font-weight: 700; color: #166534; margin-bottom: 8px; }
.reg-next-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #166534; padding: 4px 0; }
.reg-success-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.reg-alert-error {
    background: #fee2e2; border: 1px solid #fca5a5; border-radius: 10px;
    padding: 14px 18px; color: #991b1b; font-size: 14px; font-weight: 600; margin-bottom: 20px;
}
.btn-ghost-link {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 11px 22px; border: 1.5px solid #e5e7eb; border-radius: 10px;
    font-size: 14px; font-weight: 700; color: #374151; text-decoration: none;
}

/* Sidebar */
.sidebar-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
    overflow: hidden; box-shadow: 0 4px 20px rgba(15,23,42,.07); margin-bottom: 18px;
}
.sidebar-img {
    height: 170px; overflow: hidden;
    background: linear-gradient(135deg,#1e3a8a,#0f766e);
    display: flex; align-items: center; justify-content: center;
    font-size: 52px;
}
.sidebar-img img { width: 100%; height: 100%; object-fit: cover; }
.sidebar-body { padding: 20px; }
.sidebar-price-row { display: flex; align-items: baseline; gap: 8px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #f0f2f5; }
.sidebar-price { font-size: 28px; font-weight: 900; color: #1e3a8a; }
.sidebar-price-label { font-size: 12px; color: #9ca3af; }
.feature-row { display: flex; align-items: center; gap: 10px; padding: 7px 0; border-bottom: 1px solid #f9fafb; font-size: 13.5px; color: #374151; }
.feature-row:last-child { border-bottom: none; }
.feature-row svg { flex-shrink: 0; color: #16a34a; }

.trust-card {
    background: linear-gradient(135deg,#042C53,#378ADD);
    border-radius: 14px; padding: 20px; color: #fff; margin-bottom: 18px;
}
.trust-card h4 { font-size: 15px; font-weight: 800; margin: 0 0 6px; }
.trust-card p  { font-size: 13px; opacity: .85; margin: 0 0 14px; line-height: 1.5; }
.trust-bullets { display: flex; flex-direction: column; gap: 8px; }
.trust-bullet { display: flex; align-items: center; gap: 8px; font-size: 13px; opacity: .9; }

.sidebar-help {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
    padding: 18px; text-align: center;
}
.sidebar-help-title { font-size: 13px; font-weight: 700; color: #111827; margin-bottom: 4px; }
.sidebar-help-sub   { font-size: 12.5px; color: #6b7280; margin-bottom: 12px; }
.sidebar-help-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; background: #f0f4ff; color: #1e3a8a;
    border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;
}
</style>
