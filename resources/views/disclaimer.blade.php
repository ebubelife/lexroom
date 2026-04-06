<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Disclaimer — First Mediator</title>
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
  <h1>Disclaimer</h1>
  
  <div class="intro-banner">
    We provide an AI-powered mediation platform to assist parties in resolving disputes. By using our Service, you acknowledge and agree to the following:
  </div>

  <h2>1. Informational Purposes Only</h2>
  <ul>
    <li>The AI and platform are designed to assist and guide the mediation process.</li>
    <li>The Service does not provide legal, financial, or professional advice.</li>
    <li>Any recommendations or suggestions provided by the AI are for informational purposes only.</li>
  </ul>

  <h2>2. No Guarantees</h2>
  <ul>
    <li>We cannot guarantee the outcome of any mediation or resolution.</li>
    <li>The Service is not legally binding, and the results may vary depending on user inputs and cooperation between parties.</li>
  </ul>

  <h2>3. Use at Your Own Risk</h2>
  <ul>
    <li>Users assume full responsibility for decisions made based on AI guidance.</li>
    <li>We are not liable for any loss, damage, or dispute arising from the use of the Service.</li>
  </ul>

  <h2>4. Professional Advice</h2>
  <ul>
    <li>Users are encouraged to seek independent legal, financial, or professional advice if required.</li>
    <li>Our Service does not replace official mediation, arbitration, or court proceedings.</li>
  </ul>

  <div class="contact-box">
    <h2>5. Contact Us</h2>
    <p>If you have any questions about this Disclaimer, please contact us:</p>
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
