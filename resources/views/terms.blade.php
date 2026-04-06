<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Terms & Conditions — First Mediator</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/shared-layout.css') }}">
<script src="{{ asset('js/shared-layout.js') }}"></script>

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

@media (max-width: 900px) {
  .page-container { margin-top: 100px; }
  h1 { font-size: 42px; }
}
</style>
</head>
<body>

@include('partials.navbar')

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

@include('partials.footer')

</body>
</html>
