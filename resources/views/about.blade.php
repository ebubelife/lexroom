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
  max-width: 800px; margin: 120px auto; /* Increased top margin for fixed header */
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
  gap: 48px;
  margin: 80px 0;
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

@media (max-width: 900px) {
  .highlight-section { grid-template-columns: 1fr; }
  .page-container { margin-top: 100px; }
  h1 { font-size: 42px; }
}
</style>
</head>
<body>

@include('partials.navbar')

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
  <div class="highlight-section">
    <div class="highlight-card">
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

@include('partials.footer')

<!-- Logic handled by shared-layout.js -->
</body>
</html>
