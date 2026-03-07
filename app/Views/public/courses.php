<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cursos — Escola Angolana Modelo</title>
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
body { font-family: 'DM Sans', sans-serif; background: var(--white); color: var(--ink); overflow-x: hidden; }
h1,h2,h3,h4,h5,h6 { font-family: 'Syne', sans-serif; }

/* ===== NAVBAR ===== */
nav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
  background: rgba(255,255,255,0.92); backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(221,72,20,0.08);
  padding: 0 2rem; height: 72px;
  display: flex; align-items: center; justify-content: space-between;
  transition: all 0.3s ease;
}
nav.scrolled { height: 60px; box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
.nav-logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
.nav-logo-icon { width: 44px; height: 44px; background: var(--fire); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; box-shadow: 0 4px 12px rgba(221,72,20,0.35); }
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
.page-header { background: var(--night); padding: 8rem 2rem 5rem; position: relative; overflow: hidden; }
.page-header-bg { position: absolute; inset: 0; overflow: hidden; }
.ph-orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.35; }
.ph-orb-1 { width: 500px; height: 500px; background: radial-gradient(circle, #dd4814, transparent 70%); top: -200px; right: -100px; }
.ph-orb-2 { width: 350px; height: 350px; background: radial-gradient(circle, #1e3a5f, transparent 70%); bottom: -100px; left: 5%; }
.ph-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 60px 60px; }
.page-header-inner { position: relative; z-index: 2; max-width: 1280px; margin: 0 auto; }
.page-header-badge { display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(221,72,20,0.15); border: 1px solid rgba(221,72,20,0.3); color: #ff8c5a; padding: 0.4rem 1rem; border-radius: 50px; font-size: 0.8rem; font-weight: 500; margin-bottom: 1.25rem; }
.page-header h1 { font-size: clamp(2rem, 4vw, 3.25rem); font-weight: 800; color: white; margin-bottom: 0.75rem; letter-spacing: -0.02em; }
.page-header h1 .accent { color: var(--fire-glow); }
.page-header p { color: rgba(255,255,255,0.6); font-size: 1.05rem; max-width: 560px; line-height: 1.7; margin-bottom: 1.5rem; }
.breadcrumb { display: flex; gap: 0.5rem; align-items: center; list-style: none; }
.breadcrumb li { font-size: 0.85rem; color: rgba(255,255,255,0.4); }
.breadcrumb li a { color: rgba(255,255,255,0.5); text-decoration: none; transition: color 0.2s; }
.breadcrumb li a:hover { color: var(--fire-glow); }
.breadcrumb li + li::before { content: '/'; margin-right: 0.5rem; opacity: 0.4; }
.breadcrumb li:last-child { color: rgba(255,255,255,0.8); }

/* header stats strip */
.header-stats { display: flex; gap: 3rem; margin-top: 2.5rem; padding-top: 2.5rem; border-top: 1px solid rgba(255,255,255,0.08); }
.hstat-num { font-family: 'Syne', sans-serif; font-size: 1.75rem; font-weight: 800; color: white; line-height: 1; }
.hstat-num span { color: var(--fire-glow); }
.hstat-lbl { font-size: 0.78rem; color: rgba(255,255,255,0.4); margin-top: 0.2rem; }

/* ===== FILTER BAR ===== */
.filter-bar { background: white; border-bottom: 1px solid var(--mist); padding: 1.25rem 2rem; position: sticky; top: 72px; z-index: 90; transition: top 0.3s; }
.filter-bar-inner { max-width: 1280px; margin: 0 auto; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; justify-content: space-between; }
.filter-label { font-size: 0.8rem; font-weight: 600; color: var(--ash); text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap; }
.filter-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.filter-pill {
  padding: 0.45rem 1.1rem; border-radius: 50px;
  border: 1.5px solid var(--mist); background: none;
  color: var(--slate); font-family: 'DM Sans', sans-serif;
  font-weight: 500; font-size: 0.85rem; cursor: pointer;
  transition: all 0.2s; white-space: nowrap;
}
.filter-pill:hover { border-color: var(--fire); color: var(--fire); }
.filter-pill.active { background: var(--fire); border-color: var(--fire); color: white; box-shadow: 0 4px 12px rgba(221,72,20,0.3); }
.filter-count { font-size: 0.8rem; color: var(--ash); white-space: nowrap; }
.filter-count strong { color: var(--ink); font-family: 'Syne', sans-serif; }

/* ===== COURSES SECTION ===== */
.courses-section { background: var(--snow); padding: 4rem 2rem; }
.courses-inner { max-width: 1280px; margin: 0 auto; }
.courses-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.75rem; }

/* ===== COURSE CARD ===== */
.course-card {
  background: var(--white); border-radius: 20px; overflow: hidden;
  border: 1px solid var(--mist); transition: all 0.35s;
  display: flex; flex-direction: column;
}
.course-card:hover { transform: translateY(-8px); box-shadow: 0 24px 50px rgba(0,0,0,0.1); border-color: rgba(221,72,20,0.25); }
.course-card.hidden { display: none; }

.course-thumb { position: relative; height: 210px; overflow: hidden; }
.course-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
.course-card:hover .course-thumb img { transform: scale(1.07); }
.course-thumb-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, transparent 40%, rgba(13,17,23,0.55) 100%); }
.course-chip {
  position: absolute; top: 1rem; left: 1rem; z-index: 2;
  padding: 0.3rem 0.85rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700;
  letter-spacing: 0.03em; text-transform: uppercase;
}
.chip-ciencias { background: rgba(59,130,246,0.85); color: white; }
.chip-humanidades { background: rgba(168,85,247,0.85); color: white; }
.chip-economico { background: rgba(16,185,129,0.85); color: white; }
.chip-tecnico { background: rgba(245,158,11,0.85); color: white; }
.chip-profissional { background: rgba(221,72,20,0.85); color: white; }

.course-duration-badge {
  position: absolute; bottom: 1rem; right: 1rem; z-index: 2;
  background: rgba(13,17,23,0.7); backdrop-filter: blur(8px);
  color: white; padding: 0.3rem 0.75rem; border-radius: 8px;
  font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.4rem;
  border: 1px solid rgba(255,255,255,0.1);
}

.course-body { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; }
.course-name { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 700; color: var(--ink); margin-bottom: 0.5rem; line-height: 1.3; }
.course-name a { color: inherit; text-decoration: none; transition: color 0.2s; }
.course-name a:hover { color: var(--fire); }
.course-desc { color: var(--ash); font-size: 0.875rem; line-height: 1.65; flex: 1; margin-bottom: 1.25rem; }

.course-highlights { display: flex; flex-direction: column; gap: 0.4rem; margin-bottom: 1.25rem; }
.highlight-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: var(--ash); }
.highlight-item i { color: var(--emerald); font-size: 0.7rem; }

.course-divider { height: 1px; background: var(--mist); margin-bottom: 1.25rem; }
.course-foot { display: flex; align-items: center; justify-content: space-between; }
.course-free-badge { display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; font-weight: 600; color: var(--emerald); background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); padding: 0.3rem 0.75rem; border-radius: 50px; }
.btn-details { padding: 0.5rem 1.1rem; border-radius: 10px; border: 1.5px solid var(--fire); color: var(--fire); background: none; font-family: 'DM Sans', sans-serif; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.4rem; }
.btn-details:hover { background: var(--fire); color: white; }

/* Empty state */
.empty-state { grid-column: span 3; text-align: center; padding: 4rem 2rem; }
.empty-state-icon { width: 80px; height: 80px; background: rgba(221,72,20,0.08); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--fire); font-size: 2rem; border: 1px solid rgba(221,72,20,0.15); }
.empty-state h4 { font-family: 'Syne', sans-serif; font-size: 1.25rem; font-weight: 700; color: var(--ink); margin-bottom: 0.5rem; }
.empty-state p { color: var(--ash); font-size: 0.9rem; }

/* ===== MODAL ===== */
.modal-overlay {
  display: none; position: fixed; inset: 0; z-index: 2000;
  background: rgba(13,17,23,0.75); backdrop-filter: blur(6px);
  align-items: center; justify-content: center; padding: 1rem;
}
.modal-overlay.open { display: flex; }
.modal-box {
  background: var(--white); border-radius: 24px; max-width: 720px; width: 100%;
  max-height: 90vh; overflow-y: auto; position: relative;
  box-shadow: 0 40px 80px rgba(0,0,0,0.4);
  animation: modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes modalIn { from { opacity:0; transform:scale(0.9) translateY(20px); } to { opacity:1; transform:scale(1) translateY(0); } }
.modal-head {
  background: var(--ink); padding: 1.75rem 2rem;
  border-radius: 24px 24px 0 0; position: sticky; top: 0; z-index: 10;
  display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem;
}
.modal-head-icon { width: 44px; height: 44px; min-width: 44px; background: rgba(221,72,20,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--fire-glow); font-size: 1.1rem; border: 1px solid rgba(221,72,20,0.3); }
.modal-head-text h3 { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 700; color: white; margin: 0 0 0.2rem; }
.modal-head-text p { font-size: 0.8rem; color: rgba(255,255,255,0.45); margin: 0; }
.modal-close { width: 36px; height: 36px; background: rgba(255,255,255,0.08); border: none; border-radius: 10px; color: rgba(255,255,255,0.6); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; flex-shrink: 0; }
.modal-close:hover { background: rgba(255,255,255,0.15); color: white; }

.modal-content { padding: 2rem; }
.modal-about { background: var(--snow); border-radius: 14px; padding: 1.25rem; margin-bottom: 1.75rem; border: 1px solid var(--mist); }
.modal-about p { font-size: 0.9rem; color: var(--slate); line-height: 1.7; margin: 0; }

.grades-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }
.grade-block { background: var(--snow); border-radius: 14px; padding: 1.25rem; border: 1px solid var(--mist); transition: all 0.2s; }
.grade-block:hover { border-color: rgba(221,72,20,0.25); }
.grade-block.full { grid-column: span 2; }
.grade-block-header { display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--mist); }
.grade-block-icon { width: 30px; height: 30px; background: rgba(221,72,20,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--fire); font-size: 0.8rem; }
.grade-block-header h6 { font-family: 'Syne', sans-serif; font-size: 0.9rem; font-weight: 700; color: var(--ink); margin: 0; }
.discipline-list { list-style: none; display: flex; flex-direction: column; gap: 0.45rem; }
.discipline-list li { display: flex; align-items: center; gap: 0.6rem; font-size: 0.82rem; color: var(--slate); }
.discipline-list li::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--fire); flex-shrink: 0; opacity: 0.5; }

.modal-info-bar { display: flex; align-items: center; gap: 0.75rem; background: rgba(221,72,20,0.06); border: 1px solid rgba(221,72,20,0.15); border-radius: 12px; padding: 1rem 1.25rem; font-size: 0.875rem; color: var(--slate); }
.modal-info-bar i { color: var(--fire); }
.modal-info-bar strong { color: var(--ink); }

.modal-foot { padding: 1.5rem 2rem; border-top: 1px solid var(--mist); display: flex; justify-content: flex-end; gap: 1rem; }
.btn-modal-close { padding: 0.65rem 1.5rem; border-radius: 10px; border: 1.5px solid var(--mist); color: var(--ash); background: none; font-family: 'DM Sans', sans-serif; font-weight: 500; font-size: 0.875rem; cursor: pointer; transition: all 0.2s; }
.btn-modal-close:hover { border-color: var(--ink); color: var(--ink); }
.btn-modal-enroll { padding: 0.65rem 1.75rem; border-radius: 10px; background: var(--fire); color: white; border: none; font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.875rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(221,72,20,0.3); text-decoration: none; display: flex; align-items: center; gap: 0.5rem; }
.btn-modal-enroll:hover { background: var(--fire-deep); transform: translateY(-2px); }

/* ===== WHY SECTION ===== */
.why-section { padding: 5rem 2rem; background: var(--white); }
.why-inner { max-width: 1280px; margin: 0 auto; }
.tag { display: inline-flex; align-items: center; gap: 0.4rem; background: rgba(221,72,20,0.08); color: var(--fire); padding: 0.35rem 0.9rem; border-radius: 50px; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 1rem; }
.section-head { margin-bottom: 3rem; }
.section-head h2 { font-size: clamp(1.75rem, 3vw, 2.5rem); font-weight: 800; color: var(--ink); line-height: 1.2; letter-spacing: -0.02em; }
.section-head h2 .accent { color: var(--fire); }
.section-head p { color: var(--ash); font-size: 1rem; margin-top: 0.6rem; max-width: 580px; line-height: 1.7; }
.why-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
.why-card { background: var(--snow); border-radius: 20px; padding: 2rem; border: 1px solid var(--mist); transition: all 0.3s; text-align: center; }
.why-card:hover { transform: translateY(-8px); box-shadow: 0 24px 50px rgba(0,0,0,0.07); border-color: rgba(221,72,20,0.2); }
.why-icon { width: 68px; height: 68px; margin: 0 auto 1.5rem; background: rgba(221,72,20,0.08); border-radius: 18px; display: flex; align-items: center; justify-content: center; color: var(--fire); font-size: 1.6rem; border: 1px solid rgba(221,72,20,0.12); transition: all 0.3s; }
.why-card:hover .why-icon { background: var(--fire); color: white; border-color: var(--fire); transform: rotate(6deg); }
.why-card h4 { font-family: 'Syne', sans-serif; font-size: 1.05rem; font-weight: 700; color: var(--ink); margin-bottom: 0.65rem; }
.why-card p { font-size: 0.875rem; color: var(--ash); line-height: 1.65; }

/* ===== CTA ===== */
.cta-outer { background: var(--fire); position: relative; overflow: hidden; padding: 5.5rem 2rem; }
.cta-outer::before { content: ''; position: absolute; inset: 0; background: repeating-linear-gradient(45deg, transparent, transparent 40px, rgba(255,255,255,0.03) 40px, rgba(255,255,255,0.03) 80px); }
.cta-inner { max-width: 720px; margin: 0 auto; text-align: center; position: relative; z-index: 2; }
.cta-inner h2 { font-size: clamp(1.75rem, 3.5vw, 2.75rem); font-weight: 800; color: white; margin-bottom: 0.85rem; }
.cta-inner p { color: rgba(255,255,255,0.8); font-size: 1.05rem; margin-bottom: 2.25rem; }
.cta-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
.btn-cta-white { padding: 0.875rem 2.25rem; background: white; color: var(--fire); border-radius: 12px; font-weight: 700; font-size: 0.95rem; text-decoration: none; transition: all 0.3s; box-shadow: 0 8px 24px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 0.5rem; }
.btn-cta-white:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(0,0,0,0.2); }
.btn-cta-outline { padding: 0.875rem 2.25rem; background: transparent; border: 2px solid rgba(255,255,255,0.4); color: white; border-radius: 12px; font-weight: 600; font-size: 0.95rem; text-decoration: none; transition: all 0.3s; display: flex; align-items: center; gap: 0.5rem; }
.btn-cta-outline:hover { background: rgba(255,255,255,0.1); border-color: white; transform: translateY(-3px); }

/* ===== FOOTER ===== */
footer { background: var(--night); padding: 5rem 2rem 2rem; border-top: 1px solid rgba(255,255,255,0.06); }
.footer-inner { max-width: 1280px; margin: 0 auto; }
.footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 4rem; margin-bottom: 4rem; }
.footer-brand p { color: rgba(255,255,255,0.45); font-size: 0.875rem; line-height: 1.7; max-width: 300px; margin-top: 1.25rem; }
.footer-socials { display: flex; gap: 0.75rem; margin-top: 1.75rem; }
.social-btn { width: 38px; height: 38px; border-radius: 10px; background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.5); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; text-decoration: none; transition: all 0.2s; border: 1px solid rgba(255,255,255,0.08); }
.social-btn:hover { background: var(--fire); color: white; border-color: var(--fire); transform: translateY(-3px); }
.footer-col h5 { font-family: 'Syne', sans-serif; font-size: 0.95rem; font-weight: 700; color: white; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.08); }
.footer-links { list-style: none; display: flex; flex-direction: column; gap: 0.6rem; }
.footer-links a { color: rgba(255,255,255,0.45); font-size: 0.875rem; text-decoration: none; transition: all 0.2s; }
.footer-links a:hover { color: var(--fire); padding-left: 0.25rem; }
.footer-contact-list { list-style: none; display: flex; flex-direction: column; gap: 1rem; }
.footer-contact-list li { display: flex; gap: 0.75rem; color: rgba(255,255,255,0.45); font-size: 0.875rem; align-items: flex-start; }
.footer-contact-list i { color: var(--fire); margin-top: 3px; flex-shrink: 0; }
.footer-bottom { border-top: 1px solid rgba(255,255,255,0.06); padding-top: 2rem; display: flex; justify-content: space-between; align-items: center; color: rgba(255,255,255,0.3); font-size: 0.8rem; }
.footer-bottom a { color: rgba(255,255,255,0.5); text-decoration: none; }
.footer-bottom a:hover { color: var(--fire); }

/* ===== BACK TO TOP ===== */
.back-top { position: fixed; bottom: 2rem; right: 2rem; width: 48px; height: 48px; border-radius: 12px; background: var(--fire); color: white; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 8px 24px rgba(221,72,20,0.4); opacity: 0; visibility: hidden; transform: translateY(10px); transition: all 0.3s; z-index: 999; }
.back-top.show { opacity: 1; visibility: visible; transform: translateY(0); }
.back-top:hover { transform: translateY(-4px); }

/* ===== REVEAL ===== */
[data-reveal] { opacity: 0; transform: translateY(24px); transition: opacity 0.6s ease, transform 0.6s ease; }
[data-reveal].revealed { opacity: 1; transform: translateY(0); }
[data-reveal][data-delay="1"] { transition-delay: 0.1s; }
[data-reveal][data-delay="2"] { transition-delay: 0.2s; }
[data-reveal][data-delay="3"] { transition-delay: 0.3s; }
[data-reveal][data-delay="4"] { transition-delay: 0.4s; }
[data-reveal][data-delay="5"] { transition-delay: 0.5s; }

/* ===== RESPONSIVE ===== */
@media (max-width: 1024px) {
  .courses-grid { grid-template-columns: repeat(2, 1fr); }
  .why-grid { grid-template-columns: repeat(2, 1fr); }
  .footer-grid { grid-template-columns: 1fr 1fr; gap: 2.5rem; }
  .grades-grid { grid-template-columns: 1fr; }
  .grade-block.full { grid-column: span 1; }
}
@media (max-width: 768px) {
  .nav-links, .nav-actions { display: none; }
  .hamburger { display: flex; }
  .courses-grid { grid-template-columns: 1fr; }
  .why-grid { grid-template-columns: 1fr; }
  .footer-grid { grid-template-columns: 1fr; gap: 2rem; }
  .footer-bottom { flex-direction: column; gap: 0.75rem; text-align: center; }
  .empty-state { grid-column: span 1; }
  .header-stats { flex-wrap: wrap; gap: 1.5rem; }
  .filter-bar-inner { flex-direction: column; align-items: flex-start; gap: 0.75rem; }
  .modal-box { border-radius: 16px; }
  .modal-foot { flex-direction: column; }
  .btn-modal-enroll { justify-content: center; }
}
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
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
    <li><a href="index.html">Início</a></li>
    <li><a href="cursos.html" class="active">Cursos</a></li>
    <li><a href="#">Sobre</a></li>
    <li><a href="#">Depoimentos</a></li>
    <li><a href="#">Contato</a></li>
  </ul>
  <div class="nav-actions">
    <a href="inscricao.html" class="btn-ghost"><i class="fas fa-user-plus" style="margin-right:6px"></i>Inscrever-se</a>
    <a href="#" class="btn-fire"><i class="fas fa-sign-in-alt" style="margin-right:6px"></i>Entrar</a>
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
    <li><a href="cursos.html" onclick="toggleMenu()">Cursos</a></li>
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
    <div class="page-header-badge"><i class="fas fa-circle" style="font-size:0.55rem; animation:pulse 2s infinite"></i> Grade Curricular 2025</div>
    <h1>Nossos <span class="accent">Cursos</span></h1>
    <p>Conheça a grade curricular completa do Ensino Médio e encontre o percurso académico ideal para o seu futuro.</p>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li><a href="index.html">Início</a></li>
        <li>Cursos</li>
      </ol>
    </nav>
    <div class="header-stats">
      <div>
        <div class="hstat-num">5<span>+</span></div>
        <div class="hstat-lbl">Áreas de Formação</div>
      </div>
      <div>
        <div class="hstat-num">3–4</div>
        <div class="hstat-lbl">Anos de Duração</div>
      </div>
      <div>
        <div class="hstat-num">95<span>%</span></div>
        <div class="hstat-lbl">Taxa de Aprovação</div>
      </div>
      <div>
        <div class="hstat-num">0 Kz</div>
        <div class="hstat-lbl">Ensino Gratuito</div>
      </div>
    </div>
  </div>
</div>

<!-- FILTER BAR -->
<div class="filter-bar" id="filterBar">
  <div class="filter-bar-inner">
    <span class="filter-label">Filtrar por área:</span>
    <div class="filter-pills">
      <button class="filter-pill active" data-filter="all">Todos</button>
      <button class="filter-pill" data-filter="Ciências">Ciências</button>
      <button class="filter-pill" data-filter="Humanidades">Humanidades</button>
      <button class="filter-pill" data-filter="Económico-Jurídico">Económico-Jurídico</button>
      <button class="filter-pill" data-filter="Técnico">Técnico</button>
      <button class="filter-pill" data-filter="Profissional">Profissional</button>
    </div>
    <div class="filter-count">Mostrando <strong id="visibleCount">5</strong> cursos</div>
  </div>
</div>

<!-- COURSES -->
<div class="courses-section">
  <div class="courses-inner">
    <div class="courses-grid" id="coursesGrid">

      <!-- CARD 1 -->
      <div class="course-card" data-category="Ciências" data-reveal>
        <div class="course-thumb">
          <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=600&q=80&auto=format&fit=crop" alt="Ciências e Tecnologia">
          <div class="course-thumb-overlay"></div>
          <span class="course-chip chip-ciencias">Ciências</span>
          <div class="course-duration-badge"><i class="fas fa-clock"></i> 3 anos</div>
        </div>
        <div class="course-body">
          <div class="course-name"><a href="#">Ciências e Tecnologia</a></div>
          <div class="course-desc">Formação sólida em Matemática, Física, Química e Biologia, preparando alunos para o ingresso em cursos superiores de Engenharia, Medicina e Ciências Exatas.</div>
          <div class="course-highlights">
            <div class="highlight-item"><i class="fas fa-circle"></i> Certificação reconhecida pelo MINED</div>
            <div class="highlight-item"><i class="fas fa-circle"></i> Corpo docente especializado</div>
            <div class="highlight-item"><i class="fas fa-circle"></i> Laboratórios modernos</div>
          </div>
          <div class="course-divider"></div>
          <div class="course-foot">
            <div class="course-free-badge"><i class="fas fa-check-circle"></i> Ensino Gratuito</div>
            <button class="btn-details" onclick="openModal('modal-ciencias')">Ver Grade <i class="fas fa-arrow-right"></i></button>
          </div>
        </div>
      </div>

      <!-- CARD 2 -->
      <div class="course-card" data-category="Humanidades" data-reveal data-delay="1">
        <div class="course-thumb">
          <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=600&q=80&auto=format&fit=crop" alt="Humanidades">
          <div class="course-thumb-overlay"></div>
          <span class="course-chip chip-humanidades">Humanidades</span>
          <div class="course-duration-badge"><i class="fas fa-clock"></i> 3 anos</div>
        </div>
        <div class="course-body">
          <div class="course-name"><a href="#">Humanidades e Ciências Sociais</a></div>
          <div class="course-desc">Aprofundamento em Literatura, História, Filosofia, Sociologia e Línguas, formando pensadores críticos para Direito, Comunicação e Ciências Sociais.</div>
          <div class="course-highlights">
            <div class="highlight-item"><i class="fas fa-circle"></i> Certificação reconhecida pelo MINED</div>
            <div class="highlight-item"><i class="fas fa-circle"></i> Foco em pensamento crítico</div>
            <div class="highlight-item"><i class="fas fa-circle"></i> Actividades culturais integradas</div>
          </div>
          <div class="course-divider"></div>
          <div class="course-foot">
            <div class="course-free-badge"><i class="fas fa-check-circle"></i> Ensino Gratuito</div>
            <button class="btn-details" onclick="openModal('modal-humanidades')">Ver Grade <i class="fas fa-arrow-right"></i></button>
          </div>
        </div>
      </div>

      <!-- CARD 3 -->
      <div class="course-card" data-category="Económico-Jurídico" data-reveal data-delay="2">
        <div class="course-thumb">
          <img src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600&q=80&auto=format&fit=crop" alt="Económico-Jurídico">
          <div class="course-thumb-overlay"></div>
          <span class="course-chip chip-economico">Econ. Jurídico</span>
          <div class="course-duration-badge"><i class="fas fa-clock"></i> 3 anos</div>
        </div>
        <div class="course-body">
          <div class="course-name"><a href="#">Ciências Económicas e Jurídicas</a></div>
          <div class="course-desc">Preparação em Economia, Direito, Gestão e Contabilidade para futuros líderes empresariais, economistas e juristas de Angola.</div>
          <div class="course-highlights">
            <div class="highlight-item"><i class="fas fa-circle"></i> Certificação reconhecida pelo MINED</div>
            <div class="highlight-item"><i class="fas fa-circle"></i> Simulações de mercado e negócios</div>
            <div class="highlight-item"><i class="fas fa-circle"></i> Parceria com empresas locais</div>
          </div>
          <div class="course-divider"></div>
          <div class="course-foot">
            <div class="course-free-badge"><i class="fas fa-check-circle"></i> Ensino Gratuito</div>
            <button class="btn-details" onclick="openModal('modal-economico')">Ver Grade <i class="fas fa-arrow-right"></i></button>
          </div>
        </div>
      </div>

      <!-- CARD 4 -->
      <div class="course-card" data-category="Técnico" data-reveal data-delay="3">
        <div class="course-thumb">
          <img src="https://images.unsplash.com/photo-1581091226033-d5c48150dbaa?w=600&q=80&auto=format&fit=crop" alt="Técnico">
          <div class="course-thumb-overlay"></div>
          <span class="course-chip chip-tecnico">Técnico</span>
          <div class="course-duration-badge"><i class="fas fa-clock"></i> 4 anos</div>
        </div>
        <div class="course-body">
          <div class="course-name"><a href="#">Ensino Técnico-Profissional</a></div>
          <div class="course-desc">Formação técnica especializada em áreas como Informática, Eletrónica e Mecânica, combinando teoria e prática intensiva em laboratório.</div>
          <div class="course-highlights">
            <div class="highlight-item"><i class="fas fa-circle"></i> Certificação técnica reconhecida</div>
            <div class="highlight-item"><i class="fas fa-circle"></i> Estágio profissional integrado</div>
            <div class="highlight-item"><i class="fas fa-circle"></i> Alta empregabilidade</div>
          </div>
          <div class="course-divider"></div>
          <div class="course-foot">
            <div class="course-free-badge"><i class="fas fa-check-circle"></i> Ensino Gratuito</div>
            <button class="btn-details" onclick="openModal('modal-tecnico')">Ver Grade <i class="fas fa-arrow-right"></i></button>
          </div>
        </div>
      </div>

      <!-- CARD 5 -->
      <div class="course-card" data-category="Profissional" data-reveal data-delay="4">
        <div class="course-thumb">
          <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=600&q=80&auto=format&fit=crop" alt="Profissional">
          <div class="course-thumb-overlay"></div>
          <span class="course-chip chip-profissional">Profissional</span>
          <div class="course-duration-badge"><i class="fas fa-clock"></i> 3 anos</div>
        </div>
        <div class="course-body">
          <div class="course-name"><a href="#">Formação Profissional</a></div>
          <div class="course-desc">Qualificação rápida e prática em áreas como Saúde, Administração e Turismo, com inserção directa no mercado de trabalho angolano.</div>
          <div class="course-highlights">
            <div class="highlight-item"><i class="fas fa-circle"></i> Certificação profissional</div>
            <div class="highlight-item"><i class="fas fa-circle"></i> Inserção rápida no mercado</div>
            <div class="highlight-item"><i class="fas fa-circle"></i> Convénios com empregadores</div>
          </div>
          <div class="course-divider"></div>
          <div class="course-foot">
            <div class="course-free-badge"><i class="fas fa-check-circle"></i> Ensino Gratuito</div>
            <button class="btn-details" onclick="openModal('modal-profissional')">Ver Grade <i class="fas fa-arrow-right"></i></button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- WHY SECTION -->
<div class="why-section">
  <div class="why-inner">
    <div class="section-head centered" data-reveal style="text-align:center">
      <div class="tag" style="margin:0 auto 1rem"><i class="fas fa-star"></i> Diferenciais</div>
      <h2>Por que escolher os nossos <span class="accent">cursos?</span></h2>
      <p style="margin-left:auto; margin-right:auto">Vantagens que fazem a diferença na sua formação e no seu futuro</p>
    </div>
    <div class="why-grid">
      <div class="why-card" data-reveal>
        <div class="why-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <h4>Corpo Docente Especializado</h4>
        <p>Professores qualificados com vasta experiência no ensino médio e preparação para os exames de acesso ao ensino superior.</p>
      </div>
      <div class="why-card" data-reveal data-delay="2">
        <div class="why-icon"><i class="fas fa-book-open"></i></div>
        <h4>Material Didático Actualizado</h4>
        <p>Conteúdos alinhados com as directrizes do MINED e com as principais universidades públicas e privadas de Angola.</p>
      </div>
      <div class="why-card" data-reveal data-delay="4">
        <div class="why-icon"><i class="fas fa-laptop-code"></i></div>
        <h4>Plataforma Digital</h4>
        <p>Acesso a materiais complementares, exercícios resolvidos, simulados e acompanhamento online do desempenho académico.</p>
      </div>
    </div>
  </div>
</div>

<!-- CTA -->
<div class="cta-outer">
  <div class="cta-inner" data-reveal>
    <h2>Gostou dos nossos cursos?</h2>
    <p>Faça já a sua pré-inscrição e garanta a sua vaga para o próximo ano letivo. Vagas limitadas!</p>
    <div class="cta-btns">
      <a href="inscricao.html" class="btn-cta-white"><i class="fas fa-pen"></i> Inscrever-se Agora</a>
      <a href="#" class="btn-cta-outline"><i class="fas fa-envelope"></i> Tirar Dúvidas</a>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer>
  <div class="footer-inner">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="index.html" class="nav-logo">
          <div class="nav-logo-icon"><i class="fas fa-graduation-cap"></i></div>
          <div class="nav-logo-text" style="color:white">Escola Angolana Modelo <span>Excelência em Educação</span></div>
        </a>
        <p>Excelência em educação desde 2010, formando cidadãos preparados para os desafios do futuro com qualidade, inovação e compromisso social.</p>
        <div class="footer-socials">
          <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
          <a href="#" class="social-btn"><i class="fab fa-youtube"></i></a>
          <a href="#" class="social-btn"><i class="fab fa-whatsapp"></i></a>
        </div>
      </div>
      <div class="footer-col">
        <h5>Links Rápidos</h5>
        <ul class="footer-links">
          <li><a href="index.html">Início</a></li>
          <li><a href="cursos.html">Cursos</a></li>
          <li><a href="#">Sobre Nós</a></li>
          <li><a href="#">Contato</a></li>
          <li><a href="inscricao.html">Inscrição</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h5>Cursos</h5>
        <ul class="footer-links">
          <li><a href="#">Ciências e Tecnologia</a></li>
          <li><a href="#">Humanidades</a></li>
          <li><a href="#">Ciências Económicas</a></li>
          <li><a href="#">Técnico-Profissional</a></li>
          <li><a href="#">Formação Profissional</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h5>Contato</h5>
        <ul class="footer-contact-list">
          <li><i class="fas fa-map-marker-alt"></i><span>Rua da Educação, 123<br>Luanda, Angola</span></li>
          <li><i class="fas fa-phone"></i><span>+244 999 999 999<br>+244 999 999 998</span></li>
          <li><i class="fas fa-envelope"></i><span>info@escola.ao<br>secretaria@escola.ao</span></li>
          <li><i class="fas fa-clock"></i><span>Seg–Sex: 07:30–18:00<br>Sáb: 08:00–12:00</span></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© 2025 Escola Angolana Modelo. Todos os direitos reservados.</span>
      <span>Desenvolvido com <span style="color:#dd4814">♥</span> por <a href="#">EAM Tech</a></span>
    </div>
  </div>
</footer>

<a href="#" class="back-top" id="backTop"><i class="fas fa-arrow-up"></i></a>

<!-- ===== MODALS ===== -->

<!-- Modal Ciências -->
<div class="modal-overlay" id="modal-ciencias">
  <div class="modal-box">
    <div class="modal-head">
      <div style="display:flex;align-items:flex-start;gap:1rem;flex:1">
        <div class="modal-head-icon"><i class="fas fa-flask"></i></div>
        <div class="modal-head-text"><h3>Ciências e Tecnologia</h3><p>Grade curricular · 3 anos · 10ª à 12ª Classe</p></div>
      </div>
      <button class="modal-close" onclick="closeModal('modal-ciencias')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-content">
      <div class="modal-about"><p>Curso de formação geral com ênfase em Ciências Exactas e Tecnologia, preparando os alunos para o ingresso em Medicina, Engenharia, Arquitectura e demais cursos superiores de base científica.</p></div>
      <div class="grades-grid">
        <div class="grade-block">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>10ª Classe</h6></div>
          <ul class="discipline-list">
            <li>Língua Portuguesa</li><li>Matemática</li><li>Física</li><li>Química</li><li>Biologia</li><li>Inglês</li><li>Educação Física</li><li>Ed. Moral e Cívica</li>
          </ul>
        </div>
        <div class="grade-block">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>11ª Classe</h6></div>
          <ul class="discipline-list">
            <li>Língua Portuguesa</li><li>Matemática</li><li>Física</li><li>Química</li><li>Biologia</li><li>Filosofia</li><li>Geografia</li><li>História</li>
          </ul>
        </div>
        <div class="grade-block full">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>12ª Classe</h6></div>
          <ul class="discipline-list" style="display:grid;grid-template-columns:1fr 1fr;gap:0.4rem 2rem">
            <li>Língua Portuguesa</li><li>Preparação para Exames</li><li>Matemática</li><li>Monografia/TCC</li><li>Disciplinas de Opção</li><li>Orientação Vocacional</li>
          </ul>
        </div>
      </div>
      <div class="modal-info-bar"><i class="fas fa-info-circle"></i><span><strong>Duração:</strong> 3 anos — 10ª à 12ª Classe | <strong>Certificação:</strong> Reconhecida pelo MINED</span></div>
    </div>
    <div class="modal-foot">
      <button class="btn-modal-close" onclick="closeModal('modal-ciencias')">Fechar</button>
      <a href="inscricao.html" class="btn-modal-enroll"><i class="fas fa-user-plus"></i> Inscrever-se neste Curso</a>
    </div>
  </div>
</div>

<!-- Modal Humanidades -->
<div class="modal-overlay" id="modal-humanidades">
  <div class="modal-box">
    <div class="modal-head">
      <div style="display:flex;align-items:flex-start;gap:1rem;flex:1">
        <div class="modal-head-icon"><i class="fas fa-book"></i></div>
        <div class="modal-head-text"><h3>Humanidades e Ciências Sociais</h3><p>Grade curricular · 3 anos · 10ª à 12ª Classe</p></div>
      </div>
      <button class="modal-close" onclick="closeModal('modal-humanidades')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-content">
      <div class="modal-about"><p>Formação com ênfase em pensamento crítico, Literatura, Filosofia, História e Línguas. Prepara para Direito, Comunicação Social, Relações Internacionais e Ciências Sociais.</p></div>
      <div class="grades-grid">
        <div class="grade-block">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>10ª Classe</h6></div>
          <ul class="discipline-list">
            <li>Língua Portuguesa</li><li>Inglês / Francês</li><li>História</li><li>Geografia</li><li>Matemática</li><li>Filosofia</li><li>Educação Física</li><li>Ed. Moral e Cívica</li>
          </ul>
        </div>
        <div class="grade-block">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>11ª Classe</h6></div>
          <ul class="discipline-list">
            <li>Literatura Portuguesa</li><li>Inglês Avançado</li><li>Sociologia</li><li>História de Angola</li><li>Filosofia</li><li>Psicologia</li><li>Arte e Cultura</li><li>Matemática</li>
          </ul>
        </div>
        <div class="grade-block full">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>12ª Classe</h6></div>
          <ul class="discipline-list" style="display:grid;grid-template-columns:1fr 1fr;gap:0.4rem 2rem">
            <li>Língua Portuguesa</li><li>Preparação para Exames</li><li>Disciplinas de Opção</li><li>Monografia/TCC</li><li>Inglês / Francês</li><li>Orientação Vocacional</li>
          </ul>
        </div>
      </div>
      <div class="modal-info-bar"><i class="fas fa-info-circle"></i><span><strong>Duração:</strong> 3 anos — 10ª à 12ª Classe | <strong>Certificação:</strong> Reconhecida pelo MINED</span></div>
    </div>
    <div class="modal-foot">
      <button class="btn-modal-close" onclick="closeModal('modal-humanidades')">Fechar</button>
      <a href="inscricao.html" class="btn-modal-enroll"><i class="fas fa-user-plus"></i> Inscrever-se neste Curso</a>
    </div>
  </div>
</div>

<!-- Modal Económico -->
<div class="modal-overlay" id="modal-economico">
  <div class="modal-box">
    <div class="modal-head">
      <div style="display:flex;align-items:flex-start;gap:1rem;flex:1">
        <div class="modal-head-icon"><i class="fas fa-chart-line"></i></div>
        <div class="modal-head-text"><h3>Ciências Económicas e Jurídicas</h3><p>Grade curricular · 3 anos · 10ª à 12ª Classe</p></div>
      </div>
      <button class="modal-close" onclick="closeModal('modal-economico')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-content">
      <div class="modal-about"><p>Formação em Economia, Direito, Contabilidade e Gestão Empresarial. Prepara para Economia, Gestão de Empresas, Finanças, Direito e Administração Pública.</p></div>
      <div class="grades-grid">
        <div class="grade-block">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>10ª Classe</h6></div>
          <ul class="discipline-list">
            <li>Língua Portuguesa</li><li>Matemática</li><li>Economia</li><li>Contabilidade</li><li>Inglês</li><li>Direito</li><li>Educação Física</li><li>Ed. Moral e Cívica</li>
          </ul>
        </div>
        <div class="grade-block">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>11ª Classe</h6></div>
          <ul class="discipline-list">
            <li>Língua Portuguesa</li><li>Matemática Financeira</li><li>Economia Política</li><li>Gestão Empresarial</li><li>Direito Comercial</li><li>Estatística</li><li>Filosofia</li><li>Inglês</li>
          </ul>
        </div>
        <div class="grade-block full">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>12ª Classe</h6></div>
          <ul class="discipline-list" style="display:grid;grid-template-columns:1fr 1fr;gap:0.4rem 2rem">
            <li>Língua Portuguesa</li><li>Preparação para Exames</li><li>Economia Aplicada</li><li>Monografia/TCC</li><li>Disciplinas de Opção</li><li>Orientação Vocacional</li>
          </ul>
        </div>
      </div>
      <div class="modal-info-bar"><i class="fas fa-info-circle"></i><span><strong>Duração:</strong> 3 anos — 10ª à 12ª Classe | <strong>Certificação:</strong> Reconhecida pelo MINED</span></div>
    </div>
    <div class="modal-foot">
      <button class="btn-modal-close" onclick="closeModal('modal-economico')">Fechar</button>
      <a href="inscricao.html" class="btn-modal-enroll"><i class="fas fa-user-plus"></i> Inscrever-se neste Curso</a>
    </div>
  </div>
</div>

<!-- Modal Técnico -->
<div class="modal-overlay" id="modal-tecnico">
  <div class="modal-box">
    <div class="modal-head">
      <div style="display:flex;align-items:flex-start;gap:1rem;flex:1">
        <div class="modal-head-icon"><i class="fas fa-microchip"></i></div>
        <div class="modal-head-text"><h3>Ensino Técnico-Profissional</h3><p>Grade curricular · 4 anos · 10ª à 13ª Classe</p></div>
      </div>
      <button class="modal-close" onclick="closeModal('modal-tecnico')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-content">
      <div class="modal-about"><p>Formação técnica especializada em Informática, Eletrónica e Mecânica, com forte componente prática em laboratório e estágio profissional integrado no último ano.</p></div>
      <div class="grades-grid">
        <div class="grade-block">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>10ª e 11ª Classe</h6></div>
          <ul class="discipline-list">
            <li>Língua Portuguesa</li><li>Matemática</li><li>Física</li><li>Inglês Técnico</li><li>Fundamentos de TI</li><li>Eletrónica Básica</li><li>Educação Física</li><li>Ed. Moral e Cívica</li>
          </ul>
        </div>
        <div class="grade-block">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>12ª Classe</h6></div>
          <ul class="discipline-list">
            <li>Programação</li><li>Redes de Computadores</li><li>Sistemas Operativos</li><li>Bases de Dados</li><li>Projecto Técnico</li><li>Segurança Informática</li><li>Matemática Aplicada</li><li>Inglês</li>
          </ul>
        </div>
        <div class="grade-block full">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>13ª Classe — Estágio e Conclusão</h6></div>
          <ul class="discipline-list" style="display:grid;grid-template-columns:1fr 1fr;gap:0.4rem 2rem">
            <li>Estágio Profissional</li><li>Trabalho de Fim de Curso (TFC)</li><li>Gestão de Projectos</li><li>Preparação para o Mercado</li><li>Empreendedorismo</li><li>Defesa do TFC</li>
          </ul>
        </div>
      </div>
      <div class="modal-info-bar"><i class="fas fa-info-circle"></i><span><strong>Duração:</strong> 4 anos — 10ª à 13ª Classe | <strong>Certificação:</strong> Técnica + MINED</span></div>
    </div>
    <div class="modal-foot">
      <button class="btn-modal-close" onclick="closeModal('modal-tecnico')">Fechar</button>
      <a href="inscricao.html" class="btn-modal-enroll"><i class="fas fa-user-plus"></i> Inscrever-se neste Curso</a>
    </div>
  </div>
</div>

<!-- Modal Profissional -->
<div class="modal-overlay" id="modal-profissional">
  <div class="modal-box">
    <div class="modal-head">
      <div style="display:flex;align-items:flex-start;gap:1rem;flex:1">
        <div class="modal-head-icon"><i class="fas fa-briefcase"></i></div>
        <div class="modal-head-text"><h3>Formação Profissional</h3><p>Grade curricular · 3 anos · 10ª à 12ª Classe</p></div>
      </div>
      <button class="modal-close" onclick="closeModal('modal-profissional')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-content">
      <div class="modal-about"><p>Qualificação profissional rápida e prática em Saúde, Administração e Turismo, com inserção directa no mercado de trabalho angolano através de convénios com empregadores.</p></div>
      <div class="grades-grid">
        <div class="grade-block">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>10ª Classe</h6></div>
          <ul class="discipline-list">
            <li>Língua Portuguesa</li><li>Matemática</li><li>Inglês</li><li>Introdução à Área</li><li>Informática Básica</li><li>Comunicação</li><li>Educação Física</li><li>Ed. Moral e Cívica</li>
          </ul>
        </div>
        <div class="grade-block">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>11ª Classe</h6></div>
          <ul class="discipline-list">
            <li>Língua Portuguesa</li><li>Prática Profissional I</li><li>Ética Profissional</li><li>Gestão e Organização</li><li>Legislação Laboral</li><li>Inglês Profissional</li><li>Empreendedorismo</li><li>Matemática</li>
          </ul>
        </div>
        <div class="grade-block full">
          <div class="grade-block-header"><div class="grade-block-icon"><i class="fas fa-graduation-cap"></i></div><h6>12ª Classe</h6></div>
          <ul class="discipline-list" style="display:grid;grid-template-columns:1fr 1fr;gap:0.4rem 2rem">
            <li>Prática Profissional II</li><li>Monografia/TCC</li><li>Estágio Supervisionado</li><li>Preparação para o Mercado</li><li>Disciplinas de Opção</li><li>Defesa do TCC</li>
          </ul>
        </div>
      </div>
      <div class="modal-info-bar"><i class="fas fa-info-circle"></i><span><strong>Duração:</strong> 3 anos — 10ª à 12ª Classe | <strong>Certificação:</strong> Profissional + MINED</span></div>
    </div>
    <div class="modal-foot">
      <button class="btn-modal-close" onclick="closeModal('modal-profissional')">Fechar</button>
      <a href="inscricao.html" class="btn-modal-enroll"><i class="fas fa-user-plus"></i> Inscrever-se neste Curso</a>
    </div>
  </div>
</div>

<script>
// Navbar scroll
const nav = document.getElementById('nav');
window.addEventListener('scroll', () => {
  nav.classList.toggle('scrolled', window.scrollY > 80);
  document.getElementById('backTop').classList.toggle('show', window.scrollY > 400);
  // Adjust filter bar top when nav shrinks
  document.getElementById('filterBar').style.top = window.scrollY > 80 ? '60px' : '72px';
});

// Mobile menu
function toggleMenu() { document.getElementById('mobileNav').classList.toggle('open'); }

// Reveal on scroll
const reveals = document.querySelectorAll('[data-reveal]');
const observer = new IntersectionObserver(entries => {
  entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('revealed'); });
}, { threshold: 0.08 });
reveals.forEach(el => observer.observe(el));

// Filter pills
const pills = document.querySelectorAll('.filter-pill');
const cards = document.querySelectorAll('.course-card');
const countEl = document.getElementById('visibleCount');

pills.forEach(pill => {
  pill.addEventListener('click', () => {
    pills.forEach(p => p.classList.remove('active'));
    pill.classList.add('active');
    const filter = pill.dataset.filter;
    let count = 0;
    cards.forEach(card => {
      const match = filter === 'all' || card.dataset.category === filter;
      card.style.transition = 'all 0.35s';
      if(match) {
        card.classList.remove('hidden');
        card.style.opacity = '1'; card.style.transform = '';
        count++;
      } else {
        card.style.opacity = '0'; card.style.transform = 'scale(0.95)';
        setTimeout(() => { if(!match) card.classList.add('hidden'); }, 300);
      }
    });
    if(filter === 'all') count = cards.length;
    setTimeout(() => countEl.textContent = count, 350);
  });
});

// Modal
function openModal(id) {
  document.getElementById(id).classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeModal(id) {
  document.getElementById(id).classList.remove('open');
  document.body.style.overflow = '';
}
// Close on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', e => {
    if(e.target === overlay) {
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    }
  });
});
// Close on Escape
document.addEventListener('keydown', e => {
  if(e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.open').forEach(m => {
      m.classList.remove('open');
      document.body.style.overflow = '';
    });
  }
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const href = a.getAttribute('href');
    if(href === '#') return;
    const t = document.querySelector(href);
    if(t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
  });
});
</script>
</body>
</html>