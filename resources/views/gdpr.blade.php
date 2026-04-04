<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GDPR Privacy Policy — First Mediator</title>
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
  .footer-inner { flex-direction: column; align-items: center; text-align: center; gap: 40px; }
  .footer-links { flex-direction: column; gap: 16px; align-items: center; }
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
  <h1>GDPR Privacy Policy</h1>
  
  <p class="intro-banner">
    First Mediator LTD (“we”, “us”, or “our”) respects your privacy and is committed to complying with the UK GDPR and Data Protection Act 2018. This policy explains how we collect, use, store, and protect your personal data when you use our AI mediation platform (“Service”).
  </p>

  <h2>1. Data Controller</h2>
  <p>First Mediator LTD is the data controller responsible for your personal information.</p>
  <p>
    <strong>Contact:</strong><br>
    86 – 90, Paul Street, London EC2A 4NE<br>
    Email: <a href="mailto:info@firstmediator.com" style="color: var(--gold); text-decoration: none;">info@firstmediator.com</a>
  </p>

  <h2>2. Personal Data We Collect</h2>
  <p>We may collect:</p>
  <ul>
    <li><strong>Identity & Contact Info:</strong> Name, email address, phone number, account details.</li>
    <li><strong>Dispute & Mediation Data:</strong> Messages, documents, and communications you provide during mediation.</li>
    <li><strong>Technical Data:</strong> IP address, device type, browser, usage patterns, and cookies.</li>
  </ul>

  <h2>3. How We Use Your Data</h2>
  <p>We process your personal data for these purposes:</p>
  <ul>
    <li><strong>Providing Mediation Services:</strong> Facilitate communication and assist in dispute resolution.</li>
    <li><strong>Platform Operations:</strong> Improve functionality, performance, and security.</li>
    <li><strong>Legal Compliance:</strong> Fulfil our obligations under UK law.</li>
    <li><strong>Communication:</strong> Notify you about account activity, updates, or changes.</li>
  </ul>

  <h2>4. Legal Basis for Processing</h2>
  <p>We process your personal data based on the following GDPR legal grounds:</p>
  <ul>
    <li><strong>Consent:</strong> When you agree to use our Service.</li>
    <li><strong>Contractual Necessity:</strong> To provide mediation services.</li>
    <li><strong>Legal Obligation:</strong> To comply with applicable laws.</li>
    <li><strong>Legitimate Interests:</strong> To maintain and improve the platform while respecting your rights.</li>
  </ul>

  <h2>5. Data Sharing</h2>
  <p>We will not sell your data. Your information may be shared with:</p>
  <ul>
    <li>The other party involved in the mediation.</li>
    <li>Service providers necessary for platform operation.</li>
    <li>Legal authorities if required by law.</li>
  </ul>

  <h2>6. Data Retention</h2>
  <p>Your personal data is retained only as long as necessary for mediation and legal compliance. You may request deletion of your personal data at any time (see Section 12).</p>

  <h2>7. Your GDPR Rights</h2>
  <p>Under the GDPR, you have the right to:</p>
  <ul>
    <li><strong>Access:</strong> Request a copy of your personal data.</li>
    <li><strong>Rectification:</strong> Correct inaccurate or incomplete data.</li>
    <li><strong>Erasure (“Right to be Forgotten”):</strong> Delete your data where lawful.</li>
    <li><strong>Restrict Processing:</strong> Limit how we use your data.</li>
    <li><strong>Data Portability:</strong> Receive your data in a structured, machine-readable format.</li>
    <li><strong>Object:</strong> Object to processing based on legitimate interests.</li>
    <li><strong>Withdraw Consent:</strong> Withdraw previously given consent.</li>
    <li><strong>Complaint:</strong> Lodge a complaint with the Information Commissioner’s Office (ICO).</li>
  </ul>

  <h2>8. Data Security</h2>
  <p>We implement technical and organisational measures to protect your personal data. While we take security seriously, no system is completely secure.</p>

  <h2>9. Cookies and Tracking</h2>
  <p>We may use cookies to improve your experience. Cookies track usage patterns but do not personally identify you unless voluntarily provided.</p>

  <h2>10. Children</h2>
  <p>Our platform is not for individuals under 18 years old. We do not knowingly collect data from children.</p>

  <h2>11. Changes to this Policy</h2>
  <p>We may update this GDPR Policy occasionally. Updated policies will be posted on our website with the new effective date. Continued use of the Service indicates acceptance of the updated policy.</p>

  <div class="contact-box">
    <h2>12. Contact</h2>
    <p>For GDPR-related inquiries, data access, or deletion requests, contact:</p>
    <p>
      <strong>First Mediator LTD</strong><br>
      86 – 90, Paul Street, London EC2A 4NE<br>
      Email: <a href="mailto:info@firstmediator.com" style="color: var(--gold); text-decoration: none;">info@firstmediator.com</a>
    </p>
  </div>
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
