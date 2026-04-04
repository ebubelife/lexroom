<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us — First Mediator</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
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
  --serif: 'Instrument Serif', Georgia, serif;
  --sans: 'DM Sans', system-ui, sans-serif;
  --logo-light-display: block;
  --logo-dark-display: none;
}

[data-theme="dark"] {
  --bg: var(--navy);
  --bg-alt: #0F2336;
  --text-primary: #F0EDE6;
  --text-secondary: #9BA8B4;
  --text-muted: #5A6A78;
  --border: #1E3248;
  --logo-light-display: none;
  --logo-dark-display: block;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: var(--sans);
  background: var(--bg);
  color: var(--text-primary);
  line-height: 1.6;
  transition: background 0.3s ease, color 0.3s ease;
}

/* ── NAV ── */
nav {
  position: sticky; top: 0; left: 0; right: 0; z-index: 100;
  background: var(--bg);
  border-bottom: 1px solid var(--border);
  transition: background 0.3s, border-color 0.3s;
}
.nav-inner {
  max-width: 900px; margin: 0 auto;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 24px; height: 80px;
}
.logo { text-decoration: none; display: flex; align-items: center; }
.logo img { height: 60px; }

.theme-toggle {
  width: 36px; height: 36px;
  background: var(--gray-100); border: none;
  border-radius: 8px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: background 0.2s;
  font-size: 16px;
}
[data-theme="dark"] .theme-toggle { background: rgba(255,255,255,0.08); }

/* ── CONTENT ── */
.page-container {
  max-width: 800px; margin: 60px auto 120px;
  padding: 0 24px;
}
h1 {
  font-family: var(--serif);
  font-size: clamp(40px, 8vw, 64px);
  font-weight: 400;
  margin-bottom: 40px;
  line-height: 1.1;
}
h2 {
  font-family: var(--serif);
  font-size: 28px;
  font-weight: 400;
  margin: 48px 0 20px;
  color: var(--gold);
}
p, li {
  font-size: 17px;
  color: var(--text-secondary);
  margin-bottom: 16px;
}
ul {
  margin-bottom: 24px;
  padding-left: 20px;
}
li::marker { color: var(--gold); }

.mission-box {
  background: var(--bg-alt);
  border-left: 4px solid var(--gold);
  padding: 40px;
  border-radius: 0 16px 16px 0;
  margin: 60px 0;
}
.mission-box h2 { margin-top: 0; }

.highlight-section {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 32px;
  margin: 60px 0;
}
.highlight-card {
  padding: 32px;
  background: var(--bg-alt);
  border: 1px solid var(--border);
  border-radius: 16px;
}
.highlight-card h3 {
  font-family: var(--serif);
  font-size: 24px;
  color: var(--gold);
  margin-bottom: 16px;
}

@media (max-width: 768px) {
  .highlight-section { grid-template-columns: 1fr; }
}

/* ── FOOTER ── */
footer {
  border-top: 1px solid var(--border);
  padding: 80px 24px 40px;
  background: var(--bg);
}
.footer-inner {
  max-width: 900px; margin: 0 auto;
}
.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 60px;
  margin-bottom: 60px;
}
.footer-col h4 {
  font-family: var(--serif);
  font-size: 20px;
  color: var(--gold);
  margin-bottom: 24px;
}
.footer-links {
  display: flex;
  flex-direction: column;
  gap: 16px;
  list-style: none;
}
.footer-links a {
  font-size: 14px; color: var(--text-muted);
  text-decoration: none;
  transition: color 0.2s;
}
.footer-links a:hover { color: var(--gold); }

.footer-bottom {
  border-top: 1px solid var(--border);
  padding-top: 40px;
  text-align: center;
}
.footer-copy { font-size: 13px; color: var(--text-muted); }

@media (max-width: 768px) {
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 40px;
    text-align: center;
  }
  .footer-links { align-items: center; }
  h1 { font-size: 42px; }
}
</style>
</head>
<body>

<nav>
  <div class="nav-inner">
    <a href="/" class="logo">
      <img src="{{ asset('assets/images/logos/fm-lightmode.png') }}" alt="First Mediator" style="display: var(--logo-light-display) !important;">
      <img src="{{ asset('assets/images/logos/fm-darkmode.png') }}" alt="First Mediator" style="display: var(--logo-dark-display) !important;">
    </a>
    <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
      <span id="theme-icon">🌙</span>
    </button>
  </div>
</nav>

<main class="page-container">
  <h1>About Us</h1>
  
  <div class="intro-banner">
    First Mediator LTD is a UK-based innovative mediation platform that leverages artificial intelligence to assist individuals and organizations in resolving disputes efficiently, fairly, and transparently. Our AI acts as a neutral third-party facilitator, helping both sides communicate effectively, identify solutions, and reach amicable outcomes.
  </div>

  <h2>Our Mission</h2>
  <p>To make dispute resolution faster, more accessible, and impartial, empowering people to find common ground without the stress, cost, or delay of traditional legal proceedings.</p>

  <h2>Our Vision</h2>
  <p>To redefine mediation by integrating cutting-edge AI technology with human understanding, creating a trusted platform where disputes can be resolved respectfully and constructively.</p>

  <h2>What We Offer</h2>
  <ul>
    <li><strong>AI-Assisted Mediation:</strong> Neutral AI support to guide discussions and propose fair and amicable resolutions.</li>
    <li><strong>Secure & Confidential Platform:</strong> User data is protected under GDPR and UK data privacy laws.</li>
    <li><strong>Accessible & Affordable:</strong> High-quality mediation services at a fraction of the cost of traditional legal services.</li>
    <li><strong>Fast Resolution:</strong> AI helps streamline the process, leading to quicker outcomes for both parties.</li>
  </ul>

      <h3>Innovative Approach</h3>
      <p>Combining AI technology with human empathy for smarter mediation.</p>
    </div>
    <div class="highlight-card">
      <h3>Confidential & Safe</h3>
      <p>Strong privacy protections and secure handling of all information.</p>
    </div>
    <div class="highlight-card">
      <h3>Cost-Effective</h3>
      <p>Reducing reliance on expensive legal procedures.</p>
    </div>
    <div class="highlight-card">
      <h3>User-Friendly</h3>
      <p>Simple, clear, and accessible platform for all users.</p>
    </div>
  </div>

  <p style="font-style: italic; border-top: 1px solid var(--border); padding-top: 40px; margin-top: 60px;">
    First Mediator LTD is more than a platform—it’s a modern solution for resolving disputes, helping people move forward with clarity, confidence, and peace of mind at just the cost of a cup of coffee with friends.
  </p>
</main>

<footer>
  <div class="footer-inner">
    <a href="/" class="logo">
      <img src="{{ asset('assets/images/logos/fm-lightmode.png') }}" alt="First Mediator" style="height: 48px; display: var(--logo-light-display) !important;">
      <img src="{{ asset('assets/images/logos/fm-darkmode.png') }}" alt="First Mediator" style="height: 48px; display: var(--logo-dark-display) !important;">
    </a>
    <ul class="footer-links">
      <li><a href="/#how">How it works</a></li>
      <li><a href="{{ route('about') }}">About Us</a></li>
      <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
      <li><a href="{{ route('gdpr') }}">GDPR Policy</a></li>
      <li><a href="{{ route('terms') }}">Terms of Service</a></li>
      <li><a href="{{ route('disclaimer') }}">Disclaimer</a></li>
      <li><a href="mailto:info@firstmediator.com">Contact</a></li>
    </ul>
    <p class="footer-copy">© 2026 FirstMediator</p>
  </div>
</footer>

<script>
function toggleTheme() {
  const html = document.documentElement;
  const currentTheme = html.getAttribute('data-theme');
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
  html.setAttribute('data-theme', newTheme);
  localStorage.setItem('firstmediator_theme', newTheme);
  document.getElementById('theme-icon').textContent = newTheme === 'dark' ? '☀️' : '🌙';
  
  const isDark = newTheme === 'dark';
  html.style.setProperty('--logo-light-display', isDark ? 'none' : 'block');
  html.style.setProperty('--logo-dark-display', isDark ? 'block' : 'none');
}

// Persist theme
(function() {
  const theme = localStorage.getItem('firstmediator_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  document.documentElement.setAttribute('data-theme', theme);
  const isDark = theme === 'dark';
  if (isDark) {
    document.documentElement.style.setProperty('--logo-light-display', 'none');
    document.documentElement.style.setProperty('--logo-dark-display', 'block');
  }
})();
</script>
</body>
</html>
