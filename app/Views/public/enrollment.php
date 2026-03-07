<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscrição — Escola Angolana Modelo</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
  --fire: #dd4814;
  --fire-deep: #b33a0f;
  --fire-glow: #ff6b35;
  --night: #0d1117;
  --ink: #1a2332;
  --slate: #2d3f55;
  --ash: #64748b;
  --fog: #94a3b8;
  --mist: #e2e8f0;
  --snow: #f8fafc;
  --white: #ffffff;
  --gold: #f59e0b;
  --emerald: #10b981;
}

*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
body {
  font-family: 'DM Sans', sans-serif;
  background: var(--white);
  color: var(--ink);
  overflow-x: hidden;
}
h1,h2,h3,h4,h5,h6 { font-family: 'Syne', sans-serif; }

/* ===== NAVBAR ===== */
nav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
  background: rgba(255,255,255,0.92);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(221,72,20,0.08);
  padding: 0 2rem; height: 72px;
  display: flex; align-items: center; justify-content: space-between;
  transition: all 0.3s ease;
}
nav.scrolled { height: 60px; box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
.nav-logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
.nav-logo-icon {
  width: 44px; height: 44px; background: var(--fire);
  border-radius: 12px; display: flex; align-items: center; justify-content: center;
  color: white; font-size: 1.2rem; box-shadow: 0 4px 12px rgba(221,72,20,0.35);
}
.nav-logo-text { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.1rem; color: var(--ink); line-height: 1.1; }
.nav-logo-text span { display: block; font-weight: 400; font-size: 0.7rem; color: var(--ash); letter-spacing: 0.05em; font-family: 'DM Sans', sans-serif; }
.nav-links { display: flex; align-items: center; gap: 0.25rem; list-style: none; }
.nav-links a { text-decoration: none; color: var(--slate); font-weight: 500; font-size: 0.9rem; padding: 0.5rem 0.9rem; border-radius: 8px; transition: all 0.2s; }
.nav-links a:hover, .nav-links a.active { color: var(--fire); background: rgba(221,72,20,0.07); }
.nav-actions { display: flex; align-items: center; gap: 0.75rem; }
.btn-ghost { padding: 0.5rem 1rem; border-radius: 8px; border: 1.5px solid var(--mist); color: var(--slate); background: none; cursor: pointer; font-family: 'DM Sans', sans-serif; font-weight: 500; font-size: 0.875rem; text-decoration: none; transition: all 0.2s; }
.btn-ghost:hover { border-color: var(--fire); color: var(--fire); }
.btn-fire { padding: 0.5rem 1.2rem; border-radius: 8px; background: var(--fire); color: white; border: none; cursor: pointer; font-family: 'DM Sans', sans-serif; font-weight: 600; font-size: 0.875rem; text-decoration: none; transition: all 0.2s; box-shadow: 0 2px 8px rgba(221,72,20,0.3); }
.btn-fire:hover { background: var(--fire-deep); transform: translateY(-1px); }
.hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: 4px; }
.hamburger span { display: block; width: 24px; height: 2px; background: var(--ink); border-radius: 2px; transition: all 0.3s; }
.mobile-nav { display: none; position: fixed; inset: 0; background: var(--night); z-index: 999; padding: 2rem; flex-direction: column; }
.mobile-nav.open { display: flex; }
.mobile-nav-close { align-self: flex-end; background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer; }
.mobile-nav ul { list-style: none; display: flex; flex-direction: column; gap: 0.5rem; margin-top: 3rem; }
.mobile-nav ul a { color: white; text-decoration: none; font-family: 'Syne', sans-serif; font-size: 1.5rem; font-weight: 700; padding: 0.75rem 0; display: block; border-bottom: 1px solid rgba(255,255,255,0.06); }
.mobile-nav ul a:hover { color: var(--fire); }
.mobile-nav-btns { display: flex; flex-direction: column; gap: 1rem; margin-top: 2rem; }

/* ===== PAGE HEADER ===== */
.page-header {
  background: var(--night);
  padding: 8rem 2rem 5rem;
  position: relative; overflow: hidden;
}
.page-header-bg {
  position: absolute; inset: 0; overflow: hidden;
}
.ph-orb {
  position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.35;
}
.ph-orb-1 { width: 500px; height: 500px; background: radial-gradient(circle, #dd4814, transparent 70%); top: -200px; right: -100px; }
.ph-orb-2 { width: 350px; height: 350px; background: radial-gradient(circle, #1e3a5f, transparent 70%); bottom: -100px; left: 5%; }
.ph-grid {
  position: absolute; inset: 0;
  background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
  background-size: 60px 60px;
}
.page-header-inner { position: relative; z-index: 2; max-width: 1280px; margin: 0 auto; }
.page-header h1 { font-size: clamp(2rem, 4vw, 3.25rem); font-weight: 800; color: white; margin-bottom: 0.75rem; letter-spacing: -0.02em; }
.page-header h1 .accent { color: var(--fire-glow); }
.page-header p { color: rgba(255,255,255,0.6); font-size: 1.1rem; max-width: 560px; line-height: 1.7; margin-bottom: 1.5rem; }
.breadcrumb { display: flex; gap: 0.5rem; align-items: center; list-style: none; }
.breadcrumb li { font-size: 0.85rem; color: rgba(255,255,255,0.4); }
.breadcrumb li a { color: rgba(255,255,255,0.5); text-decoration: none; transition: color 0.2s; }
.breadcrumb li a:hover { color: var(--fire-glow); }
.breadcrumb li + li::before { content: '/'; margin-right: 0.5rem; opacity: 0.4; }
.breadcrumb li:last-child { color: rgba(255,255,255,0.8); }

/* ===== STEPS INDICATOR ===== */
.steps-bar {
  background: var(--ink);
  padding: 1.75rem 2rem;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.steps-inner { max-width: 1280px; margin: 0 auto; display: flex; align-items: center; gap: 0; }
.step-item { display: flex; align-items: center; gap: 0.75rem; flex: 1; }
.step-circle {
  width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.875rem;
  border: 2px solid rgba(255,255,255,0.12); color: rgba(255,255,255,0.3);
  background: transparent; transition: all 0.3s;
}
.step-circle.active { background: var(--fire); border-color: var(--fire); color: white; box-shadow: 0 4px 12px rgba(221,72,20,0.4); }
.step-circle.done { background: rgba(16,185,129,0.15); border-color: var(--emerald); color: var(--emerald); }
.step-info { display: flex; flex-direction: column; }
.step-label { font-family: 'Syne', sans-serif; font-size: 0.8rem; font-weight: 700; color: rgba(255,255,255,0.3); }
.step-label.active { color: white; }
.step-label.done { color: var(--emerald); }
.step-desc { font-size: 0.72rem; color: rgba(255,255,255,0.2); }
.step-line { flex: 1; height: 1px; background: rgba(255,255,255,0.1); margin: 0 1rem; max-width: 80px; }
.step-line.done-line { background: var(--emerald); opacity: 0.4; }

/* ===== MAIN CONTENT ===== */
.page-body { background: var(--snow); padding: 4rem 2rem; min-height: 60vh; }
.page-body-inner { max-width: 900px; margin: 0 auto; }

/* ===== ALERT INFO ===== */
.info-banner {
  background: linear-gradient(135deg, rgba(221,72,20,0.07), rgba(221,72,20,0.03));
  border: 1px solid rgba(221,72,20,0.2);
  border-radius: 16px; padding: 1.5rem;
  display: flex; gap: 1.25rem; align-items: flex-start;
  margin-bottom: 2rem;
}
.info-banner-icon {
  width: 44px; height: 44px; min-width: 44px;
  background: rgba(221,72,20,0.1); border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  color: var(--fire); font-size: 1.1rem;
  border: 1px solid rgba(221,72,20,0.2);
}
.info-banner h5 { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--ink); margin-bottom: 0.3rem; }
.info-banner p { font-size: 0.875rem; color: var(--ash); line-height: 1.6; margin: 0; }
.info-banner .required-mark { color: var(--fire); font-weight: 700; }

.success-banner {
  background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.25);
  border-radius: 16px; padding: 1.5rem; display: flex; gap: 1.25rem; align-items: flex-start; margin-bottom: 2rem;
}
.success-banner-icon { width: 44px; height: 44px; min-width: 44px; background: rgba(16,185,129,0.12); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--emerald); font-size: 1.1rem; }
.success-banner h5 { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--ink); margin-bottom: 0.3rem; }
.success-banner p { font-size: 0.875rem; color: var(--ash); margin: 0; }

.error-banner {
  background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2);
  border-radius: 16px; padding: 1.5rem; display: flex; gap: 1.25rem; align-items: flex-start; margin-bottom: 2rem;
}
.error-banner-icon { width: 44px; height: 44px; min-width: 44px; background: rgba(239,68,68,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 1.1rem; }
.error-banner h5 { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--ink); margin-bottom: 0.5rem; }
.error-banner ul { font-size: 0.875rem; color: var(--ash); padding-left: 1.25rem; margin: 0; }

/* ===== FORM CARD ===== */
.form-card {
  background: var(--white); border-radius: 24px;
  border: 1px solid var(--mist); overflow: hidden;
  box-shadow: 0 4px 24px rgba(0,0,0,0.05);
}
.form-card-header {
  background: var(--ink); padding: 1.75rem 2rem;
  display: flex; align-items: center; gap: 1rem;
}
.form-card-header-icon {
  width: 44px; height: 44px; background: rgba(221,72,20,0.2);
  border-radius: 12px; display: flex; align-items: center; justify-content: center;
  color: var(--fire-glow); font-size: 1.1rem; border: 1px solid rgba(221,72,20,0.3);
}
.form-card-header h3 { font-family: 'Syne', sans-serif; font-size: 1.15rem; font-weight: 700; color: white; margin: 0; }
.form-card-header p { font-size: 0.8rem; color: rgba(255,255,255,0.4); margin: 0; }
.form-card-body { padding: 2.5rem; }

/* ===== FORM SECTIONS ===== */
.form-section { margin-bottom: 2.5rem; }
.form-section:last-of-type { margin-bottom: 0; }
.section-divider {
  display: flex; align-items: center; gap: 1rem; margin-bottom: 1.75rem;
}
.section-divider-icon {
  width: 36px; height: 36px; background: rgba(221,72,20,0.1);
  border-radius: 10px; display: flex; align-items: center; justify-content: center;
  color: var(--fire); font-size: 0.9rem; flex-shrink: 0;
  border: 1px solid rgba(221,72,20,0.15);
}
.section-divider h5 {
  font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700;
  color: var(--ink); margin: 0;
}
.section-divider::after {
  content: ''; flex: 1; height: 1px; background: var(--mist);
}

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.form-grid.cols-1 { grid-template-columns: 1fr; }
.form-group { display: flex; flex-direction: column; gap: 0.4rem; }
.form-group.span-2 { grid-column: span 2; }

label {
  font-size: 0.85rem; font-weight: 600; color: var(--slate);
  font-family: 'DM Sans', sans-serif;
}
label .req { color: var(--fire); margin-left: 2px; }

.form-control, .form-select {
  width: 100%; padding: 0.8rem 1rem;
  border: 1.5px solid var(--mist); border-radius: 12px;
  font-family: 'DM Sans', sans-serif; font-size: 0.9rem; color: var(--ink);
  background: var(--snow); transition: all 0.2s;
  outline: none; appearance: none;
}
.form-control::placeholder { color: var(--fog); }
.form-control:focus, .form-select:focus {
  border-color: var(--fire); background: white;
  box-shadow: 0 0 0 3px rgba(221,72,20,0.1);
}
.form-control.is-invalid, .form-select.is-invalid { border-color: #ef4444; }
.form-control.is-invalid:focus, .form-select.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
.form-control.is-valid, .form-select.is-valid { border-color: var(--emerald); }
.form-hint { font-size: 0.78rem; color: var(--fog); margin-top: 0.2rem; }
.form-error { font-size: 0.78rem; color: #ef4444; margin-top: 0.2rem; display: none; }
.form-error.show { display: block; }

textarea.form-control { resize: vertical; min-height: 90px; }

/* Custom select arrow */
.form-select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
  background-repeat: no-repeat; background-position: right 1rem center; background-size: 14px;
  padding-right: 2.5rem; cursor: pointer;
}
.form-select:disabled { opacity: 0.5; cursor: not-allowed; background-color: var(--mist); }

/* ===== CHECKBOX ===== */
.check-group { display: flex; gap: 0.875rem; align-items: flex-start; padding: 1.25rem; background: var(--snow); border-radius: 12px; border: 1.5px solid var(--mist); transition: all 0.2s; cursor: pointer; }
.check-group:hover { border-color: rgba(221,72,20,0.3); background: rgba(221,72,20,0.02); }
.check-group input[type="checkbox"] { display: none; }
.check-box {
  width: 22px; height: 22px; min-width: 22px; border-radius: 6px;
  border: 2px solid var(--mist); background: white;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.2s; margin-top: 1px;
}
.check-box i { font-size: 0.7rem; color: white; opacity: 0; transition: all 0.2s; }
.check-group.checked .check-box { background: var(--fire); border-color: var(--fire); }
.check-group.checked .check-box i { opacity: 1; }
.check-text { font-size: 0.875rem; color: var(--slate); line-height: 1.6; }
.check-text .req { color: var(--fire); }

/* ===== FORM ACTIONS ===== */
.form-actions {
  display: flex; gap: 1rem; padding-top: 2rem;
  border-top: 1px solid var(--mist); margin-top: 2.5rem;
}
.btn-submit {
  flex: 1; padding: 1rem 2rem; background: var(--fire); color: white;
  border: none; border-radius: 14px; cursor: pointer;
  font-family: 'Syne', sans-serif; font-weight: 700; font-size: 1rem;
  transition: all 0.3s; box-shadow: 0 8px 24px rgba(221,72,20,0.3);
  display: flex; align-items: center; justify-content: center; gap: 0.6rem;
}
.btn-submit:hover { background: var(--fire-deep); transform: translateY(-2px); box-shadow: 0 12px 32px rgba(221,72,20,0.4); }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
.btn-reset {
  padding: 1rem 1.75rem; border-radius: 14px;
  border: 1.5px solid var(--mist); color: var(--ash);
  background: none; cursor: pointer; font-family: 'DM Sans', sans-serif;
  font-weight: 500; font-size: 0.9rem; transition: all 0.2s;
  display: flex; align-items: center; gap: 0.5rem;
}
.btn-reset:hover { border-color: var(--ink); color: var(--ink); }

/* Spinner */
.spinner { display: inline-block; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ===== BOTTOM INFO CARDS ===== */
.bottom-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 2.5rem; }
.bottom-card {
  background: var(--white); border-radius: 16px; padding: 1.75rem;
  border: 1px solid var(--mist); text-align: center; transition: all 0.3s;
}
.bottom-card:hover { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(0,0,0,0.07); border-color: rgba(221,72,20,0.2); }
.bottom-card-icon {
  width: 56px; height: 56px; background: rgba(221,72,20,0.08);
  border-radius: 14px; display: flex; align-items: center; justify-content: center;
  color: var(--fire); font-size: 1.3rem; margin: 0 auto 1rem;
  border: 1px solid rgba(221,72,20,0.12); transition: all 0.3s;
}
.bottom-card:hover .bottom-card-icon { background: var(--fire); color: white; border-color: var(--fire); }
.bottom-card h6 { font-family: 'Syne', sans-serif; font-size: 0.95rem; font-weight: 700; color: var(--ink); margin-bottom: 0.35rem; }
.bottom-card p { font-size: 0.82rem; color: var(--ash); margin: 0; line-height: 1.5; }

/* ===== REVEAL ANIMATIONS ===== */
[data-reveal] { opacity: 0; transform: translateY(24px); transition: opacity 0.6s ease, transform 0.6s ease; }
[data-reveal].revealed { opacity: 1; transform: translateY(0); }
[data-reveal][data-delay="1"] { transition-delay: 0.1s; }
[data-reveal][data-delay="2"] { transition-delay: 0.2s; }
[data-reveal][data-delay="3"] { transition-delay: 0.3s; }

/* ===== FOOTER ===== */
footer { background: var(--night); padding: 3rem 2rem 2rem; border-top: 1px solid rgba(255,255,255,0.06); }
.footer-bottom-simple {
  max-width: 1280px; margin: 0 auto;
  display: flex; justify-content: space-between; align-items: center;
  color: rgba(255,255,255,0.3); font-size: 0.8rem;
}
.footer-bottom-simple a { color: rgba(255,255,255,0.5); text-decoration: none; }
.footer-bottom-simple a:hover { color: var(--fire); }
.footer-logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }

/* ===== BACK TO TOP ===== */
.back-top { position: fixed; bottom: 2rem; right: 2rem; width: 48px; height: 48px; border-radius: 12px; background: var(--fire); color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 8px 24px rgba(221,72,20,0.4); opacity: 0; visibility: hidden; transform: translateY(10px); transition: all 0.3s; z-index: 999; }
.back-top.show { opacity: 1; visibility: visible; transform: translateY(0); }
.back-top:hover { transform: translateY(-4px); }

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  .nav-links, .nav-actions { display: none; }
  .hamburger { display: flex; }
  .form-grid { grid-template-columns: 1fr; }
  .form-group.span-2 { grid-column: span 1; }
  .form-card-body { padding: 1.5rem; }
  .bottom-cards { grid-template-columns: 1fr; }
  .form-actions { flex-direction: column; }
  .btn-reset { justify-content: center; }
  .steps-inner { display: none; }
  .footer-bottom-simple { flex-direction: column; gap: 0.75rem; text-align: center; }
  .page-header { padding: 7rem 1.5rem 4rem; }
  .page-body { padding: 2.5rem 1rem; }
}
@media (max-width: 480px) {
  .form-card-header { padding: 1.25rem 1.5rem; }
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav id="nav">
  <a href="index.html" class="nav-logo">
    <div class="nav-logo-icon"><i class="fas fa-graduation-cap"></i></div>
    <div class="nav-logo-text">Escola Angolana Modelo <span>Excelência em Educação</span></div>
  </a>
  <ul class="nav-links">
    <li><a href="/">Início</a></li>
  <!--   <li><a href="/courses">Cursos</a></li>
    <li><a href="/about">Sobre</a></li>
    <li><a href="/testimonials">Depoimentos</a></li>
    <li><a href="/contact">Contato</a></li> -->
  </ul>
  <div class="nav-actions">
   <!--  <a href="inscricao.html" class="btn-ghost active" style="color:var(--fire); border-color:rgba(221,72,20,0.3)">
      <i class="fas fa-user-plus" style="margin-right:6px"></i>Inscrever-se
    </a> -->
    <a href="/auth/students" class="btn-fire"><i class="fas fa-sign-in-alt" style="margin-right:6px"></i>Entrar</a>
  </div>
  <div class="hamburger" id="hamburger" onclick="toggleMenu()">
    <span></span><span></span><span></span>
  </div>
</nav>

<!-- MOBILE NAV -->
<div class="mobile-nav" id="mobileNav">
  <button class="mobile-nav-close" onclick="toggleMenu()"><i class="fas fa-times"></i></button>
  <ul>
    <li><a href="index.html" onclick="toggleMenu()">Início</a></li>
    <li><a href="#" onclick="toggleMenu()">Cursos</a></li>
    <li><a href="#" onclick="toggleMenu()">Sobre</a></li>
    <li><a href="#" onclick="toggleMenu()">Contato</a></li>
  </ul>
  <div class="mobile-nav-btns">
    <a href="inscricao.html" style="padding:0.875rem; border-radius:12px; background:rgba(255,255,255,0.07); border:1.5px solid rgba(255,255,255,0.15); color:white; text-align:center; text-decoration:none; font-weight:500">Inscrever-se</a>
    <a href="#" style="padding:0.875rem; border-radius:12px; background:var(--fire); color:white; text-align:center; text-decoration:none; font-weight:700">Entrar</a>
  </div>
</div>

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="page-header-bg">
    <div class="ph-orb ph-orb-1"></div>
    <div class="ph-orb ph-orb-2"></div>
    <div class="ph-grid"></div>
  </div>
  <div class="page-header-inner">
    <div style="display:inline-flex; align-items:center; gap:0.5rem; background:rgba(221,72,20,0.15); border:1px solid rgba(221,72,20,0.3); color:#ff8c5a; padding:0.4rem 1rem; border-radius:50px; font-size:0.8rem; font-weight:500; margin-bottom:1.25rem;">
      <i class="fas fa-circle" style="font-size:0.55rem; animation:pulse 2s infinite"></i> Matrículas Abertas 2025
    </div>
    <h1>Inscrição de <span class="accent">Estudante</span></h1>
    <p>Preencha o formulário abaixo para iniciar o seu processo de matrícula. A nossa equipa entrará em contacto em até 48h.</p>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li><a href="/home">Início</a></li>
        <li>Inscrição</li>
      </ol>
    </nav>
  </div>
</div>

<!-- STEPS BAR -->
<div class="steps-bar">
  <div class="steps-inner">
    <div class="step-item">
      <div class="step-circle active">1</div>
      <div class="step-info">
        <div class="step-label active">Dados Pessoais</div>
        <div class="step-desc">Informações do aluno</div>
      </div>
    </div>
    <div class="step-line"></div>
    <div class="step-item">
      <div class="step-circle">2</div>
      <div class="step-info">
        <div class="step-label">Informação Académica</div>
        <div class="step-desc">Curso e classe</div>
      </div>
    </div>
    <div class="step-line"></div>
    <div class="step-item">
      <div class="step-circle">3</div>
      <div class="step-info">
        <div class="step-label">Emergência</div>
        <div class="step-desc">Contacto de emergência</div>
      </div>
    </div>
    <div class="step-line"></div>
    <div class="step-item">
      <div class="step-circle">4</div>
      <div class="step-info">
        <div class="step-label">Confirmação</div>
        <div class="step-desc">Rever e enviar</div>
      </div>
    </div>
  </div>
</div>

<!-- PAGE BODY -->
<div class="page-body">
  <div class="page-body-inner">

    <!-- Info Banner -->
    <div class="info-banner" data-reveal>
      <div class="info-banner-icon"><i class="fas fa-info-circle"></i></div>
      <div>
        <h5>Informações Importantes</h5>
        <p>Campos marcados com <span class="required-mark">*</span> são obrigatórios. Após o envio, a nossa equipa entrará em contacto em até 48h úteis para dar continuidade ao processo de matrícula.</p>
      </div>
    </div>

    <!-- FORM CARD -->
    <div class="form-card" data-reveal data-delay="1">
      <div class="form-card-header">
        <div class="form-card-header-icon"><i class="fas fa-pen-to-square"></i></div>
        <div>
          <h3>Formulário de Inscrição</h3>
          <p>Preencha todos os campos com atenção</p>
        </div>
      </div>

      <div class="form-card-body">
        <form id="enrollmentForm" action="/inscricao/submit" method="post" novalidate>

          <!-- DADOS PESSOAIS -->
          <div class="form-section">
            <div class="section-divider">
              <div class="section-divider-icon"><i class="fas fa-user"></i></div>
              <h5>Dados Pessoais</h5>
            </div>
            <div class="form-grid">
              <div class="form-group span-2">
                <label for="full_name">Nome Completo <span class="req">*</span></label>
                <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Digite o nome completo do aluno" required>
                <div class="form-error" id="err-full_name">Por favor insira o nome completo.</div>
              </div>

              <div class="form-group">
                <label for="birth_date">Data de Nascimento <span class="req">*</span></label>
                <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                <div class="form-error" id="err-birth_date">Data de nascimento inválida.</div>
              </div>

              <div class="form-group">
                <label for="gender">Gênero <span class="req">*</span></label>
                <select class="form-select" id="gender" name="gender" required>
                  <option value="">Selecione...</option>
                  <option value="Masculino">Masculino</option>
                  <option value="Feminino">Feminino</option>
                </select>
                <div class="form-error" id="err-gender">Por favor selecione o gênero.</div>
              </div>

              <div class="form-group">
                <label for="identity_document">Nº do BI / Passaporte <span class="req">*</span></label>
                <input type="text" class="form-control" id="identity_document" name="identity_document" placeholder="Ex: 000000000LA000" required>
                <div class="form-error" id="err-identity_document">Documento de identidade obrigatório.</div>
              </div>

              <div class="form-group">
                <label for="email">Email <span class="req">*</span></label>
                <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                <div class="form-error" id="err-email">Email inválido.</div>
              </div>

              <div class="form-group">
                <label for="phone">Telefone <span class="req">*</span></label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+244 000 000 000" required>
                <span class="form-hint">Formato: +244 000 000 000</span>
                <div class="form-error" id="err-phone">Número de telefone inválido.</div>
              </div>

              <div class="form-group span-2">
                <label for="address">Endereço Completo <span class="req">*</span></label>
                <textarea class="form-control" id="address" name="address" placeholder="Rua, bairro, município, província" required></textarea>
                <div class="form-error" id="err-address">Por favor insira o endereço.</div>
              </div>
            </div>
          </div>

          <!-- INFORMAÇÕES ACADÉMICAS -->
          <div class="form-section">
            <div class="section-divider">
              <div class="section-divider-icon"><i class="fas fa-graduation-cap"></i></div>
              <h5>Informações Académicas</h5>
            </div>
            <div class="form-grid">
              <div class="form-group">
                <label for="grade_level">Classe Pretendida <span class="req">*</span></label>
                <select class="form-select" id="grade_level" name="grade_level" required>
                  <option value="">Selecione a classe...</option>
                  <optgroup label="Ensino Primário">
                    <option value="1">1ª Classe</option>
                    <option value="2">2ª Classe</option>
                    <option value="3">3ª Classe</option>
                    <option value="4">4ª Classe</option>
                    <option value="5">5ª Classe</option>
                    <option value="6">6ª Classe</option>
                  </optgroup>
                  <optgroup label="Ensino Médio (I Ciclo)">
                    <option value="7">7ª Classe</option>
                    <option value="8">8ª Classe</option>
                    <option value="9">9ª Classe</option>
                  </optgroup>
                  <optgroup label="Ensino Médio (II Ciclo)">
                    <option value="13">10ª Classe</option>
                    <option value="14">11ª Classe</option>
                    <option value="15">12ª Classe</option>
                    <option value="16">13ª Classe</option>
                  </optgroup>
                </select>
                <div class="form-error" id="err-grade_level">Por favor selecione a classe.</div>
              </div>

              <div class="form-group">
                <label for="course">Curso <span style="color:var(--fog); font-weight:400">(apenas Ensino Médio II Ciclo)</span></label>
                <select class="form-select" id="course" name="course" disabled>
                  <option value="">Selecione o curso...</option>
                  <option value="1">Ciências e Tecnologia</option>
                  <option value="2">Ciências Económicas e Jurídicas</option>
                  <option value="3">Humanidades e Ciências Sociais</option>
                </select>
                <span class="form-hint">Disponível apenas para 10ª–13ª classes</span>
              </div>

              <div class="form-group span-2">
                <label for="previous_school">Escola Anterior</label>
                <input type="text" class="form-control" id="previous_school" name="previous_school" placeholder="Nome da escola onde estudou anteriormente (opcional)">
              </div>
            </div>
          </div>

          <!-- CONTACTO DE EMERGÊNCIA -->
          <div class="form-section">
            <div class="section-divider">
              <div class="section-divider-icon"><i class="fas fa-phone-alt"></i></div>
              <h5>Contacto de Emergência</h5>
            </div>
            <div class="form-grid">
              <div class="form-group">
                <label for="emergency_name">Nome do Responsável / Encarregado <span class="req">*</span></label>
                <input type="text" class="form-control" id="emergency_name" name="emergency_name" placeholder="Nome completo do encarregado" required>
                <div class="form-error" id="err-emergency_name">Por favor insira o nome do responsável.</div>
              </div>

              <div class="form-group">
                <label for="emergency_contact">Telefone de Emergência <span class="req">*</span></label>
                <input type="tel" class="form-control" id="emergency_contact" name="emergency_contact" placeholder="+244 000 000 000" required>
                <div class="form-error" id="err-emergency_contact">Número de emergência inválido.</div>
              </div>
            </div>
          </div>

          <!-- TERMOS -->
          <div class="form-section">
            <div class="section-divider">
              <div class="section-divider-icon"><i class="fas fa-shield-alt"></i></div>
              <h5>Declaração e Termos</h5>
            </div>
            <label class="check-group" id="check-group-terms" onclick="toggleCheck(this)">
              <input type="checkbox" id="terms" name="terms" value="1">
              <div class="check-box"><i class="fas fa-check"></i></div>
              <div class="check-text">
                Declaro que as informações fornecidas são verdadeiras e estou ciente das políticas da escola. <span class="req">*</span>
              </div>
            </label>
            <div class="form-error" id="err-terms" style="margin-top:0.5rem">Deve aceitar os termos para continuar.</div>
          </div>

          <!-- ACTIONS -->
          <div class="form-actions">
            <button type="button" class="btn-reset" onclick="resetForm()">
              <i class="fas fa-rotate-left"></i> Limpar
            </button>
            <button type="submit" class="btn-submit" id="submitBtn">
              <i class="fas fa-paper-plane"></i> Enviar Inscrição
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- BOTTOM CARDS -->
    <div class="bottom-cards">
      <div class="bottom-card" data-reveal>
        <div class="bottom-card-icon"><i class="fas fa-bolt"></i></div>
        <h6>Processo Rápido</h6>
        <p>Resposta em até 48 horas úteis após o envio</p>
      </div>
      <div class="bottom-card" data-reveal data-delay="1">
        <div class="bottom-card-icon"><i class="fas fa-shield-halved"></i></div>
        <h6>Dados Seguros</h6>
        <p>As suas informações estão protegidas e em segurança</p>
      </div>
      <div class="bottom-card" data-reveal data-delay="2">
        <div class="bottom-card-icon"><i class="fas fa-headset"></i></div>
        <h6>Suporte Disponível</h6>
        <p>Dúvidas? Ligue-nos: +244 999 999 999</p>
      </div>
    </div>

  </div>
</div>

<!-- FOOTER -->
<footer>
  <div class="footer-bottom-simple">
    <a href="index.html" class="footer-logo">
      <div class="nav-logo-icon" style="width:36px;height:36px;font-size:1rem"><i class="fas fa-graduation-cap"></i></div>
      <span style="font-family:'Syne',sans-serif;font-weight:700;color:rgba(255,255,255,0.6);font-size:0.95rem">Escola Angolana Modelo</span>
    </a>
    <span>© 2025 Escola Angolana Modelo. Todos os direitos reservados.</span>
    <span>Desenvolvido com <span style="color:#dd4814">♥</span> por <a href="#">EAM Tech</a></span>
  </div>
</footer>

<a href="#" class="back-top" id="backTop"><i class="fas fa-arrow-up"></i></a>

<style>
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
</style>

<script>
// Navbar scroll
const nav = document.getElementById('nav');
window.addEventListener('scroll', () => {
  nav.classList.toggle('scrolled', window.scrollY > 80);
  document.getElementById('backTop').classList.toggle('show', window.scrollY > 400);
});

// Mobile menu
function toggleMenu() { document.getElementById('mobileNav').classList.toggle('open'); }

// Reveal on scroll
const reveals = document.querySelectorAll('[data-reveal]');
const observer = new IntersectionObserver(entries => {
  entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('revealed'); });
}, { threshold: 0.08 });
reveals.forEach(el => observer.observe(el));

// Toggle checkbox
function toggleCheck(label) {
  label.classList.toggle('checked');
  label.querySelector('input').checked = label.classList.contains('checked');
}

// Grade → Course logic
document.getElementById('grade_level').addEventListener('change', function() {
  const highSchool = [13, 14, 15, 16];
  const courseSelect = document.getElementById('course');
  const isHS = highSchool.includes(parseInt(this.value));
  courseSelect.disabled = !isHS;
  if(!isHS) courseSelect.value = '';
});

// Phone formatter
function formatPhone(input) {
  let val = input.value.replace(/\D/g, '');
  if(val.startsWith('244')) val = val.slice(3);
  val = val.slice(0, 9);
  let out = '+244 ';
  if(val.length > 3) {
    out += val.slice(0,3) + ' ';
    if(val.length > 6) out += val.slice(3,6) + ' ' + val.slice(6);
    else out += val.slice(3);
  } else out += val;
  if(val.length > 0) input.value = out;
}
document.getElementById('phone').addEventListener('input', function(){ formatPhone(this); });
document.getElementById('emergency_contact').addEventListener('input', function(){ formatPhone(this); });

// Real-time validation
function setFieldState(id, valid, errId) {
  const el = document.getElementById(id);
  const err = document.getElementById(errId || 'err-'+id);
  if(valid) {
    el.classList.remove('is-invalid'); el.classList.add('is-valid');
    if(err) err.classList.remove('show');
  } else {
    el.classList.add('is-invalid'); el.classList.remove('is-valid');
    if(err) err.classList.add('show');
  }
  return valid;
}

document.getElementById('email').addEventListener('blur', function() {
  setFieldState('email', /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value) || !this.value);
});
document.getElementById('full_name').addEventListener('blur', function() {
  setFieldState('full_name', this.value.trim().length >= 3);
});
document.getElementById('birth_date').addEventListener('change', function() {
  const d = new Date(this.value);
  const age = (new Date() - d) / 31557600000;
  const valid = age >= 5 && age <= 35;
  setFieldState('birth_date', valid);
  if(age > 25 && age <= 35) {
    if(!confirm('A idade informada é superior a 25 anos. Deseja continuar?')) this.value = '';
  }
});

// Reset
function resetForm() {
  if(confirm('Tem certeza que deseja limpar todos os campos?')) {
    document.getElementById('enrollmentForm').reset();
    document.getElementById('course').disabled = true;
    document.getElementById('check-group-terms').classList.remove('checked');
    document.querySelectorAll('.form-control, .form-select').forEach(el => {
      el.classList.remove('is-invalid','is-valid');
    });
    document.querySelectorAll('.form-error').forEach(el => el.classList.remove('show'));
  }
}

// Submit
document.getElementById('enrollmentForm').addEventListener('submit', function(e) {
  e.preventDefault();
  let valid = true;
  const fields = ['full_name','birth_date','gender','identity_document','email','phone','address','grade_level','emergency_name','emergency_contact'];
  fields.forEach(id => {
    const el = document.getElementById(id);
    if(el && el.required && !el.value.trim()) {
      setFieldState(id, false); valid = false;
    }
  });
  const termsChecked = document.getElementById('check-group-terms').classList.contains('checked');
  if(!termsChecked) {
    document.getElementById('err-terms').classList.add('show'); valid = false;
  } else {
    document.getElementById('err-terms').classList.remove('show');
  }
  if(!valid) {
    document.querySelector('.is-invalid')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }
  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.innerHTML = '<div class="spinner"></div> A enviar...';
  setTimeout(() => {
    // Simulate success for demo
    const body = document.querySelector('.page-body-inner');
    const banner = document.createElement('div');
    banner.className = 'success-banner';
    banner.innerHTML = `<div class="success-banner-icon"><i class="fas fa-check-circle"></i></div><div><h5>Inscrição Enviada com Sucesso!</h5><p>A sua inscrição foi recebida. A nossa equipa entrará em contacto em até 48h úteis. Obrigado!</p></div>`;
    body.insertBefore(banner, body.firstChild);
    banner.scrollIntoView({ behavior: 'smooth', block: 'start' });
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar Inscrição';
  }, 1800);
});
</script>
</body>
</html>