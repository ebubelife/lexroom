<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>First Mediator — Dispute Resolution, Without the Legal Bill</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/shared-layout.css') }}">
<script src="{{ asset('js/shared-layout.js') }}"></script>
<style>
:root {
  --navy: #0D1B2A;
  --gold: #C9A84C;
  --gold-light: #E8C96A;
  --gold-pale: #F5EDD6;
  --white: #ffffff;
  --off-white: #FAFAF8;
  --black: #0A0A0A;
  --gray-100: #F4F4F2;
  --gray-200: #E8E8E4;
  --gray-400: #ADADAA;
  --gray-600: #6B6B68;
  --gray-800: #2E2E2C;
  --bg: var(--white);
  --bg-alt: var(--off-white);
  --text-primary: var(--black);
  --text-secondary: var(--gray-600);
  --text-muted: var(--gray-400);
  --border: var(--gray-200);
  --card-bg: var(--white);
  --nav-bg: rgba(255,255,255,0.92);
  --serif: 'Instrument Serif', Georgia, serif;
  --sans: 'DM Sans', system-ui, sans-serif;
  --radius: 12px;
  --radius-lg: 20px;
  --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
  --shadow-lg: 0 4px 24px rgba(0,0,0,0.08), 0 1px 4px rgba(0,0,0,0.04);
  --logo-light-display: block !important;
  --logo-dark-display: none !important;
}

[data-theme="dark"] {
  --bg: var(--navy);
  --bg-alt: #0F2336;
  --text-primary: #F0EDE6;
  --text-secondary: #9BA8B4;
  --text-muted: #5A6A78;
  --border: #1E3248;
  --card-bg: #112030;
  --nav-bg: rgba(13,27,42,0.95);
  --shadow: 0 1px 3px rgba(0,0,0,0.3), 0 4px 16px rgba(0,0,0,0.2);
  --shadow-lg: 0 4px 24px rgba(0,0,0,0.4);
  --logo-light-display: none !important;
  --logo-dark-display: block !important;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html { scroll-behavior: smooth; }

html { scroll-behavior: smooth; }

/* ── SCROLL ANIMATIONS ── */
.reveal {
  opacity: 0;
  transform: translateY(28px);
  transition: opacity 0.65s cubic-bezier(0.16,1,0.3,1), transform 0.65s cubic-bezier(0.16,1,0.3,1);
}
.reveal.visible { opacity: 1; transform: none; }
.reveal-delay-1 { transition-delay: 0.1s; }
.reveal-delay-2 { transition-delay: 0.2s; }
.reveal-delay-3 { transition-delay: 0.3s; }
.reveal-delay-4 { transition-delay: 0.4s; }

/* Header/Footer styles moved to shared-layout.css */

.btn-primary-lg {
  font-size: 16px; padding: 14px 32px; border-radius: 10px;
  display: inline-flex; align-items: center; gap: 8px;
}

.theme-toggle {
  width: 36px; height: 36px;
  background: var(--gray-100); border: none;
  border-radius: 8px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: background 0.2s;
  font-size: 16px;
}
[data-theme="dark"] .theme-toggle { background: rgba(255,255,255,0.08); }
.theme-toggle:hover { background: var(--gray-200); }
[data-theme="dark"] .theme-toggle:hover { background: rgba(255,255,255,0.12); }



/* ── SECTIONS ── */
section { padding: 100px 24px; }
.container { max-width: 1160px; margin: 0 auto; }
.section-label {
  font-size: 12px; font-weight: 600;
  letter-spacing: 0.12em; text-transform: uppercase;
  color: var(--gold);
  margin-bottom: 16px;
}
h1, h2, h3 { font-family: var(--serif); letter-spacing: -0.02em; line-height: 1.15; }
h1 { font-size: clamp(42px, 6vw, 72px); font-weight: 400; }
h2 { font-size: clamp(32px, 4vw, 52px); font-weight: 400; }
h3 { font-size: 22px; font-weight: 400; }
p { color: var(--text-secondary); line-height: 1.7; }


/* ── HERO ── */
#hero {
  padding-top: 160px;
  padding-bottom: 100px;
  text-align: center;
  position: relative;
}
.hero-eyebrow {
  display: inline-flex; align-items: center; gap: 8px;
  background: var(--gold-pale);
  color: #8B6B1A;
  font-size: 13px; font-weight: 500;
  padding: 6px 14px; border-radius: 100px;
  margin-bottom: 32px;
  letter-spacing: 0.01em;
}
[data-theme="dark"] .hero-eyebrow {
  background: rgba(201,168,76,0.12);
  color: var(--gold-light);
}
.hero-eyebrow-dot {
  width: 6px; height: 6px;
  background: var(--gold);
  border-radius: 50%;
  animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot {
  0%,100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(0.7); }
}
.hero-title {
  max-width: 820px; margin: 0 auto 24px;
  color: var(--text-primary);
}
.hero-title em {
  font-style: italic;
  color: var(--gold);
}
.hero-sub {
  font-size: 18px; font-weight: 300;
  max-width: 560px; margin: 0 auto 48px;
  color: var(--text-secondary);
  line-height: 1.65;
}
.hero-cta-group {
  display: flex; align-items: center; justify-content: center; gap: 12px;
  flex-wrap: wrap;
}
.hero-note {
  font-size: 13px; color: var(--text-muted);
  margin-top: 18px;
}

/* hero mockup */
.hero-mockup {
  margin: 72px auto 0;
  max-width: 900px;
  position: relative;
}
.mockup-browser {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: var(--shadow-lg);
}
.mockup-bar {
  background: var(--gray-100);
  padding: 12px 16px;
  display: flex; align-items: center; gap: 8px;
  border-bottom: 1px solid var(--border);
}
[data-theme="dark"] .mockup-bar { background: rgba(255,255,255,0.04); }
.mockup-dots { display: flex; gap: 6px; }
.mockup-dot { width: 10px; height: 10px; border-radius: 50%; }
.mockup-dot:nth-child(1) { background: #FF5F57; }
.mockup-dot:nth-child(2) { background: #FFBD2E; }
.mockup-dot:nth-child(3) { background: #28C840; }
.mockup-url {
  flex: 1; background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 6px; padding: 4px 12px;
  font-size: 12px; color: var(--text-muted);
  font-family: var(--sans);
  max-width: 300px; margin: 0 auto;
  text-align: center;
}
.mockup-content {
  padding: 0;
  display: grid; grid-template-columns: 1fr 320px;
  min-height: 380px;
}
.mockup-chat {
  padding: 24px;
  border-right: 1px solid var(--border);
  display: flex; flex-direction: column; gap: 14px;
  overflow: hidden;
}
.chat-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 8px;
}
.chat-phase {
  font-size: 11px; font-weight: 600; letter-spacing: 0.08em;
  text-transform: uppercase; color: var(--gold);
}
.chat-timer {
  font-size: 13px; font-weight: 600;
  color: var(--text-primary);
  font-variant-numeric: tabular-nums;
  background: var(--gray-100);
  padding: 4px 10px; border-radius: 6px;
}
[data-theme="dark"] .chat-timer { background: rgba(255,255,255,0.06); }
.msg {
  display: flex; gap: 10px; align-items: flex-start;
  animation: msg-in 0.4s ease both;
}
@keyframes msg-in {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: none; }
}
.msg-avatar {
  width: 28px; height: 28px; border-radius: 50%;
  flex-shrink: 0; display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 600;
}
.msg-a .msg-avatar { background: #E8F0FE; color: #2563EB; }
.msg-b .msg-avatar { background: #F3E8FF; color: #7C3AED; }
.msg-bubble {
  padding: 10px 14px; border-radius: 12px;
  font-size: 13px; line-height: 1.5;
  max-width: 75%;
  color: var(--text-primary);
}
.msg-a .msg-bubble { background: #EFF6FF; border-bottom-left-radius: 4px; }
.msg-b { flex-direction: row-reverse; }
.msg-b .msg-bubble { background: #F5F3FF; border-bottom-right-radius: 4px; }
[data-theme="dark"] .msg-a .msg-bubble { background: rgba(37,99,235,0.12); }
[data-theme="dark"] .msg-b .msg-bubble { background: rgba(124,58,237,0.12); }
.msg-lex {
  background: linear-gradient(135deg, rgba(201,168,76,0.08), rgba(201,168,76,0.04));
  border: 1px solid rgba(201,168,76,0.2);
  border-radius: 10px; padding: 12px 14px;
  font-size: 13px; line-height: 1.55; color: var(--text-primary);
}
.msg-lex-label {
  font-size: 10px; font-weight: 700; letter-spacing: 0.1em;
  text-transform: uppercase; color: var(--gold);
  margin-bottom: 6px; display: block;
}
.mockup-sidebar {
  padding: 20px;
  display: flex; flex-direction: column; gap: 16px;
}
.sidebar-card {
  background: var(--bg-alt);
  border: 1px solid var(--border);
  border-radius: 10px; padding: 14px;
}
.sidebar-card-title {
  font-size: 11px; font-weight: 600; letter-spacing: 0.08em;
  text-transform: uppercase; color: var(--text-muted);
  margin-bottom: 10px;
}
.phase-steps { display: flex; flex-direction: column; gap: 8px; }
.phase-step {
  display: flex; align-items: center; gap: 10px;
  font-size: 12px; color: var(--text-secondary);
}
.phase-step.active { color: var(--text-primary); font-weight: 500; }
.phase-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: var(--border); flex-shrink: 0;
}
.phase-step.active .phase-dot { background: var(--gold); }
.phase-step.done .phase-dot { background: #22C55E; }
.evidence-list { display: flex; flex-direction: column; gap: 8px; }
.evidence-item {
  display: flex; align-items: center; gap: 8px;
  font-size: 12px; color: var(--text-secondary);
}
.evidence-icon {
  width: 28px; height: 28px; border-radius: 6px;
  background: var(--gray-100); display: flex; align-items: center; justify-content: center;
  font-size: 14px; flex-shrink: 0;
}
[data-theme="dark"] .evidence-icon { background: rgba(255,255,255,0.06); }

/* ── GAP SECTION ── */
#gap {
  padding: 80px 24px;
  text-align: center;
}
.gap-statement {
  font-family: var(--serif);
  font-size: clamp(22px, 3.5vw, 38px);
  font-weight: 400;
  line-height: 1.35;
  max-width: 700px; margin: 0 auto;
  color: var(--text-primary);
}
.gap-statement em { font-style: italic; color: var(--gold); }
.gap-divider {
  width: 48px; height: 2px;
  background: var(--gold);
  margin: 40px auto;
  border-radius: 2px;
}

/* ── HOW IT WORKS ── */
#how { padding: 100px 24px; }
.steps-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2px;
  margin-top: 64px;
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  overflow: hidden;
}
.step-card {
  padding: 40px 32px;
  background: var(--card-bg);
  border-right: 1px solid var(--border);
  transition: background 0.25s;
  position: relative;
}
.step-card:last-child { border-right: none; }
.step-card:hover { background: var(--bg-alt); }
.step-num {
  font-family: var(--serif);
  font-size: 56px; font-weight: 400;
  color: var(--gold);
  opacity: 0.3;
  line-height: 1;
  margin-bottom: 24px;
  display: block;
}
.step-title {
  font-family: var(--serif);
  font-size: 22px; font-weight: 400;
  color: var(--text-primary);
  margin-bottom: 12px;
}
.step-desc {
  font-size: 15px; color: var(--text-secondary);
  line-height: 1.65;
}

/* ── DISPUTE CATEGORIES ── */
#categories { padding: 100px 24px; background: var(--bg-alt); }
[data-theme="dark"] #categories { background: rgba(255,255,255,0.02); }
.categories-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-top: 56px;
}
.cat-card {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 28px;
  cursor: pointer;
  transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
  text-decoration: none;
  display: block;
}
.cat-card:hover {
  border-color: var(--gold);
  box-shadow: 0 0 0 3px rgba(201,168,76,0.08);
  transform: translateY(-2px);
}
.cat-icon {
  font-size: 28px; margin-bottom: 16px; display: block;
  line-height: 1;
}
.cat-title {
  font-family: var(--serif);
  font-size: 18px; font-weight: 400;
  color: var(--text-primary);
  margin-bottom: 8px;
}
.cat-desc {
  font-size: 14px; color: var(--text-secondary);
  line-height: 1.6;
}

/* ── TRUST SIGNALS ── */
#trust { padding: 80px 24px; }
.trust-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1px;
  background: var(--border);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  overflow: hidden;
  margin-top: 56px;
}
.trust-item {
  background: var(--card-bg);
  padding: 36px 28px;
  transition: background 0.2s;
}
.trust-item:hover { background: var(--bg-alt); }
.trust-stat {
  font-family: var(--serif);
  font-size: 40px; font-weight: 400;
  color: var(--text-primary);
  line-height: 1;
  margin-bottom: 8px;
}
.trust-stat span { color: var(--gold); }
.trust-label {
  font-size: 14px; color: var(--text-secondary);
  line-height: 1.5;
}

/* ── PRICING ── */
#pricing { padding: 100px 24px; }
.pricing-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-top: 56px;
  max-width: 900px; margin-left: auto; margin-right: auto;
  margin-top: 56px;
}
.pricing-card {
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 36px 32px;
  background: var(--card-bg);
  position: relative;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.pricing-card.featured {
  border-color: var(--gold);
  box-shadow: 0 0 0 1px var(--gold), var(--shadow-lg);
}
.pricing-badge {
  position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
  background: var(--gold); color: var(--navy);
  font-size: 11px; font-weight: 700; letter-spacing: 0.08em;
  text-transform: uppercase;
  padding: 4px 14px; border-radius: 100px;
  white-space: nowrap;
}
.pricing-name {
  font-size: 13px; font-weight: 600; letter-spacing: 0.08em;
  text-transform: uppercase; color: var(--text-muted);
  margin-bottom: 12px;
}
.pricing-amount {
  font-family: var(--serif);
  font-size: 42px; font-weight: 400;
  color: var(--text-primary);
  line-height: 1;
  margin-bottom: 4px;
}
.pricing-amount sup {
  font-size: 20px; vertical-align: super; color: var(--gold);
}
.pricing-period {
  font-size: 14px; color: var(--text-muted);
  margin-bottom: 24px;
}
.pricing-divider {
  height: 1px; background: var(--border);
  margin: 24px 0;
}
.pricing-features { list-style: none; display: flex; flex-direction: column; gap: 12px; }
.pricing-features li {
  display: flex; align-items: flex-start; gap: 10px;
  font-size: 14px; color: var(--text-secondary);
}
.pricing-features li::before {
  content: '✓'; color: var(--gold);
  font-weight: 700; flex-shrink: 0;
  margin-top: 1px;
}
.pricing-cta {
  margin-top: 28px;
  display: block; width: 100%;
  text-align: center;
  font-family: var(--sans);
  font-size: 15px; font-weight: 600;
  padding: 13px; border-radius: 10px;
  cursor: pointer; text-decoration: none;
  transition: all 0.2s;
}
.pricing-cta-outline {
  border: 1.5px solid var(--border);
  color: var(--text-primary); background: none;
}
.pricing-cta-outline:hover {
  border-color: var(--gold); color: var(--gold);
}
.pricing-cta-filled {
  background: var(--gold); color: var(--navy); border: none;
}
.pricing-cta-filled:hover {
  background: var(--gold-light);
  box-shadow: 0 4px 16px rgba(201,168,76,0.35);
}
.split-note {
  text-align: center; margin-top: 20px;
  font-size: 14px; color: var(--text-muted);
}
.split-note strong { color: var(--text-secondary); }

/* ── FAQ ── */
#faq { padding: 100px 24px; background: var(--bg-alt); }
[data-theme="dark"] #faq { background: rgba(255,255,255,0.02); }
.faq-grid {
  max-width: 720px; margin: 56px auto 0;
  display: flex; flex-direction: column; gap: 0;
  border: 1px solid var(--border); border-radius: var(--radius-lg);
  overflow: hidden;
}
.faq-item {
  border-bottom: 1px solid var(--border);
  background: var(--card-bg);
}
.faq-item:last-child { border-bottom: none; }
.faq-question {
  width: 100%; padding: 22px 28px;
  display: flex; align-items: center; justify-content: space-between;
  background: none; border: none; cursor: pointer;
  text-align: left; gap: 16px;
  transition: background 0.2s;
}
.faq-question:hover { background: var(--bg-alt); }
.faq-question-text {
  font-family: var(--serif);
  font-size: 18px; font-weight: 400;
  color: var(--text-primary);
  line-height: 1.3;
}
.faq-icon {
  width: 28px; height: 28px; border-radius: 50%;
  background: var(--gray-100); flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; color: var(--text-secondary);
  transition: background 0.2s, transform 0.3s;
  font-weight: 300;
}
[data-theme="dark"] .faq-icon { background: rgba(255,255,255,0.06); }
.faq-item.open .faq-icon {
  background: var(--gold-pale); color: var(--gold);
  transform: rotate(45deg);
}
[data-theme="dark"] .faq-item.open .faq-icon { background: rgba(201,168,76,0.15); }
.faq-answer {
  max-height: 0; overflow: hidden;
  transition: max-height 0.4s cubic-bezier(0.16,1,0.3,1), padding 0.3s;
  padding: 0 28px;
}
.faq-item.open .faq-answer {
  max-height: 300px;
  padding: 0 28px 22px;
}
.faq-answer p {
  font-size: 15px; color: var(--text-secondary);
  line-height: 1.7;
}

/* ── FINAL CTA ── */
#cta {
  padding: 120px 24px;
  text-align: center;
}
.cta-box {
  max-width: 680px; margin: 0 auto;
}
.cta-title {
  color: var(--text-primary);
  margin-bottom: 20px;
}
.cta-sub {
  font-size: 18px; color: var(--text-secondary);
  margin-bottom: 48px;
  font-weight: 300;
}
.cta-divider {
  width: 48px; height: 2px;
  background: var(--gold);
  margin: 0 auto 48px;
  border-radius: 2px;
}

/* ── FOOTER ── */
footer {
  border-top: 1px solid var(--border);
  padding: 80px 24px 48px;
  background: var(--bg);
}
.footer-inner {
  max-width: 1160px; margin: 0 auto;
}
.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 64px;
  margin-bottom: 64px;
}
.footer-col h4 {
  font-family: var(--sans);
  font-size: 14px; font-weight: 600;
  letter-spacing: 0.05em; text-transform: uppercase;
  color: var(--text-primary);
  margin-bottom: 24px;
}
.footer-links {
  display: flex; flex-direction: column; gap: 12px; list-style: none;
}
.footer-links a {
  font-size: 14px; color: var(--text-secondary);
  text-decoration: none;
  transition: color 0.2s;
}
.footer-links a:hover { color: var(--gold); }
.footer-bottom {
  border-top: 1px solid var(--border);
  padding-top: 32px;
  text-align: center;
}
.footer-copy {
  font-size: 13px; color: var(--text-muted);
}

/* ── RESPONSIVE ── */
@media (max-width: 900px) {
  .steps-grid, .categories-grid, .trust-grid, .pricing-grid {
    grid-template-columns: 1fr;
  }
  .step-card { border-right: none; border-bottom: 1px solid var(--border); }
  .step-card:last-child { border-bottom: none; }
  .trust-grid { background: none; border: 1px solid var(--border); }
  .trust-item { border-bottom: 1px solid var(--border); }
  .trust-item:last-child { border-bottom: none; }
  .nav-links { display: none; }
  .nav-right .btn-ghost, .nav-right .btn-primary { display: none; }
  .mobile-menu-btn { display: block; }
  
  .mockup-content { grid-template-columns: 1fr; }
  .mockup-sidebar { display: none; }
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 48px;
    text-align: center;
  }
  .footer-inner {
    text-align: center;
  }
  .footer-col .logo {
    justify-content: center;
  }
  .footer-links {
    align-items: center;
  }
}
</style>
</head>
<body>

@include('partials.navbar')
      <li><a href="#pricing">Pricing</a></li>
      <li><a href="#faq">FAQ</a></li>
    </ul>
    <div class="nav-right">
      <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
        <span id="theme-icon">🌙</span>
      </button>
      <a href="{{ route('login') }}" class="btn-ghost">Log in</a>
      <a href="{{ route('register') }}" class="btn-primary">Create a Room</a>
      
      <button class="mobile-menu-btn" onclick="toggleMobileMenu()" aria-label="Toggle menu">
        <svg id="menu-icon-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
        <svg id="menu-icon-close" class="hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
  </div>
  
  <!-- Mobile Nav Dropdown -->
  <div id="mobile-nav" class="mobile-nav">
    <a href="{{ route('login') }}" class="btn-ghost" onclick="toggleMobileMenu()">Log in</a>
    <a href="{{ route('register') }}" class="btn-primary" onclick="toggleMobileMenu()">Create a Room</a>
    
    <div style="height: 1px; background: var(--border); margin: 8px 0;"></div>
    
    <ul style="list-style: none; display: flex; flex-direction: column; gap: 16px; padding: 8px 0;">
      <li><a href="#how" style="text-decoration: none; color: var(--text-secondary); font-size: 15px;" onclick="toggleMobileMenu()">How it works</a></li>
      <li><a href="#categories" style="text-decoration: none; color: var(--text-secondary); font-size: 15px;" onclick="toggleMobileMenu()">Disputes</a></li>
      <li><a href="#pricing" style="text-decoration: none; color: var(--text-secondary); font-size: 15px;" onclick="toggleMobileMenu()">Pricing</a></li>
      <li><a href="#faq" style="text-decoration: none; color: var(--text-secondary); font-size: 15px;" onclick="toggleMobileMenu()">FAQ</a></li>
    </ul>
  </div>
</nav>

<!-- HERO -->
<section id="hero">
  <div class="container">
    <div class="reveal">
      <div class="hero-eyebrow">
        <span class="hero-eyebrow-dot"></span>
        AI-assisted mediation · No lawyer required
      </div>
    </div>
    <h1 class="hero-title reveal reveal-delay-1">
      Resolve your dispute<br><em>without hiring a lawyer.</em>
    </h1>
    <p class="hero-sub reveal reveal-delay-2">
      Two parties. One room. An impartial AI mediator. A formal resolution report — in under 90 minutes.
    </p>
    <div class="hero-cta-group reveal reveal-delay-3">
      <a href="{{ route('register') }}" class="btn-primary btn-primary-lg">
        Create a Room
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>
      <a href="#how" class="btn-ghost" style="font-size:16px; padding: 14px 24px;">See how it works</a>
    </div>
    <p class="hero-note reveal reveal-delay-4">Setup in 15 minutes · Starts from $45 · PDF report included</p>

    <!-- Browser Mockup -->
    <div class="hero-mockup reveal reveal-delay-4">
      <div class="mockup-browser">
        <div class="mockup-bar">
          <div class="mockup-dots">
            <div class="mockup-dot"></div>
            <div class="mockup-dot"></div>
            <div class="mockup-dot"></div>
          </div>
          <div class="mockup-url">firstmediator.com/room/inv-2024-0847</div>
        </div>
        <div class="mockup-content">
          <div class="mockup-chat">
            <div class="chat-header">
              <span class="chat-phase">Cross-examination</span>
              <span class="chat-timer">42:18</span>
            </div>
            <div class="msg msg-a" style="animation-delay:0.1s">
              <div class="msg-avatar">A</div>
              <div class="msg-bubble">The invoice was sent on March 3rd. I have email proof. Payment was due within 14 days.</div>
            </div>
            <div class="msg msg-b" style="animation-delay:0.3s">
              <div class="msg-avatar">B</div>
              <div class="msg-bubble">The deliverables were incomplete. The final logo variants were never submitted.</div>
            </div>
            <div class="msg-lex" style="animation-delay:0.5s">
              <span class="msg-lex-label">⚖ First Mediator</span>
              Party A — your invoice (Exhibit 1) references 3 logo variants. Party B — can you confirm which specific variants were not delivered? This will help establish the scope of completion.
            </div>
            <div class="msg msg-a" style="animation-delay:0.7s">
              <div class="msg-avatar">A</div>
              <div class="msg-bubble">All 3 variants were delivered. See the Dropbox link in my last email, attached as Exhibit 3.</div>
            </div>
          </div>
          <div class="mockup-sidebar">
            <div class="sidebar-card">
              <div class="sidebar-card-title">Session phases</div>
              <div class="phase-steps">
                <div class="phase-step done"><div class="phase-dot"></div>Opening statements</div>
                <div class="phase-step done"><div class="phase-dot"></div>Evidence submission</div>
                <div class="phase-step active"><div class="phase-dot"></div>Cross-examination</div>
                <div class="phase-step"><div class="phase-dot"></div>Analysis & findings</div>
                <div class="phase-step"><div class="phase-dot"></div>Resolution proposal</div>
              </div>
            </div>
            <div class="sidebar-card">
              <div class="sidebar-card-title">Evidence vault</div>
              <div class="evidence-list">
                <div class="evidence-item">
                  <div class="evidence-icon">📄</div>
                  <div>Invoice_March.pdf<br><span style="font-size:11px;color:var(--text-muted)">Party A · 84KB</span></div>
                </div>
                <div class="evidence-item">
                  <div class="evidence-icon">📧</div>
                  <div>Email_thread.pdf<br><span style="font-size:11px;color:var(--text-muted)">Party A · 212KB</span></div>
                </div>
                <div class="evidence-item">
                  <div class="evidence-icon">🖼️</div>
                  <div>Logo_variants.png<br><span style="font-size:11px;color:var(--text-muted)">Party A · 1.2MB</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- GAP -->
<section id="gap">
  <div class="container">
    <div class="gap-divider reveal"></div>
    <p class="gap-statement reveal">
      Between arguing on WhatsApp and <em>hiring a solicitor,</em> there has never been anything in between. Until now.
    </p>
    <div class="gap-divider reveal" style="margin-top:40px;margin-bottom:0"></div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section id="how">
  <div class="container">
    <div class="reveal">
      <div class="section-label">How it works</div>
      <h2>Three steps to resolution.</h2>
    </div>
    <div class="steps-grid">
      <div class="step-card reveal reveal-delay-1">
        <span class="step-num">01</span>
        <div class="step-title">Create a Room</div>
        <p class="step-desc">Select your dispute type, jurisdiction, and session duration. Write a brief case summary. Send the room link to the other party. Setup takes under 15 minutes.</p>
      </div>
      <div class="step-card reveal reveal-delay-2">
        <span class="step-num">02</span>
        <div class="step-title">Present your case</div>
        <p class="step-desc">Both parties join the room and present their position. Upload contracts, invoices, screenshots. First Mediator reads everything, asks targeted questions, and keeps the session fair.</p>
      </div>
      <div class="step-card reveal reveal-delay-3">
        <span class="step-num">03</span>
        <div class="step-title">Receive your report</div>
        <p class="step-desc">First Mediator delivers a formal, timestamped PDF report — findings, resolution recommendation, and confidence score. Usable as documentation if the matter proceeds to court.</p>
      </div>
    </div>
  </div>
</section>

<!-- DISPUTE CATEGORIES -->
<section id="categories">
  <div class="container">
    <div class="reveal">
      <div class="section-label">Dispute types</div>
      <h2>Built for everyday conflicts.</h2>
      <p style="margin-top:12px;max-width:520px;font-size:16px;">The disputes that are too serious to ignore, but too costly to litigate.</p>
    </div>
    <div class="categories-grid">
      <a href="#" class="cat-card reveal reveal-delay-1">
        <span class="cat-icon">🏠</span>
        <div class="cat-title">Tenancy</div>
        <p class="cat-desc">Deposit disputes, wrongful deductions, repairs withheld, early eviction claims.</p>
      </a>
      <a href="#" class="cat-card reveal reveal-delay-1">
        <span class="cat-icon">💼</span>
        <div class="cat-title">Freelance</div>
        <p class="cat-desc">Unpaid invoices, scope creep, contract breaches between clients and service providers.</p>
      </a>
      <a href="#" class="cat-card reveal reveal-delay-2">
        <span class="cat-icon">🤝</span>
        <div class="cat-title">Business</div>
        <p class="cat-desc">Partner disputes, profit sharing disagreements, co-founder exits, breach of agreement.</p>
      </a>
      <a href="#" class="cat-card reveal reveal-delay-2">
        <span class="cat-icon">🛒</span>
        <div class="cat-title">E-commerce</div>
        <p class="cat-desc">Undelivered goods, refund disputes, misrepresented products, seller–buyer conflicts.</p>
      </a>
      <a href="#" class="cat-card reveal reveal-delay-3">
        <span class="cat-icon">👔</span>
        <div class="cat-title">Employment</div>
        <p class="cat-desc">Withheld salaries, wrongful termination, unpaid benefits, contract interpretation.</p>
      </a>
      <a href="#" class="cat-card reveal reveal-delay-3">
        <span class="cat-icon">💰</span>
        <div class="cat-title">Debt & Loans</div>
        <p class="cat-desc">Informal loan disputes, repayment disagreements, friend-to-friend debt recovery.</p>
      </a>
    </div>
  </div>
</section>

<!-- TRUST SIGNALS -->
<section id="trust">
  <div class="container">
    <div class="reveal" style="text-align:center">
      <div class="section-label">Why FirstMediator</div>
      <h2>Designed for the way disputes actually happen.</h2>
    </div>
    <div class="trust-grid">
      <div class="trust-item reveal reveal-delay-1">
        <div class="trust-stat"><span>15</span>min</div>
        <div class="trust-label">Average time to set up and start a session</div>
      </div>
      <div class="trust-item reveal reveal-delay-2">
        <div class="trust-stat">$<span>45</span></div>
        <div class="trust-label">Starting price — a fraction of any legal consultation</div>
      </div>
      <div class="trust-item reveal reveal-delay-3">
        <div class="trust-stat"><span>100%</span></div>
        <div class="trust-label">AI-impartial — First Mediator has no side, no bias, no agenda</div>
      </div>
      <div class="trust-item reveal reveal-delay-4">
        <div class="trust-stat"><span>PDF</span></div>
        <div class="trust-label">Formal report, timestamped and usable as court documentation</div>
      </div>
    </div>
  </div>
</section>

<!-- PRICING -->
<section id="pricing">
  <div class="container" style="text-align:center">
    <div class="reveal">
      <div class="section-label">Pricing</div>
      <h2>Pay per session. Nothing more.</h2>
      <p style="margin-top:12px;font-size:16px;max-width:480px;margin-left:auto;margin-right:auto;">No subscriptions. No hidden fees. Split the cost with the other party if you prefer.</p>
    </div>
    <div class="pricing-grid">
      <div class="pricing-card reveal reveal-delay-1">
        <div class="pricing-name">Starter</div>
        <div class="pricing-amount"><sup>$</sup>45</div>
        <div class="pricing-period">30 minute session</div>
        <div class="pricing-divider"></div>
        <ul class="pricing-features">
          <li>AI-moderated session</li>
          <li>Evidence vault (10 files)</li>
          <li>Mediation report PDF</li>
          <li>Split payment option</li>
        </ul>
        <a href="{{ route('register') }}" class="pricing-cta pricing-cta-outline">Create a Room</a>
      </div>
      <div class="pricing-card featured reveal reveal-delay-2">
        <div class="pricing-badge">Most popular</div>
        <div class="pricing-name">Standard</div>
        <div class="pricing-amount"><sup>$</sup>75</div>
        <div class="pricing-period">60 minute session</div>
        <div class="pricing-divider"></div>
        <ul class="pricing-features">
          <li>AI-moderated session</li>
          <li>Evidence vault (20 files)</li>
          <li>Mediation report PDF</li>
          <li>Split payment option</li>
          <li>Lawyer escalation access</li>
        </ul>
        <a href="{{ route('register') }}" class="pricing-cta pricing-cta-filled">Create a Room</a>
      </div>
      <div class="pricing-card reveal reveal-delay-3">
        <div class="pricing-name">Extended</div>
        <div class="pricing-amount"><sup>$</sup>100</div>
        <div class="pricing-period">90 minute session</div>
        <div class="pricing-divider"></div>
        <ul class="pricing-features">
          <li>AI-moderated session</li>
          <li>Evidence vault (20 files)</li>
          <li>Mediation report PDF</li>
          <li>Split payment option</li>
          <li>Lawyer escalation access</li>
          <li>Priority First Mediator processing</li>
        </ul>
        <a href="{{ route('register') }}" class="pricing-cta pricing-cta-outline">Create a Room</a>
      </div>
    </div>
    <p class="split-note reveal">
      <strong>Split payment available.</strong> Party A pays half. Party B pays their half. Room opens when both confirm.
    </p>
  </div>
</section>

<!-- FAQ -->
<section id="faq">
  <div class="container" style="text-align:center">
    <div class="reveal">
      <div class="section-label">FAQ</div>
      <h2>Questions people ask.</h2>
    </div>
    <div class="faq-grid reveal">
      <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
          <span class="faq-question-text">Is the First Mediator report legally binding?</span>
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>No. The Mediation Report is not a court order and does not constitute legal advice. However, it is a timestamped, formal document that can be used as supporting evidence if your dispute proceeds to court. FirstMediator is a technology tool, not a law firm.</p>
        </div>
      </div>
      <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
          <span class="faq-question-text">What if the other party refuses to join?</span>
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>We send the other party a room invite link via email. If they decline to join, the session does not proceed and you are not charged. Their refusal to engage is itself documented — which can be relevant if the matter escalates.</p>
        </div>
      </div>
      <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
          <span class="faq-question-text">How does First Mediator stay impartial?</span>
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>First Mediator has no relationship with either party and no financial stake in the outcome. It analyses both positions, the uploaded evidence, and the applicable legal framework for your jurisdiction. Every finding comes with a confidence score so you can see how certain the analysis is.</p>
        </div>
      </div>
      <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
          <span class="faq-question-text">Is my case data private and secure?</span>
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Yes. All session data is encrypted at rest (AES-256) and in transit (TLS 1.3). Evidence files are stored on secure cloud infrastructure. FirstMediator complies with global data protection standards (GDPR, CCPA). Session transcripts are retained for 12 months then deleted.</p>
        </div>
      </div>
      <div class="faq-item">
        <button class="faq-question" onclick="toggleFaq(this)">
          <span class="faq-question-text">Can I escalate to a real lawyer after?</span>
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-answer">
          <p>Yes — this is FMRefer. At the end of any session you can escalate to a verified lawyer from our directory, filtered by jurisdiction and speciality. Your full case file (transcript, evidence, First Mediator report) is packaged and sent to the lawyer automatically. They respond within 48–72 hours.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FINAL CTA -->
<section id="cta">
  <div class="container">
    <div class="cta-box">
      <div class="section-label reveal">Get started</div>
      <h2 class="cta-title reveal reveal-delay-1">Your dispute deserves<br><em style="color:var(--gold);font-style:italic">a proper resolution.</em></h2>
      <div class="cta-divider reveal reveal-delay-2"></div>
      <p class="cta-sub reveal reveal-delay-2">Stop going back and forth. Get a room, present your case, and walk away with a formal report — today.</p>
      <div class="reveal reveal-delay-3">
        <a href="{{ route('register') }}" class="btn-primary btn-primary-lg">
          Create a Room
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
      </div>
      <p class="hero-note reveal reveal-delay-4" style="margin-top:20px">No subscription · Starts from $45 · Setup in 15 minutes</p>
    </div>
  </div>
</section>

<!-- FOOTER -->
@include('partials.footer')

<script>
// Scroll reveal
const revealEls = document.querySelectorAll('.reveal');
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('visible');
      observer.unobserve(e.target);
    }
  });
}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
revealEls.forEach(el => observer.observe(el));

// FAQ
function toggleFaq(btn) {
  const item = btn.closest('.faq-item');
  const isOpen = item.classList.contains('open');
  document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
  if (!isOpen) item.classList.add('open');
}

// Animate chat timer
let secs = 42 * 60 + 18;
setInterval(() => {
  secs--;
  if (secs < 0) secs = 42 * 60 + 18;
  const m = Math.floor(secs / 60).toString().padStart(2,'0');
  const s = (secs % 60).toString().padStart(2,'0');
  const el = document.querySelector('.chat-timer');
  if (el) el.textContent = `${m}:${s}`;
}, 1000);
</script>
</body>
</html>