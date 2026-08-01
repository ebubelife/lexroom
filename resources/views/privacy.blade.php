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
[data-theme="dark"] {
  --bg-alt: #0F2336;
}
</style>

<link rel="stylesheet" href="{{ asset('css/shared-layout.css') }}">
<script src="{{ asset('js/shared-layout.js') }}"></script>

<!-- Tailwind (compiled) -- required for the shared footer's utility classes -->
@vite(['resources/css/app.css'])

<style>
/* ── CONTENT ── */
.page-container {
  max-width: 800px; margin: 120px auto;
  padding: 0 24px;
}
h1 {
  font-family: var(--serif);
  font-size: clamp(40px, 8vw, 64px);
  font-weight: 400;
  margin-bottom: 40px;
  line-height: 1.1;
  color: var(--text-primary);
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

@media (max-width: 900px) {
  .page-container { margin-top: 100px; }
  h1 { font-size: 42px; }
}
</style>
</head>
<body>

@include('partials.navbar')

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

@include('partials.footer')

<!-- Logic handled by shared-layout.js -->
</body>
</html>
</body>
</html>
