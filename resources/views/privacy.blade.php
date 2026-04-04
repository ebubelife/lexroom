<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Privacy Policy — First Mediator</title>
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
  padding: 60px 24px;
  background: var(--bg);
}
.footer-inner {
  max-width: 900px; margin: 0 auto;
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 32px;
}
.footer-links { display: flex; gap: 24px; list-style: none; }
.footer-links a {
  font-size: 14px; color: var(--text-muted);
  text-decoration: none;
  transition: color 0.2s;
}
.footer-links a:hover { color: var(--gold); }
.footer-copy { font-size: 13px; color: var(--text-muted); }

@media (max-width: 600px) {
  .footer-inner { 
    flex-direction: column; 
    align-items: center; 
    text-align: center; 
    gap: 48px; 
  }
  .footer-links { 
    flex-direction: column; 
    gap: 20px; 
    align-items: center; 
  }
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
  <h1>Privacy Policy</h1>
  <p>We are committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your personal information when you use our AI-powered mediation platform (“Service”).</p>

  <h2>1. Information We Collect</h2>
  <p>We may collect the following information:</p>
  <ul>
    <li><strong>Personal Information:</strong> Name, email address, phone number, and any information you provide when creating an account or participating in mediation.</li>
    <li><strong>Dispute Information:</strong> Details of the dispute, communications, and any documents you submit.</li>
    <li><strong>Usage Data:</strong> Information about your use of the platform, including IP address, device type, browser, and activity logs.</li>
  </ul>

  <h2>2. How We Use Your Information</h2>
  <p>We use your data to:</p>
  <ul>
    <li>Facilitate the mediation process between parties.</li>
    <li>Improve and maintain the functionality of the Service.</li>
    <li>Communicate with you regarding your account or disputes.</li>
    <li>Comply with legal obligations when required.</li>
  </ul>

  <h2>3. Data Sharing</h2>
  <p>Your personal data will not be sold or shared with third parties for marketing.</p>
  <p>We may share your information only with:</p>
  <ul>
    <li>The other party in the dispute for the purposes of mediation.</li>
    <li>Our service providers who help operate the platform.</li>
    <li>Legal authorities when required by law.</li>
  </ul>

  <h2>4. Data Retention</h2>
  <p>We keep your data only as long as necessary for the mediation process and to comply with legal requirements.</p>
  <p>You can request deletion of your personal data at any time by contacting us (see Section 9).</p>

  <h2>5. Your Rights</h2>
  <p>Under UK and EU data protection laws, you have the right to:</p>
  <ul>
    <li>Access the personal information we hold about you.</li>
    <li>Request correction of inaccurate data.</li>
    <li>Request deletion of your personal data.</li>
    <li>Withdraw consent to processing where consent is the legal basis.</li>
    <li>Lodge a complaint with a supervisory authority if you believe your data is mishandled.</li>
  </ul>

  <h2>6. Data Security</h2>
  <p>We implement appropriate technical and organizational measures to protect your data. However, no system is completely secure, and we cannot guarantee absolute protection.</p>

  <h2>7. Cookies and Tracking</h2>
  <p>We may use cookies and similar technologies to improve your experience. Cookies may track usage patterns but do not identify individuals unless voluntarily provided.</p>

  <h2>8. Children’s Privacy</h2>
  <p>Our platform is not intended for children under 18. We do not knowingly collect personal information from minors.</p>

  <div class="contact-box">
    <h2>9. Contact Us</h2>
    <p>For questions or requests regarding your personal data, please contact:</p>
    <p>
      <strong>First Mediator LTD</strong><br>
      86-90, Paul Street, London. EC2A 4NE<br>
      Email: <a href="mailto:info@firstmediator.com" style="color: var(--gold); text-decoration: none;">info@firstmediator.com</a>
    </p>
  </div>

  <h2>10. Policy Updates</h2>
  <p>We may update this Privacy Policy from time to time. The updated version will be posted on our website with a new effective date. Continued use of the Service constitutes acceptance of the updated Privacy Policy.</p>
</main>

<footer>
  <div class="footer-inner">
    <a href="/" class="logo" style="height: 48px;">
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
  const isDark = html.getAttribute('data-theme') === 'dark';
  html.setAttribute('data-theme', isDark ? 'light' : 'dark');
  document.getElementById('theme-icon').textContent = isDark ? '🌙' : '☀️';
  
  // Update logo display
  html.style.setProperty('--logo-light-display', isDark ? 'block' : 'none');
  html.style.setProperty('--logo-dark-display', isDark ? 'none' : 'block');
}

// Persist theme
(function() {
  const theme = localStorage.getItem('firstmediator_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  document.documentElement.setAttribute('data-theme', theme);
  const isDark = theme === 'dark';
  if (isDark) {
    document.getElementById('theme-icon').textContent = '☀️';
    document.documentElement.style.setProperty('--logo-light-display', 'none');
    document.documentElement.style.setProperty('--logo-dark-display', 'block');
  }
})();

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
</script>
</body>
</html>
