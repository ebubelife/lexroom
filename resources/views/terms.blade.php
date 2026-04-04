<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Terms & Conditions — First Mediator</title>
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

.intro-banner {
  background: var(--bg-alt);
  border-left: 4px solid var(--gold);
  padding: 32px;
  border-radius: 0 12px 12px 0;
  margin-bottom: 40px;
  font-style: italic;
}

.contact-box {
  background: var(--bg-alt);
  border: 1px solid var(--border);
  padding: 32px;
  border-radius: 16px;
  margin-top: 40px;
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
  <h1>Terms & Conditions</h1>
  
  <div class="intro-banner">
    Our platform uses AI to help two parties resolve disputes while the AI analysis acts as an unbiased umpire and issues recommendations. By using our service, you agree to these terms, so please read them carefully.
  </div>

  <h2>1. Using Our Service</h2>
  <ul>
    <li>You must be at least 18 years old (or legally able to enter contracts).</li>
    <li>Our AI helps with mediation, but it does not give legal advice or make legally binding decisions.</li>
  </ul>

  <h2>2. Your Responsibilities</h2>
  <ul>
    <li>Be honest and accurate when providing information.</li>
    <li>Treat the other party and our platform with respect.</li>
    <li>Use the service only for legitimate mediation.</li>
  </ul>

  <h2>3. Privacy</h2>
  <p>We keep your information private and follow our Privacy Policy. Data is used only to help the mediation process and will not be shared without your consent, except if required by law.</p>

  <h2>4. AI Limitations</h2>
  <p>The AI gives guidance and suggestions but cannot enforce outcomes. Always consider professional advice if needed.</p>

  <h2>5. Prohibited Activities</h2>
  <p>Do not:</p>
  <ul>
    <li>Harass or threaten anyone.</li>
    <li>Try to hack or disrupt our platform.</li>
    <li>Use our service for illegal purposes.</li>
  </ul>

  <h2>6. Intellectual Property</h2>
  <p>The platform, AI, and content belong to First Mediator LTD. Don’t copy or use our materials without permission.</p>

  <h2>7. Termination</h2>
  <p>We can suspend or remove your access if you break these rules or misuse the platform, and no refunds will be issued.</p>

  <h2>8. Liability</h2>
  <p>We are not responsible for outcomes of disputes or any damages from using the platform. The AI is here to assist, not guarantee results.</p>

  <h2>9. Governing Law</h2>
  <p>These terms are governed by the laws of England and Wales. Any legal disputes will be handled in courts in England.</p>

  <div class="contact-box">
    <h2>10. Contact Us</h2>
    <p>If you have questions:</p>
    <p>
      <strong>First Mediator LTD</strong><br>
      86 – 90, Paul Street, London EC2A 4NE<br>
      Email: <a href="mailto:info@firstmediator.com" style="color: var(--gold); text-decoration: none;">info@firstmediator.com</a>
    </p>
  </div>
</main>

<footer>
  <div class="footer-inner">
    <div class="footer-grid">
      <!-- Col 1: Logo -->
      <div class="footer-col">
        <a href="/" class="logo">
          <img src="{{ asset('assets/images/logos/fm-lightmode.png') }}" alt="First Mediator" style="height: 60px; display: var(--logo-light-display) !important;">
          <img src="{{ asset('assets/images/logos/fm-darkmode.png') }}" alt="First Mediator" style="height: 60px; display: var(--logo-dark-display) !important;">
        </a>
      </div>

      <!-- Col 2: Legal -->
      <div class="footer-col">
          <h4>Legal</h4>
          <ul class="footer-links">
              <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
              <li><a href="{{ route('gdpr') }}">GDPR Policy</a></li>
              <li><a href="{{ route('terms') }}">Terms of Service</a></li>
              <li><a href="{{ route('disclaimer') }}">Disclaimer</a></li>
          </ul>
      </div>

      <!-- Col 3: Company -->
      <div class="footer-col">
          <h4>Company</h4>
          <ul class="footer-links">
              <li><a href="/">Home</a></li>
              <li><a href="{{ route('about') }}">About Us</a></li>
              <li><a href="/login">Login</a></li>
              <li><a href="/register">Sign Up</a></li>
          </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p class="footer-copy">© 2026 FirstMediator &middot; Dispute Resolution, Without the Legal Bill.</p>
    </div>
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
