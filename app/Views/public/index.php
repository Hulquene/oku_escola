<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Escola Angolana Modelo</title>
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

h1,h2,h3,h4,h5 { font-family: 'Syne', sans-serif; }

/* ===== NAVBAR ===== */
nav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
  background: rgba(255,255,255,0.92);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(221,72,20,0.08);
  padding: 0 2rem;
  height: 72px;
  display: flex; align-items: center; justify-content: space-between;
  transition: all 0.3s ease;
}
nav.scrolled {
  height: 60px;
  box-shadow: 0 4px 30px rgba(0,0,0,0.08);
}
.nav-logo {
  display: flex; align-items: center; gap: 0.75rem;
  text-decoration: none;
}
.nav-logo-icon {
  width: 44px; height: 44px;
  background: var(--fire);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  color: white; font-size: 1.2rem;
  box-shadow: 0 4px 12px rgba(221,72,20,0.35);
}
.nav-logo-text { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.1rem; color: var(--ink); line-height: 1.1; }
.nav-logo-text span { display: block; font-weight: 400; font-size: 0.7rem; color: var(--ash); letter-spacing: 0.05em; font-family: 'DM Sans', sans-serif; }

.nav-links { display: flex; align-items: center; gap: 0.25rem; list-style: none; }
.nav-links a {
  text-decoration: none; color: var(--slate);
  font-weight: 500; font-size: 0.9rem;
  padding: 0.5rem 0.9rem; border-radius: 8px;
  transition: all 0.2s;
}
.nav-links a:hover, .nav-links a.active { color: var(--fire); background: rgba(221,72,20,0.07); }
.nav-actions { display: flex; align-items: center; gap: 0.75rem; }
.btn-ghost {
  padding: 0.5rem 1rem; border-radius: 8px;
  border: 1.5px solid var(--mist); color: var(--slate);
  background: none; cursor: pointer;
  font-family: 'DM Sans', sans-serif; font-weight: 500; font-size: 0.875rem;
  text-decoration: none; transition: all 0.2s;
}
.btn-ghost:hover { border-color: var(--fire); color: var(--fire); }
.btn-fire {
  padding: 0.5rem 1.2rem; border-radius: 8px;
  background: var(--fire); color: white; border: none;
  cursor: pointer; font-family: 'DM Sans', sans-serif;
  font-weight: 600; font-size: 0.875rem;
  text-decoration: none; transition: all 0.2s;
  box-shadow: 0 2px 8px rgba(221,72,20,0.3);
}
.btn-fire:hover { background: var(--fire-deep); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(221,72,20,0.4); }

.hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: 4px; }
.hamburger span { display: block; width: 24px; height: 2px; background: var(--ink); border-radius: 2px; transition: all 0.3s; }

/* ===== HERO ===== */
.hero {
  min-height: 100vh;
  background: var(--night);
  position: relative;
  display: flex; align-items: center;
  overflow: hidden;
  padding-top: 72px;
}

.hero-bg-shapes {
  position: absolute; inset: 0; overflow: hidden;
}
.hero-orb {
  position: absolute; border-radius: 50%;
  filter: blur(80px); opacity: 0.4;
  animation: drift 12s ease-in-out infinite;
}
.hero-orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, #dd4814, transparent 70%); top: -200px; right: -150px; animation-delay: 0s; }
.hero-orb-2 { width: 400px; height: 400px; background: radial-gradient(circle, #1e3a5f, transparent 70%); bottom: -100px; left: 10%; animation-delay: -4s; }
.hero-orb-3 { width: 300px; height: 300px; background: radial-gradient(circle, #f59e0b, transparent 70%); top: 40%; left: 40%; opacity: 0.15; animation-delay: -8s; }

.hero-grid {
  position: absolute; inset: 0;
  background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
  background-size: 60px 60px;
}

@keyframes drift {
  0%, 100% { transform: translate(0, 0) scale(1); }
  33% { transform: translate(30px, -30px) scale(1.05); }
  66% { transform: translate(-20px, 20px) scale(0.95); }
}

.hero-content {
  position: relative; z-index: 2;
  width: 100%; max-width: 1280px; margin: 0 auto;
  padding: 4rem 2rem;
  display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;
}

.hero-badge {
  display: inline-flex; align-items: center; gap: 0.5rem;
  background: rgba(221,72,20,0.15); border: 1px solid rgba(221,72,20,0.3);
  color: #ff8c5a; padding: 0.4rem 1rem; border-radius: 50px;
  font-size: 0.8rem; font-weight: 500; letter-spacing: 0.05em;
  margin-bottom: 1.5rem;
}
.hero-badge i { font-size: 0.7rem; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

.hero-title {
  font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 800;
  color: white; line-height: 1.1; margin-bottom: 1.5rem;
  letter-spacing: -0.02em;
}
.hero-title .accent { color: var(--fire-glow); }
.hero-title .line2 { display: block; }

.hero-sub {
  color: rgba(255,255,255,0.6); font-size: 1.1rem;
  line-height: 1.7; max-width: 520px; margin-bottom: 2.5rem;
}

.hero-cta { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 3.5rem; }
.btn-hero-primary {
  padding: 0.875rem 2rem; background: var(--fire); color: white;
  border-radius: 12px; font-weight: 600; font-size: 1rem;
  text-decoration: none; transition: all 0.3s;
  box-shadow: 0 8px 24px rgba(221,72,20,0.4);
  display: flex; align-items: center; gap: 0.5rem;
}
.btn-hero-primary:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(221,72,20,0.5); background: var(--fire-glow); }
.btn-hero-secondary {
  padding: 0.875rem 2rem; background: rgba(255,255,255,0.07);
  border: 1.5px solid rgba(255,255,255,0.15); color: white;
  border-radius: 12px; font-weight: 500; font-size: 1rem;
  text-decoration: none; transition: all 0.3s; backdrop-filter: blur(10px);
  display: flex; align-items: center; gap: 0.5rem;
}
.btn-hero-secondary:hover { background: rgba(255,255,255,0.12); transform: translateY(-3px); }

.hero-stats { display: flex; gap: 2.5rem; }
.hero-stat { }
.hero-stat-num { font-family: 'Syne', sans-serif; font-size: 2rem; font-weight: 800; color: white; line-height: 1; }
.hero-stat-num span { color: var(--fire-glow); }
.hero-stat-lbl { font-size: 0.8rem; color: rgba(255,255,255,0.45); margin-top: 0.25rem; }

.hero-visual { position: relative; }
.hero-img-wrap {
  position: relative; border-radius: 24px; overflow: hidden;
  box-shadow: 0 40px 80px rgba(0,0,0,0.5);
}
.hero-img-wrap img { width: 100%; height: 480px; object-fit: cover; display: block; }
.hero-img-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to bottom, transparent 50%, rgba(13,17,23,0.6) 100%);
}

.hero-card-float {
  position: absolute;
  background: rgba(255,255,255,0.08);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 16px; padding: 1rem 1.25rem;
  color: white;
  animation: float 6s ease-in-out infinite;
}
.hero-card-1 { bottom: -20px; left: -40px; animation-delay: 0s; }
.hero-card-2 { top: 30px; right: -30px; animation-delay: -3s; }
.hero-card-val { font-family: 'Syne', sans-serif; font-size: 1.5rem; font-weight: 800; }
.hero-card-lbl { font-size: 0.75rem; opacity: 0.6; }
.hero-card-icon { font-size: 1.5rem; margin-bottom: 0.25rem; }
@keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }

/* ===== SECTIONS SHARED ===== */
.section { padding: 6rem 2rem; }
.section-inner { max-width: 1280px; margin: 0 auto; }
.section-alt { background: var(--snow); }

.tag {
  display: inline-flex; align-items: center; gap: 0.4rem;
  background: rgba(221,72,20,0.08); color: var(--fire);
  padding: 0.35rem 0.9rem; border-radius: 50px;
  font-size: 0.8rem; font-weight: 600; letter-spacing: 0.04em;
  text-transform: uppercase; margin-bottom: 1rem;
}
.section-head { margin-bottom: 3.5rem; }
.section-head h2 {
  font-size: clamp(1.8rem, 3vw, 2.75rem); font-weight: 800;
  color: var(--ink); line-height: 1.2; letter-spacing: -0.02em;
}
.section-head h2 .accent { color: var(--fire); }
.section-head p {
  color: var(--ash); font-size: 1.05rem; margin-top: 0.75rem;
  max-width: 600px; line-height: 1.7;
}
.centered { text-align: center; }
.centered p { margin-left: auto; margin-right: auto; }

/* ===== METRICS STRIP ===== */
.metrics {
  background: var(--ink);
  padding: 3rem 2rem;
}
.metrics-inner {
  max-width: 1280px; margin: 0 auto;
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem;
}
.metric {
  text-align: center; padding: 1.5rem;
  border-right: 1px solid rgba(255,255,255,0.07);
}
.metric:last-child { border-right: none; }
.metric-icon {
  width: 52px; height: 52px; background: rgba(221,72,20,0.15);
  border-radius: 14px; display: flex; align-items: center; justify-content: center;
  margin: 0 auto 1rem; color: var(--fire); font-size: 1.3rem;
  border: 1px solid rgba(221,72,20,0.2);
}
.metric-num { font-family: 'Syne', sans-serif; font-size: 2.5rem; font-weight: 800; color: white; line-height: 1; }
.metric-num sup { font-size: 1rem; color: var(--fire); }
.metric-lbl { color: rgba(255,255,255,0.45); font-size: 0.875rem; margin-top: 0.4rem; }

/* ===== ABOUT ===== */
.about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: center; }
.about-imgs { position: relative; }
.about-img-main {
  width: 100%; border-radius: 20px; object-fit: cover;
  height: 520px; display: block;
  box-shadow: 0 30px 60px rgba(0,0,0,0.12);
}
.about-img-badge {
  position: absolute; bottom: -20px; right: -20px;
  background: var(--fire);
  border-radius: 20px; padding: 1.5rem 2rem;
  color: white; text-align: center;
  box-shadow: 0 16px 40px rgba(221,72,20,0.4);
}
.badge-num { font-family: 'Syne', sans-serif; font-size: 2.5rem; font-weight: 800; line-height: 1; }
.badge-txt { font-size: 0.8rem; opacity: 0.85; }

.about-features { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin: 2rem 0; }
.feat-item {
  display: flex; gap: 1rem; align-items: flex-start;
  background: var(--snow); border-radius: 14px; padding: 1.25rem;
  border: 1px solid var(--mist); transition: all 0.3s;
}
.feat-item:hover { border-color: rgba(221,72,20,0.3); transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
.feat-icon {
  width: 40px; height: 40px; min-width: 40px; background: rgba(221,72,20,0.1);
  border-radius: 10px; display: flex; align-items: center; justify-content: center;
  color: var(--fire); font-size: 1rem;
}
.feat-label { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.9rem; color: var(--ink); }
.feat-desc { font-size: 0.8rem; color: var(--ash); margin-top: 0.2rem; line-height: 1.5; }

/* ===== COURSES ===== */
.courses-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.75rem; }
.course-card {
  background: var(--white); border-radius: 20px; overflow: hidden;
  border: 1px solid var(--mist); transition: all 0.3s;
  display: flex; flex-direction: column;
}
.course-card:hover { transform: translateY(-8px); box-shadow: 0 24px 50px rgba(0,0,0,0.1); border-color: rgba(221,72,20,0.2); }
.course-thumb { position: relative; height: 200px; overflow: hidden; }
.course-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
.course-card:hover .course-thumb img { transform: scale(1.08); }
.course-chip {
  position: absolute; top: 1rem; left: 1rem;
  background: var(--fire); color: white;
  padding: 0.3rem 0.85rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600;
}
.course-body { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; }
.course-name { font-family: 'Syne', sans-serif; font-size: 1.15rem; font-weight: 700; color: var(--ink); margin-bottom: 0.5rem; }
.course-desc { color: var(--ash); font-size: 0.875rem; line-height: 1.6; flex: 1; }
.course-meta {
  display: flex; gap: 1rem; padding: 1rem 0;
  border-top: 1px solid var(--mist); border-bottom: 1px solid var(--mist);
  margin: 1rem 0;
}
.course-meta span { font-size: 0.8rem; color: var(--ash); display: flex; align-items: center; gap: 0.4rem; }
.course-meta i { color: var(--fire); }
.course-foot { display: flex; align-items: center; justify-content: space-between; }
.course-price { font-family: 'Syne', sans-serif; font-weight: 700; color: var(--fire); font-size: 1rem; }
.btn-outline-fire {
  padding: 0.45rem 1.1rem; border-radius: 8px;
  border: 1.5px solid var(--fire); color: var(--fire);
  background: none; font-weight: 600; font-size: 0.85rem;
  text-decoration: none; transition: all 0.2s;
}
.btn-outline-fire:hover { background: var(--fire); color: white; }

/* ===== FEATURES ===== */
.features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
.feat-card {
  background: white; border-radius: 20px; padding: 2rem;
  border: 1px solid var(--mist); transition: all 0.3s;
  text-align: center;
}
.feat-card:hover { transform: translateY(-8px); box-shadow: 0 24px 50px rgba(0,0,0,0.08); border-color: rgba(221,72,20,0.25); }
.feat-card-icon {
  width: 70px; height: 70px; margin: 0 auto 1.5rem;
  background: linear-gradient(135deg, rgba(221,72,20,0.1), rgba(221,72,20,0.05));
  border-radius: 20px; display: flex; align-items: center; justify-content: center;
  font-size: 1.75rem; color: var(--fire);
  border: 1px solid rgba(221,72,20,0.15);
  transition: all 0.3s;
}
.feat-card:hover .feat-card-icon { background: var(--fire); color: white; border-color: var(--fire); transform: rotate(8deg); }
.feat-card h4 { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 700; color: var(--ink); margin-bottom: 0.75rem; }
.feat-card p { font-size: 0.9rem; color: var(--ash); line-height: 1.65; }

/* ===== TESTIMONIALS ===== */
.testimonials-outer { background: var(--ink); padding: 6rem 2rem; }
.testimonials-inner { max-width: 1280px; margin: 0 auto; }
.testimonials-inner .section-head h2 { color: white; }
.testimonials-inner .section-head p { color: rgba(255,255,255,0.5); }
.testimonials-inner .tag { background: rgba(221,72,20,0.2); }

.testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 3rem; }
.testi-card {
  background: rgba(255,255,255,0.05); border-radius: 20px; padding: 2rem;
  border: 1px solid rgba(255,255,255,0.08); transition: all 0.3s; position: relative;
}
.testi-card:hover { background: rgba(255,255,255,0.08); border-color: rgba(221,72,20,0.3); transform: translateY(-6px); }
.testi-quote {
  font-size: 4rem; line-height: 1; color: var(--fire); opacity: 0.3;
  font-family: Georgia, serif; position: absolute; top: 1rem; right: 1.5rem;
}
.testi-stars { color: var(--gold); font-size: 0.85rem; margin-bottom: 1rem; }
.testi-text { color: rgba(255,255,255,0.75); font-size: 0.9rem; line-height: 1.75; margin-bottom: 1.5rem; font-style: italic; }
.testi-author { display: flex; align-items: center; gap: 1rem; }
.testi-author img {
  width: 50px; height: 50px; border-radius: 50%; object-fit: cover;
  border: 2px solid rgba(221,72,20,0.5);
}
.testi-name { font-family: 'Syne', sans-serif; font-weight: 700; color: white; font-size: 0.95rem; }
.testi-role { font-size: 0.78rem; color: rgba(255,255,255,0.4); }

/* ===== CTA ===== */
.cta-outer {
  background: var(--fire);
  position: relative; overflow: hidden; padding: 6rem 2rem;
}
.cta-outer::before {
  content: '';
  position: absolute; inset: 0;
  background: repeating-linear-gradient(45deg, transparent, transparent 40px, rgba(255,255,255,0.03) 40px, rgba(255,255,255,0.03) 80px);
}
.cta-inner { max-width: 800px; margin: 0 auto; text-align: center; position: relative; z-index: 2; }
.cta-inner h2 { font-size: clamp(1.75rem, 4vw, 3rem); font-weight: 800; color: white; margin-bottom: 1rem; }
.cta-inner p { color: rgba(255,255,255,0.8); font-size: 1.1rem; margin-bottom: 2.5rem; }
.cta-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
.btn-cta-white {
  padding: 0.9rem 2.25rem; background: white; color: var(--fire);
  border-radius: 12px; font-weight: 700; font-size: 1rem;
  text-decoration: none; transition: all 0.3s;
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}
.btn-cta-white:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(0,0,0,0.2); }
.btn-cta-outline {
  padding: 0.9rem 2.25rem; background: transparent;
  border: 2px solid rgba(255,255,255,0.4); color: white;
  border-radius: 12px; font-weight: 600; font-size: 1rem;
  text-decoration: none; transition: all 0.3s;
}
.btn-cta-outline:hover { background: rgba(255,255,255,0.1); border-color: white; transform: translateY(-3px); }

/* ===== FOOTER ===== */
footer {
  background: var(--night);
  padding: 5rem 2rem 2rem;
  border-top: 1px solid rgba(255,255,255,0.06);
}
.footer-inner { max-width: 1280px; margin: 0 auto; }
.footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 4rem; margin-bottom: 4rem; }
.footer-brand { }
.footer-brand .nav-logo { margin-bottom: 1.25rem; display: flex; }
.footer-brand p { color: rgba(255,255,255,0.45); font-size: 0.875rem; line-height: 1.7; max-width: 300px; }
.footer-socials { display: flex; gap: 0.75rem; margin-top: 1.75rem; }
.social-btn {
  width: 38px; height: 38px; border-radius: 10px;
  background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.5);
  display: flex; align-items: center; justify-content: center; font-size: 0.9rem;
  text-decoration: none; transition: all 0.2s; border: 1px solid rgba(255,255,255,0.08);
}
.social-btn:hover { background: var(--fire); color: white; border-color: var(--fire); transform: translateY(-3px); }

.footer-col h5 {
  font-family: 'Syne', sans-serif; font-size: 0.95rem; font-weight: 700;
  color: white; margin-bottom: 1.25rem;
  padding-bottom: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.08);
}
.footer-links { list-style: none; display: flex; flex-direction: column; gap: 0.6rem; }
.footer-links a { color: rgba(255,255,255,0.45); font-size: 0.875rem; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
.footer-links a::before { content: '→'; opacity: 0; transform: translateX(-5px); transition: all 0.2s; }
.footer-links a:hover { color: var(--fire); padding-left: 0.25rem; }
.footer-links a:hover::before { opacity: 1; transform: translateX(0); }

.footer-contact-list { list-style: none; display: flex; flex-direction: column; gap: 1rem; }
.footer-contact-list li { display: flex; gap: 0.75rem; color: rgba(255,255,255,0.45); font-size: 0.875rem; align-items: flex-start; }
.footer-contact-list i { color: var(--fire); margin-top: 3px; flex-shrink: 0; }

.footer-bottom {
  border-top: 1px solid rgba(255,255,255,0.06);
  padding-top: 2rem;
  display: flex; justify-content: space-between; align-items: center;
  color: rgba(255,255,255,0.3); font-size: 0.8rem;
}
.footer-bottom a { color: rgba(255,255,255,0.5); text-decoration: none; }
.footer-bottom a:hover { color: var(--fire); }

/* ===== BACK TO TOP ===== */
.back-top {
  position: fixed; bottom: 2rem; right: 2rem;
  width: 48px; height: 48px; border-radius: 12px;
  background: var(--fire); color: white;
  display: flex; align-items: center; justify-content: center;
  text-decoration: none; box-shadow: 0 8px 24px rgba(221,72,20,0.4);
  opacity: 0; visibility: hidden; transform: translateY(10px);
  transition: all 0.3s; z-index: 999; font-size: 1rem;
}
.back-top.show { opacity: 1; visibility: visible; transform: translateY(0); }
.back-top:hover { transform: translateY(-4px); }

/* ===== ANIMATE ON SCROLL ===== */
[data-reveal] {
  opacity: 0; transform: translateY(30px);
  transition: opacity 0.7s ease, transform 0.7s ease;
}
[data-reveal].revealed { opacity: 1; transform: translateY(0); }
[data-reveal][data-delay="1"] { transition-delay: 0.1s; }
[data-reveal][data-delay="2"] { transition-delay: 0.2s; }
[data-reveal][data-delay="3"] { transition-delay: 0.3s; }
[data-reveal][data-delay="4"] { transition-delay: 0.4s; }
[data-reveal][data-delay="5"] { transition-delay: 0.5s; }

/* ===== RESPONSIVE ===== */
@media (max-width: 1024px) {
  .hero-content { grid-template-columns: 1fr; gap: 3rem; }
  .hero-visual { display: none; }
  .about-grid { grid-template-columns: 1fr; gap: 3rem; }
  .courses-grid { grid-template-columns: repeat(2, 1fr); }
  .features-grid { grid-template-columns: repeat(2, 1fr); }
  .testi-grid { grid-template-columns: 1fr; }
  .footer-grid { grid-template-columns: 1fr 1fr; gap: 2.5rem; }
  .metrics-inner { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .nav-links, .nav-actions { display: none; }
  .hamburger { display: flex; }
  .courses-grid { grid-template-columns: 1fr; }
  .features-grid { grid-template-columns: 1fr; }
  .about-features { grid-template-columns: 1fr; }
  .footer-grid { grid-template-columns: 1fr; gap: 2rem; }
  .metrics-inner { grid-template-columns: repeat(2, 1fr); }
  .hero-stats { flex-wrap: wrap; gap: 1.5rem; }
  .footer-bottom { flex-direction: column; gap: 0.75rem; text-align: center; }
  .hero-card-1, .hero-card-2 { display: none; }
  .testi-grid { grid-template-columns: 1fr; }
}

/* Mobile nav */
.mobile-nav {
  display: none; position: fixed; inset: 0; background: var(--night); z-index: 999;
  padding: 2rem; flex-direction: column;
}
.mobile-nav.open { display: flex; }
.mobile-nav-close { align-self: flex-end; background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer; }
.mobile-nav ul { list-style: none; display: flex; flex-direction: column; gap: 0.5rem; margin-top: 3rem; }
.mobile-nav ul a { color: white; text-decoration: none; font-family: 'Syne', sans-serif; font-size: 1.5rem; font-weight: 700; padding: 0.75rem 0; display: block; border-bottom: 1px solid rgba(255,255,255,0.06); }
.mobile-nav ul a:hover { color: var(--fire); }
.mobile-nav-btns { display: flex; flex-direction: column; gap: 1rem; margin-top: 2rem; }

/* Counter anim */
.counter { display: inline-block; }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav id="nav">
  <a href="#" class="nav-logo">
    <div class="nav-logo-icon"><i class="fas fa-graduation-cap"></i></div>
    <div class="nav-logo-text">Escola Angolana Modelo <span>Excelência em Educação</span></div>
  </a>
  <ul class="nav-links">
    <li><a href="#hero" class="active">Início</a></li>
    <li><a href="#cursos">Cursos</a></li>
    <li><a href="#sobre">Sobre</a></li>
    <li><a href="#depoimentos">Depoimentos</a></li>
    <li><a href="#contato">Contato</a></li>
  </ul>
  <div class="nav-actions">
    <a href="/inscricao" class="btn-ghost"><i class="fas fa-user-plus" style="margin-right:6px"></i>Inscrever-se</a>
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
    <li><a href="#hero" onclick="toggleMenu()">Início</a></li>
    <li><a href="#cursos" onclick="toggleMenu()">Cursos</a></li>
    <li><a href="#sobre" onclick="toggleMenu()">Sobre</a></li>
    <li><a href="#depoimentos" onclick="toggleMenu()">Depoimentos</a></li>
    <li><a href="#contato" onclick="toggleMenu()">Contato</a></li>
  </ul>
  <div class="mobile-nav-btns">
    <a href="/inscricao" class="btn-hero-secondary" style="text-align:center">Inscrever-se</a>
    <a href="/auth/students" class="btn-hero-primary" style="justify-content:center">Entrar</a>
  </div>
</div>

<!-- HERO -->
<section class="hero" id="hero">
  <div class="hero-bg-shapes">
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    <div class="hero-orb hero-orb-3"></div>
    <div class="hero-grid"></div>
  </div>
  <div class="hero-content">
    <div>
      <div class="hero-badge"><i class="fas fa-circle"></i> Matrículas Abertas 2025</div>
      <h1 class="hero-title">
        Formando o <span class="accent">Futuro</span>
        <span class="line2">de Angola</span>
      </h1>
      <p class="hero-sub">Educação de excelência alinhada com os desafios do século XXI. Líderes, inovadores e cidadãos comprometidos nascem aqui.</p>
      <div class="hero-cta">
        <a href="/inscricao" class="btn-hero-primary"><i class="fas fa-pen"></i> Inscreva-se Agora</a>
        <a href="/cursos" class="btn-hero-secondary"><i class="fas fa-book"></i> Ver Cursos</a>
      </div>
      <div class="hero-stats">
        <div class="hero-stat">
          <div class="hero-stat-num"><span class="counter" data-target="1500">0</span><span>+</span></div>
          <div class="hero-stat-lbl">Alunos</div>
        </div>
        <div class="hero-stat">
          <div class="hero-stat-num"><span class="counter" data-target="80">0</span><span>+</span></div>
          <div class="hero-stat-lbl">Professores</div>
        </div>
        <div class="hero-stat">
          <div class="hero-stat-num"><span class="counter" data-target="95">0</span><span>%</span></div>
          <div class="hero-stat-lbl">Aprovação</div>
        </div>
        <div class="hero-stat">
          <div class="hero-stat-num"><span class="counter" data-target="15">0</span></div>
          <div class="hero-stat-lbl">Anos</div>
        </div>
      </div>
    </div>
    <div class="hero-visual">
      <div class="hero-img-wrap">
        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&q=80&auto=format&fit=crop" alt="Estudantes">
        <div class="hero-img-overlay"></div>
      </div>
      <div class="hero-card-float hero-card-1">
        <div class="hero-card-icon">🏆</div>
        <div class="hero-card-val">95%</div>
        <div class="hero-card-lbl">Taxa de Aprovação</div>
      </div>
      <div class="hero-card-float hero-card-2">
        <div class="hero-card-icon">🎓</div>
        <div class="hero-card-val">1.500+</div>
        <div class="hero-card-lbl">Alunos Formados</div>
      </div>
    </div>
  </div>
</section>

<!-- METRICS STRIP -->
<div class="metrics">
  <div class="metrics-inner">
    <div class="metric" data-reveal>
      <div class="metric-icon"><i class="fas fa-users"></i></div>
      <div class="metric-num"><span class="counter" data-target="1500">1500</span><sup>+</sup></div>
      <div class="metric-lbl">Alunos Matriculados</div>
    </div>
    <div class="metric" data-reveal data-delay="1">
      <div class="metric-icon"><i class="fas fa-chalkboard-teacher"></i></div>
      <div class="metric-num"><span class="counter" data-target="80">80</span><sup>+</sup></div>
      <div class="metric-lbl">Professores Qualificados</div>
    </div>
    <div class="metric" data-reveal data-delay="2">
      <div class="metric-icon"><i class="fas fa-door-open"></i></div>
      <div class="metric-num"><span class="counter" data-target="45">45</span></div>
      <div class="metric-lbl">Turmas Ativas</div>
    </div>
    <div class="metric" data-reveal data-delay="3">
      <div class="metric-icon"><i class="fas fa-trophy"></i></div>
      <div class="metric-num"><span class="counter" data-target="95">95</span><sup>%</sup></div>
      <div class="metric-lbl">Taxa de Aprovação</div>
    </div>
  </div>
</div>

<!-- ABOUT -->
<section class="section" id="sobre">
  <div class="section-inner">
    <div class="about-grid">
      <div class="about-imgs" data-reveal>
        <img class="about-img-main" src="https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&q=80&auto=format&fit=crop" alt="Nossa Escola">
        <div class="about-img-badge">
          <div class="badge-num">15</div>
          <div class="badge-txt">Anos de Excelência</div>
        </div>
      </div>
      <div data-reveal data-delay="2">
        <div class="tag"><i class="fas fa-info-circle"></i> Sobre a EAM</div>
        <div class="section-head">
          <h2>Educação que transforma <span class="accent">vidas</span></h2>
          <p>Fundada em 2010, a Escola Angolana Modelo nasceu com o propósito de oferecer uma educação verdadeiramente transformadora, alinhada às necessidades do século XXI.</p>
        </div>
        <div class="about-features">
          <div class="feat-item">
            <div class="feat-icon"><i class="fas fa-medal"></i></div>
            <div>
              <div class="feat-label">Ensino de Qualidade</div>
              <div class="feat-desc">Metodologia moderna com corpo docente altamente qualificado</div>
            </div>
          </div>
          <div class="feat-item">
            <div class="feat-icon"><i class="fas fa-laptop"></i></div>
            <div>
              <div class="feat-label">Tecnologia</div>
              <div class="feat-desc">Laboratórios modernos e plataforma digital interativa</div>
            </div>
          </div>
          <div class="feat-item">
            <div class="feat-icon"><i class="fas fa-heart"></i></div>
            <div>
              <div class="feat-label">Formação Integral</div>
              <div class="feat-desc">Atividades extracurriculares e projetos sociais</div>
            </div>
          </div>
          <div class="feat-item">
            <div class="feat-icon"><i class="fas fa-globe"></i></div>
            <div>
              <div class="feat-label">Intercâmbio Cultural</div>
              <div class="feat-desc">Parcerias internacionais e programas de intercâmbio</div>
            </div>
          </div>
        </div>
        <a href="#" class="btn-fire" style="padding:0.85rem 2rem; border-radius:12px; font-size:1rem; display:inline-flex; align-items:center; gap:0.5rem;">
          Saiba Mais <i class="fas fa-arrow-right"></i>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- COURSES -->
<section class="section section-alt" id="cursos">
  <div class="section-inner">
    <div class="section-head centered" data-reveal>
      <div class="tag" style="margin:0 auto 1rem"><i class="fas fa-book-open"></i> Nossos Cursos</div>
      <h2>Encontre o curso <span class="accent">ideal</span> para você</h2>
      <p>Grade curricular completa com os melhores programas do ensino médio angolano</p>
    </div>
    <div class="courses-grid">
      <div class="course-card" data-reveal>
        <div class="course-thumb">
          <img src="https://images.unsplash.com/photo-1532012197267-da84d127e765?w=600&q=80&auto=format&fit=crop" alt="Ciências e Tecnologia">
          <span class="course-chip">Ciências</span>
        </div>
        <div class="course-body">
          <div class="course-name">Ciências e Tecnologia</div>
          <div class="course-desc">Formação sólida em Matemática, Física, Química e Biologia para acesso às melhores universidades.</div>
          <div class="course-meta">
            <span><i class="fas fa-clock"></i> 3 anos</span>
            <span><i class="fas fa-layer-group"></i> Ensino Médio</span>
            <span><i class="fas fa-users"></i> 35 alunos/turma</span>
          </div>
          <div class="course-foot">
            <div class="course-price">Grátis</div>
            <a href="#" class="btn-outline-fire">Ver Detalhes <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
      <div class="course-card" data-reveal data-delay="2">
        <div class="course-thumb">
          <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=600&q=80&auto=format&fit=crop" alt="Ciências Económicas">
          <span class="course-chip">Economia</span>
        </div>
        <div class="course-body">
          <div class="course-name">Ciências Económicas e Jurídicas</div>
          <div class="course-desc">Preparação em Economia, Direito, Gestão e Ciências Sociais para líderes do amanhã.</div>
          <div class="course-meta">
            <span><i class="fas fa-clock"></i> 3 anos</span>
            <span><i class="fas fa-layer-group"></i> Ensino Médio</span>
            <span><i class="fas fa-users"></i> 35 alunos/turma</span>
          </div>
          <div class="course-foot">
            <div class="course-price">Grátis</div>
            <a href="#" class="btn-outline-fire">Ver Detalhes <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
      <div class="course-card" data-reveal data-delay="3">
        <div class="course-thumb">
          <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&q=80&auto=format&fit=crop" alt="Humanidades">
          <span class="course-chip">Humanidades</span>
        </div>
        <div class="course-body">
          <div class="course-name">Humanidades e Ciências Sociais</div>
          <div class="course-desc">Aprofundamento em Literatura, História, Filosofia, Sociologia e Línguas para mentes analíticas.</div>
          <div class="course-meta">
            <span><i class="fas fa-clock"></i> 3 anos</span>
            <span><i class="fas fa-layer-group"></i> Ensino Médio</span>
            <span><i class="fas fa-users"></i> 35 alunos/turma</span>
          </div>
          <div class="course-foot">
            <div class="course-price">Grátis</div>
            <a href="#" class="btn-outline-fire">Ver Detalhes <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>
    <div style="text-align:center; margin-top:3rem" data-reveal>
      <a href="/cursos" class="btn-fire" style="padding:0.875rem 2.5rem; border-radius:12px; font-size:1rem; display:inline-flex; align-items:center; gap:0.5rem;">
        Ver Todos os Cursos <i class="fas fa-arrow-right"></i>
      </a>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="section">
  <div class="section-inner">
    <div class="section-head centered" data-reveal>
      <div class="tag" style="margin:0 auto 1rem"><i class="fas fa-star"></i> Por que EAM?</div>
      <h2>Vantagens de estudar <span class="accent">conosco</span></h2>
      <p>Descubra o que torna a Escola Angolana Modelo a escolha certa para o futuro do seu filho</p>
    </div>
    <div class="features-grid">
      <div class="feat-card" data-reveal>
        <div class="feat-card-icon"><i class="fas fa-medal"></i></div>
        <h4>Excelência Acadêmica</h4>
        <p>Mais de 90% de aprovação nos exames nacionais e nas principais universidades do país.</p>
      </div>
      <div class="feat-card" data-reveal data-delay="1">
        <div class="feat-card-icon"><i class="fas fa-laptop-code"></i></div>
        <h4>Tecnologia e Inovação</h4>
        <p>Laboratórios modernos, sala de informática e plataforma digital de aprendizagem interativa.</p>
      </div>
      <div class="feat-card" data-reveal data-delay="2">
        <div class="feat-card-icon"><i class="fas fa-hands-helping"></i></div>
        <h4>Formação Integral</h4>
        <p>Atividades extracurriculares, projetos sociais, clubes estudantis e programas de liderança.</p>
      </div>
      <div class="feat-card" data-reveal data-delay="3">
        <div class="feat-card-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <h4>Corpo Docente Qualificado</h4>
        <p>Professores especializados com experiência e dedicação ao desenvolvimento dos alunos.</p>
      </div>
      <div class="feat-card" data-reveal data-delay="4">
        <div class="feat-card-icon"><i class="fas fa-building"></i></div>
        <h4>Infraestrutura Moderna</h4>
        <p>Salas climatizadas, biblioteca, laboratórios, quadra poliesportiva e áreas de convivência.</p>
      </div>
      <div class="feat-card" data-reveal data-delay="5">
        <div class="feat-card-icon"><i class="fas fa-user-check"></i></div>
        <h4>Acompanhamento Personalizado</h4>
        <p>Monitoria, tutoria e acompanhamento psicológico e pedagógico para todos os alunos.</p>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<div class="testimonials-outer" id="depoimentos">
  <div class="testimonials-inner">
    <div class="section-head" data-reveal>
      <div class="tag"><i class="fas fa-quote-left"></i> Depoimentos</div>
      <h2>O que dizem <span class="accent">nossos alunos</span></h2>
      <p>Histórias reais de quem já faz parte da nossa comunidade</p>
    </div>
    <div class="testi-grid">
      <div class="testi-card" data-reveal>
        <div class="testi-quote">"</div>
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">"A EAM me preparou para o vestibular e para a vida. Os professores são excelentes e a infraestrutura é incrível! Consegui entrar em Medicina na UAN graças ao ensino de qualidade que recebi."</p>
        <div class="testi-author">
          <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Ana Silva">
          <div>
            <div class="testi-name">Ana Silva</div>
            <div class="testi-role">Ex-aluna · Medicina UAN</div>
          </div>
        </div>
      </div>
      <div class="testi-card" data-reveal data-delay="2">
        <div class="testi-quote">"</div>
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">"Estudar na EAM foi uma das melhores decisões da minha vida. Além do ensino de qualidade, participei de projetos sociais que mudaram minha visão de mundo. Hoje sou engenheiro."</p>
        <div class="testi-author">
          <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="João Santos">
          <div>
            <div class="testi-name">João Santos</div>
            <div class="testi-role">Ex-aluno · Engenharia Civil</div>
          </div>
        </div>
      </div>
      <div class="testi-card" data-reveal data-delay="4">
        <div class="testi-quote">"</div>
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">"A EAM me proporcionou uma base sólida para minha carreira. Os professores são dedicados e a estrutura moderna faz toda diferença no aprendizado. Recomendo a todos!"</p>
        <div class="testi-author">
          <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Maria Fernandes">
          <div>
            <div class="testi-name">Maria Fernandes</div>
            <div class="testi-role">Ex-aluna · Direito</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- CTA -->
<div class="cta-outer" id="contato">
  <div class="cta-inner" data-reveal>
    <h2>Pronto para começar sua jornada?</h2>
    <p>Faça sua inscrição e garanta sua vaga para o próximo ano letivo. Vagas limitadas — não perca esta oportunidade!</p>
    <div class="cta-btns">
      <a href="/inscricao" class="btn-cta-white"><i class="fas fa-pen" style="margin-right:8px"></i>Inscreva-se Agora</a>
      <a href="/contato" class="btn-cta-outline"><i class="fas fa-envelope" style="margin-right:8px"></i>Fale Conosco</a>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer>
  <div class="footer-inner">
    <div class="footer-grid">
      <div class="footer-brand">
        <a class="nav-logo" href="#">
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
          <li><a href="#">Início</a></li>
          <li><a href="#">Cursos</a></li>
          <li><a href="#">Sobre Nós</a></li>
          <li><a href="#">Contato</a></li>
          <li><a href="/inscricao">Inscrição</a></li>
          <li><a href="/auth/students">Portal do Aluno</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h5>Cursos</h5>
        <ul class="footer-links">
          <li><a href="#">Ciências e Tecnologia</a></li>
          <li><a href="#">Ciências Económicas</a></li>
          <li><a href="#">Humanidades</a></li>
          <li><a href="#">Ensino Especial</a></li>
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

<script>
// Navbar scroll
const nav = document.getElementById('nav');
window.addEventListener('scroll', () => {
  if(window.scrollY > 80) nav.classList.add('scrolled'); else nav.classList.remove('scrolled');
  if(window.scrollY > 400) document.getElementById('backTop').classList.add('show');
  else document.getElementById('backTop').classList.remove('show');
});

// Mobile menu
function toggleMenu() {
  document.getElementById('mobileNav').classList.toggle('open');
}

// Reveal on scroll
const reveals = document.querySelectorAll('[data-reveal]');
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('revealed'); });
}, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
reveals.forEach(el => observer.observe(el));

// Counter animation
function animateCounter(el) {
  const target = parseInt(el.dataset.target);
  const duration = 2000;
  const start = performance.now();
  const update = (now) => {
    const elapsed = now - start;
    const progress = Math.min(elapsed / duration, 1);
    const ease = 1 - Math.pow(1 - progress, 3);
    el.textContent = Math.floor(ease * target);
    if(progress < 1) requestAnimationFrame(update);
    else el.textContent = target;
  };
  requestAnimationFrame(update);
}

const counterObserver = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if(e.isIntersecting && !e.target.dataset.counted) {
      e.target.dataset.counted = true;
      animateCounter(e.target);
    }
  });
}, { threshold: 0.5 });
document.querySelectorAll('.counter[data-target]').forEach(el => counterObserver.observe(el));

// Smooth scroll nav links
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const href = a.getAttribute('href');
    if(href === '#') return;
    const target = document.querySelector(href);
    if(target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

// Active nav link
const sections = document.querySelectorAll('section[id], div[id]');
window.addEventListener('scroll', () => {
  let current = '';
  sections.forEach(s => { if(window.scrollY >= s.offsetTop - 100) current = s.id; });
  document.querySelectorAll('.nav-links a').forEach(a => {
    a.classList.remove('active');
    if(a.getAttribute('href') === '#' + current) a.classList.add('active');
  });
});
</script>
</body>
</html>