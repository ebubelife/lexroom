<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Primary Meta Tags -->
<title>First Mediator | AI-Powered Online Mediation & Dispute Resolution</title>
<meta name="title" content="First Mediator | AI-Powered Online Mediation & Dispute Resolution">
<meta name="description" content="First Mediator uses artificial intelligence to help individuals, freelancers, and businesses resolve disputes quickly, fairly, and confidentially without expensive legal fees.">
<meta name="keywords" content="online mediation, AI dispute resolution, legal mediation UK, AI mediator, dispute settlement, contract dispute resolution, freelance dispute mediation, alternative dispute resolution, ADR, First Mediator">
<meta name="author" content="First Mediator LTD">
<meta name="robots" content="index, follow">
<meta name="theme-color" content="#0D1B2A">
<link rel="canonical" href="{{ url('/') }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:title" content="First Mediator | AI-Powered Online Mediation & Dispute Resolution">
<meta property="og:description" content="Resolve disputes faster, fairer, and affordably with unbiased AI-assisted online mediation.">
<meta property="og:image" content="{{ asset('assets/images/logos/FM_Logo_Gold.svg') }}">
<meta property="og:site_name" content="First Mediator">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ url('/') }}">
<meta property="twitter:title" content="First Mediator | AI-Powered Online Mediation & Dispute Resolution">
<meta property="twitter:description" content="Resolve disputes faster, fairer, and affordably with unbiased AI-assisted online mediation.">
<meta property="twitter:image" content="{{ asset('assets/images/logos/FM_Logo_Gold.svg') }}">

<!-- Structured Data (JSON-LD) for SEO -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "LegalService",
  "name": "First Mediator LTD",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('assets/images/logos/FM_Icon.svg') }}",
  "description": "UK-based AI-powered online mediation and alternative dispute resolution platform.",
  "address": {
    "@@type": "PostalAddress",
    "streetAddress": "86-90 Paul Street",
    "addressLocality": "London",
    "postalCode": "EC2A 4NE",
    "addressCountry": "UK"
  },
  "email": "info@firstmediator.com",
  "priceRange": "£",
  "areaServed": "Global"
}
</script>

<link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/logos/FM_Icon.svg') }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/shared-layout.css') }}">
<script src="{{ asset('js/shared-layout.js') }}"></script>

<!-- Tailwind (compiled, project tokens: navy / gold / gold-light / gold-pale, fontFamily serif/sans) -->
@vite(['resources/css/app.css'])

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
  --serif: 'Instrument Serif', Georgia, serif;
  --sans: 'DM Sans', system-ui, sans-serif;
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
  --shadow: 0 1px 3px rgba(0,0,0,0.3), 0 4px 16px rgba(0,0,0,0.2);
  --shadow-lg: 0 4px 24px rgba(0,0,0,0.4);
  --logo-light-display: none !important;
  --logo-dark-display: block !important;
}
*, *::before, *::after { box-sizing: border-box; }
html { scroll-behavior: smooth; }
body { background: var(--bg); font-family: var(--sans); }
h1, h2, h3 { font-family: var(--sans); letter-spacing: -0.02em; line-height: 1.15; font-weight: 600; }
.accent-serif { font-family: var(--serif); font-style: italic; font-weight: 400; }

/* ── SCROLL REVEAL (site-standard, same mechanism as welcome.blade.php) ── */
.reveal { opacity: 0; transform: translateY(28px); transition: opacity .65s cubic-bezier(.16,1,.3,1), transform .65s cubic-bezier(.16,1,.3,1); }
.reveal.visible { opacity: 1; transform: none; }
.reveal-delay-1 { transition-delay: .1s; }
.reveal-delay-2 { transition-delay: .2s; }
.reveal-delay-3 { transition-delay: .3s; }
.reveal-delay-4 { transition-delay: .4s; }

.hero-eyebrow-dot { animation: pulse-dot 2s ease-in-out infinite; }
@keyframes pulse-dot { 0%,100% { opacity:1; transform:scale(1);} 50% { opacity:.5; transform:scale(.7);} }

.btn-gold {
  background: var(--gold); color: var(--navy);
  transition: background .2s, box-shadow .2s, transform .2s;
}
.btn-gold:hover { background: var(--gold-light); box-shadow: 0 4px 16px rgba(201,168,76,.35); }
.btn-outline {
  border: 1.5px solid var(--border); color: var(--text-primary);
  transition: border-color .2s, color .2s;
}
.btn-outline:hover { border-color: var(--gold); color: var(--gold); }

.card { background: var(--card-bg); border: 1px solid var(--border); }
.card-hover { transition: border-color .2s, box-shadow .2s, transform .2s; }
.card-hover:hover { border-color: var(--gold); transform: translateY(-2px); box-shadow: 0 0 0 3px rgba(201,168,76,.08); }

.mockup-dot:nth-child(1) { background:#FF5F57; }
.mockup-dot:nth-child(2) { background:#FFBD2E; }
.mockup-dot:nth-child(3) { background:#28C840; }

@media (max-width: 900px) {
  .grid-3, .grid-4, .grid-9 { grid-template-columns: 1fr !important; }
}
@media (min-width: 640px) and (max-width: 900px) {
  .grid-9 { grid-template-columns: repeat(2,1fr) !important; }
}
</style>
</head>
<body>

@include('partials.navbar')

<!-- ================= HERO ================= -->
<section id="hero" class="pt-40 pb-20 px-6 text-center relative">
  <div class="max-w-3xl mx-auto">
    <div class="reveal inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-sm mb-8" style="background:var(--gold-pale); color:#8B6B1A;">
      <span class="hero-eyebrow-dot w-1.5 h-1.5 rounded-full bg-gold inline-block"></span>
      AI-assisted mediation · No lawyer required
    </div>
    <h1 class="reveal reveal-delay-1 text-5xl sm:text-6xl md:text-7xl mb-6" style="color:var(--text-primary); font-weight:700;">
      Resolve disputes without <em class="accent-serif text-gold" style="font-weight:900; -webkit-text-stroke: 0.4px currentColor;">expensive lawyers.</em>
    </h1>
    <p class="reveal reveal-delay-2 text-lg font-normal max-w-2xl mx-auto mb-10" style="color:var(--text-secondary);">
      Two parties. One room. An impartial AI mediator. A formal resolution report — in under 30 minutes. No retainers. No court dates.
    </p>
    <div class="reveal reveal-delay-3 flex items-center justify-center gap-3 flex-wrap">
      <a href="{{ route('register') }}" class="btn-gold inline-flex items-center gap-2 text-base font-semibold px-8 py-3.5 rounded-lg">
        Create a Mediation Room
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
    </div>
  </div>

  <!-- Browser mockup: illustrative example session, not a real case -->
  <div class="reveal reveal-delay-4 max-w-4xl mx-auto mt-16">
    <div class="card rounded-2xl overflow-hidden text-left" style="box-shadow:var(--shadow-lg);">
      <div class="flex items-center gap-2 px-4 py-3 border-b" style="background:var(--bg-alt); border-color:var(--border);">
        <div class="flex gap-1.5">
          <span class="mockup-dot w-2.5 h-2.5 rounded-full inline-block"></span>
          <span class="mockup-dot w-2.5 h-2.5 rounded-full inline-block"></span>
          <span class="mockup-dot w-2.5 h-2.5 rounded-full inline-block"></span>
        </div>
        <div class="flex-1 max-w-xs mx-auto text-center text-xs rounded px-3 py-1 border" style="background:var(--bg); border-color:var(--border); color:var(--text-muted);">
          firstmediator.com/room/inv-2024-0847
        </div>
      </div>
      <div class="grid md:grid-cols-[1fr_280px]">
        <div class="p-6 flex flex-col gap-3.5 border-r" style="border-color:var(--border);">
          <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-semibold tracking-widest uppercase text-gold">Cross-examination</span>
            <span class="text-sm font-semibold tabular-nums rounded px-2.5 py-1" style="background:var(--bg-alt); color:var(--text-primary);">42:18</span>
          </div>
          <div class="flex gap-2.5 items-start">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0" style="background:#E8F0FE;color:#2563EB;">A</div>
            <div class="text-sm rounded-xl rounded-bl-sm px-3.5 py-2.5" style="background:#EFF6FF; color:#1E293B; max-width:75%;">The invoice was sent on March 3rd. I have email proof. Payment was due within 14 days.</div>
          </div>
          <div class="flex gap-2.5 items-start flex-row-reverse">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0" style="background:#F3E8FF;color:#7C3AED;">B</div>
            <div class="text-sm rounded-xl rounded-br-sm px-3.5 py-2.5" style="background:#F5F3FF; color:#1E293B; max-width:75%;">The deliverables were incomplete. The final logo variants were never submitted.</div>
          </div>
          <div class="text-sm rounded-lg px-3.5 py-3" style="background:linear-gradient(135deg, rgba(201,168,76,.08), rgba(201,168,76,.04)); border:1px solid rgba(201,168,76,.2); color:var(--text-primary);">
            <span class="block text-[10px] font-bold tracking-widest uppercase mb-1.5 text-gold">⚖ First Mediator</span>
            Party A — your invoice (Exhibit 1) references 3 logo variants. Party B — can you confirm which specific variants were not delivered?
          </div>
          <div class="flex gap-2.5 items-start">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0" style="background:#E8F0FE;color:#2563EB;">A</div>
            <div class="text-sm rounded-xl rounded-bl-sm px-3.5 py-2.5" style="background:#EFF6FF; color:#1E293B; max-width:75%;">All 3 variants were delivered — see the link in my last email, attached as Exhibit 3.</div>
          </div>
        </div>
        <div class="p-5 flex flex-col gap-4" style="background:var(--bg-alt);">
          <div>
            <div class="text-[11px] font-semibold tracking-widest uppercase mb-2.5" style="color:var(--text-muted);">Session phases</div>
            <div class="flex flex-col gap-2 text-xs">
              <div class="flex items-center gap-2" style="color:var(--text-secondary);"><span class="w-2 h-2 rounded-full inline-block" style="background:#22C55E;"></span>Opening statements</div>
              <div class="flex items-center gap-2" style="color:var(--text-secondary);"><span class="w-2 h-2 rounded-full inline-block" style="background:#22C55E;"></span>Evidence submission</div>
              <div class="flex items-center gap-2 font-medium" style="color:var(--text-primary);"><span class="w-2 h-2 rounded-full inline-block bg-gold"></span>Cross-examination</div>
              <div class="flex items-center gap-2" style="color:var(--text-secondary);"><span class="w-2 h-2 rounded-full inline-block" style="background:var(--border);"></span>Analysis &amp; findings</div>
              <div class="flex items-center gap-2" style="color:var(--text-secondary);"><span class="w-2 h-2 rounded-full inline-block" style="background:var(--border);"></span>Resolution proposal</div>
            </div>
          </div>
          <div>
            <div class="text-[11px] font-semibold tracking-widest uppercase mb-2.5" style="color:var(--text-muted);">Evidence vault</div>
            <div class="flex flex-col gap-2 text-xs" style="color:var(--text-secondary);">
              <div class="flex items-center gap-2"><span class="w-6 h-6 rounded flex items-center justify-center text-sm" style="background:var(--gray-100);">📄</span>Invoice_March.pdf</div>
              <div class="flex items-center gap-2"><span class="w-6 h-6 rounded flex items-center justify-center text-sm" style="background:var(--gray-100);">📧</span>Email_thread.pdf</div>
              <div class="flex items-center gap-2"><span class="w-6 h-6 rounded flex items-center justify-center text-sm" style="background:var(--gray-100);">🖼️</span>Logo_variants.png</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= TRUST BAR ================= -->
<section class="py-10 px-6 border-y" style="border-color:var(--border); background:var(--bg-alt);">
  <div class="max-w-5xl mx-auto flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-sm" style="color:var(--text-secondary);">
    <span class="flex items-center gap-2"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" class="text-gold"><path d="M12 1l8 4v6c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V5l8-4z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg><strong style="color:var(--text-primary);">End-to-end encrypted</strong></span>
    <span class="flex items-center gap-2"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" class="text-gold"><path d="M12 3v18M5 7h14M5 7l-3 6a3 3 0 006 0l-3-6zm14 0l-3 6a3 3 0 006 0l-3-6z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg><strong style="color:var(--text-primary);">AI-powered</strong></span>
    <span class="flex items-center gap-2"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" class="text-gold"><path d="M9 11l3 3L22 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg><strong style="color:var(--text-primary);">Evidence-based</strong></span>
    <span class="flex items-center gap-2"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" class="text-gold"><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg><strong style="color:var(--text-primary);">Fast decisions</strong></span>
    <span class="flex items-center gap-2"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" class="text-gold"><path d="M12 1l8 4v6c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V5l8-4zM9 12l2 2 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg><strong style="color:var(--text-primary);">Confidential</strong></span>
  </div>
  <div class="grid-4 max-w-4xl mx-auto grid grid-cols-4 gap-6 mt-10 text-center">
    <div>
      <div class="font-bold text-3xl" style="color:var(--text-primary);"><span class="text-gold">15</span> min</div>
      <div class="text-xs mt-1" style="color:var(--text-secondary);">Average time to set up and start a session</div>
    </div>
    <div>
      <div class="font-bold text-3xl" style="color:var(--text-primary);"><span class="text-gold">100%</span></div>
      <div class="text-xs mt-1" style="color:var(--text-secondary);">AI-impartial — no side, no bias, no agenda</div>
    </div>
    <div>
      <div class="font-bold text-3xl" style="color:var(--text-primary);"><span class="text-gold">PDF</span></div>
      <div class="text-xs mt-1" style="color:var(--text-secondary);">Formal, timestamped resolution report</div>
    </div>
    <div>
      <div class="font-bold text-3xl" style="color:var(--text-primary);"><span class="text-gold">0</span></div>
      <div class="text-xs mt-1" style="color:var(--text-secondary);">Subscriptions required — <a href="{{ route('pricing') }}" class="underline text-gold">see pricing</a></div>
    </div>
  </div>
</section>

<!-- ================= PROBLEM ================= -->
<section class="py-24 px-6">
  <div class="max-w-5xl mx-auto">
    <div class="reveal section-label text-xs font-semibold tracking-widest uppercase mb-4 text-gold">The problem</div>
    <h2 class="reveal text-4xl sm:text-5xl mb-14 max-w-2xl" style="color:var(--text-primary);">
      Between a WhatsApp argument and a <em class="accent-serif text-gold">courtroom,</em> there was nothing. Until now.
    </h2>
    <div class="grid md:grid-cols-2 gap-6">
      <div class="reveal reveal-delay-1 card rounded-2xl p-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 rounded-full border flex items-center justify-center" style="border-color:var(--border); color:var(--text-muted);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </div>
          <div>
            <div class="font-semibold" style="color:var(--text-primary);">Traditional route</div>
            <div class="text-sm" style="color:var(--text-muted);">Lawyers, courts, arbitration</div>
          </div>
        </div>
        <div class="flex flex-col gap-3">
          @foreach ([
            'Expensive solicitors (typically £200–£500/hr)',
            'Weeks or months of delays',
            'Endless back-and-forth arguments',
            'No structured process',
            'Evidence buried in emails',
            'Unpredictable outcomes',
          ] as $item)
          <div class="flex items-center gap-3 text-sm" style="color:var(--text-secondary);">
            <span class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0" style="background:var(--bg-alt); color:var(--text-muted);">
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </span>
            {{ $item }}
          </div>
          @endforeach
        </div>
      </div>
      <div class="reveal reveal-delay-2 rounded-2xl p-8" style="background:var(--card-bg); border:1px solid rgba(201,168,76,.35); box-shadow: inset 0 0 60px rgba(201,168,76,.04);">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 rounded-full bg-gold flex items-center justify-center text-navy">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
          </div>
          <div>
            <div class="font-semibold" style="color:var(--text-primary);">First Mediator</div>
            <div class="text-sm" style="color:var(--text-muted);">AI-powered online resolution</div>
          </div>
        </div>
        <div class="flex flex-col gap-3">
          @foreach ([
            'Flat, transparent fee per case',
            'Resolution typically under 90 minutes',
            'Structured AI-guided process',
            'Clear room with a full timeline',
            'Evidence uploaded securely',
            'Calm, impartial AI mediator',
          ] as $item)
          <div class="flex items-center gap-3 text-sm font-medium" style="color:var(--text-primary);">
            <span class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 text-white" style="background:#22C55E;">
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
            </span>
            {{ $item }}
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= HOW IT WORKS ================= -->
<section id="how" class="py-24 px-6 border-t" style="border-color:var(--border);">
  <div class="max-w-4xl mx-auto">
    <div class="reveal section-label text-xs font-semibold tracking-widest uppercase mb-4 text-gold">How it works</div>
    <h2 class="reveal text-4xl sm:text-5xl mb-3" style="color:var(--text-primary);">Five steps to resolution.</h2>
    <p class="reveal mb-16 max-w-xl" style="color:var(--text-secondary);">A structured process designed for clarity, not courtrooms.</p>

    <div class="relative">
      <div class="absolute left-7 top-6 bottom-6 w-px hidden sm:block" style="background:var(--border);"></div>
      <div class="flex flex-col gap-10">
        @foreach ([
          ['num'=>'01','icon'=>'🏛️','title'=>'Create a Room','desc'=>'Select your dispute type, jurisdiction, and session duration. Write a brief case summary. Setup takes under 15 minutes.'],
          ['num'=>'02','icon'=>'📨','title'=>'Invite the Other Party','desc'=>'Send a secure room link to the other person. They accept, create an account, and the room opens — no lawyers required on either side.'],
          ['num'=>'03','icon'=>'📎','title'=>'Upload Evidence','desc'=>'Both sides upload contracts, invoices, screenshots. Everything is encrypted and shared only within the room.'],
          ['num'=>'04','icon'=>'⚡','title'=>'AI Reviews Everything','desc'=>'First Mediator reads every submission, asks targeted clarifying questions, and analyses the evidence impartially.'],
          ['num'=>'05','icon'=>'📄','title'=>'Receive Your Report','desc'=>'A formal, timestamped PDF report is generated — findings, resolution recommendation, and a confidence score.'],
        ] as $i => $step)
        @php $stepBorder = $i === 3 ? 'var(--gold)' : 'rgba(201,168,76,.3)'; @endphp
        <div class="reveal reveal-delay-{{ min($i+1,4) }} flex gap-6 items-start">
          <div class="w-14 h-14 rounded-xl border flex items-center justify-center text-xl flex-shrink-0 z-10" style="background:var(--bg); border-color:{{ $stepBorder }};">
            {{ $step['icon'] }}
          </div>
          <div class="flex-1 pb-2">
            <div class="flex items-center gap-3 mb-1.5">
              <span class="text-xs font-mono" style="color:var(--text-muted);">{{ $step['num'] }}</span>
              <h3 class="text-xl" style="color:var(--text-primary);">{{ $step['title'] }}</h3>
            </div>
            <p class="text-sm sm:text-base max-w-xl" style="color:var(--text-secondary);">{{ $step['desc'] }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <div class="mt-12">
      <a href="{{ route('register') }}" class="btn-gold inline-flex items-center gap-2 font-semibold px-6 py-3 rounded-lg">
        Start your mediation
      </a>
    </div>
  </div>
</section>

<!-- ================= FEATURES ================= -->
<section id="features" class="py-24 px-6 border-t" style="border-color:var(--border); background:var(--bg-alt);">
  <div class="max-w-5xl mx-auto">
    <div class="reveal section-label text-xs font-semibold tracking-widest uppercase mb-4 text-gold">Features</div>
    <h2 class="reveal text-4xl sm:text-5xl mb-14" style="color:var(--text-primary);">Everything you need, <span class="text-gold">nothing you don't.</span></h2>

    <div class="grid md:grid-cols-3 gap-4 mb-4">
      <div class="reveal reveal-delay-1 card card-hover md:col-span-2 rounded-2xl p-7">
        <div class="w-10 h-10 rounded-lg border flex items-center justify-center text-xl mb-4" style="border-color:var(--border);">⚡</div>
        <div class="text-xl mb-2 font-semibold" style="color:var(--text-primary);">AI Mediation Engine</div>
        <p class="text-sm max-w-md" style="color:var(--text-secondary);">Our impartial AI reads both sides, weighs the evidence, asks clarifying questions, and reasons through the dispute with structured logic.</p>
      </div>
      <div class="reveal reveal-delay-2 card card-hover rounded-2xl p-7">
        <div class="w-10 h-10 rounded-lg border flex items-center justify-center text-xl mb-4" style="border-color:var(--border);">🔒</div>
        <div class="text-xl mb-2 font-semibold" style="color:var(--text-primary);">End-to-End Encrypted</div>
        <p class="text-sm" style="color:var(--text-secondary);">Every room is private. Evidence, messages, and reports are fully encrypted.</p>
      </div>
      <div class="reveal reveal-delay-3 card card-hover rounded-2xl p-7">
        <div class="w-10 h-10 rounded-lg border flex items-center justify-center text-xl mb-4" style="border-color:var(--border);">📄</div>
        <div class="text-xl mb-2 font-semibold" style="color:var(--text-primary);">Formal PDF Reports</div>
        <p class="text-sm" style="color:var(--text-secondary);">Structured reports with AI reasoning, findings, and recommendations — professional and shareable.</p>
      </div>
      <div class="reveal reveal-delay-1 card card-hover md:col-span-2 rounded-2xl p-7">
        <div class="w-10 h-10 rounded-lg border flex items-center justify-center text-xl mb-4" style="border-color:var(--border);">📁</div>
        <div class="text-xl mb-2 font-semibold" style="color:var(--text-primary);">Evidence Management</div>
        <p class="text-sm max-w-md mb-4" style="color:var(--text-secondary);">Upload contracts, invoices, photos, screenshots, and messages. Organise your case file and let the AI reference every document accurately.</p>
        <div class="flex flex-wrap gap-2">
          @foreach (['Contract.pdf','Invoice_03.pdf','Screenshot.png','Email.pdf'] as $f)
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-xs border" style="border-color:var(--border); color:var(--text-secondary);">📎 {{ $f }}</span>
          @endforeach
        </div>
      </div>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach ([
        ['icon'=>'⏱️','title'=>'Timeline Tracking','desc'=>'Every message, upload, and AI response is logged in an immutable timeline.'],
        ['icon'=>'📬','title'=>'Smart Notifications','desc'=>'Both parties get notified at each step — no chasing required.'],
        ['icon'=>'🧑‍⚖️','title'=>'Role-based Access','desc'=>'Each party sees only what they should see. No information leaks.'],
        ['icon'=>'📊','title'=>'Case Reporting','desc'=>'Every session ends with a clear, structured mediation report.'],
      ] as $card)
      <div class="card rounded-xl p-6">
        <div class="text-2xl mb-3">{{ $card['icon'] }}</div>
        <div class="font-semibold text-base mb-1.5" style="color:var(--text-primary);">{{ $card['title'] }}</div>
        <p class="text-xs sm:text-sm" style="color:var(--text-secondary);">{{ $card['desc'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- ================= DISPUTE TYPES ================= -->
<section id="categories" class="py-24 px-6 border-t" style="border-color:var(--border);">
  <div class="max-w-5xl mx-auto">
    <div class="reveal section-label text-xs font-semibold tracking-widest uppercase mb-4 text-gold">Dispute types</div>
    <h2 class="reveal text-4xl sm:text-5xl mb-3" style="color:var(--text-primary);">Built for everyday conflicts.</h2>
    <p class="reveal mb-14 max-w-xl" style="color:var(--text-secondary);">The disputes that are too serious to ignore, but too costly to litigate.</p>

    <div class="grid-9 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach ([
        ['icon'=>'💼','title'=>'Business','desc'=>'Supplier disputes, partnerships, contract breaches, unpaid invoices.'],
        ['icon'=>'💻','title'=>'Freelance','desc'=>'Scope changes, payment disputes, deliverable disagreements.'],
        ['icon'=>'🏢','title'=>'Employment','desc'=>'Settlement agreements, wage disputes, redundancy negotiations.'],
        ['icon'=>'🏠','title'=>'Landlord & Tenant','desc'=>'Deposit disputes, damages, early termination, lease breaches.'],
        ['icon'=>'📦','title'=>'E-commerce','desc'=>'Returns, faulty goods, seller disputes, refund denials.'],
        ['icon'=>'🤝','title'=>'Partnerships','desc'=>'Equity splits, profit sharing, co-founder disagreements.'],
        ['icon'=>'🧾','title'=>'Invoices & Payments','desc'=>'Outstanding balances, late fees, disputed work, partial payments.'],
        ['icon'=>'📱','title'=>'Digital Products','desc'=>'SaaS, subscriptions, software delivery, licensing disputes.'],
        ['icon'=>'🏗️','title'=>'Property & Contracts','desc'=>'Construction, renovation, contractor disputes, property damage.'],
      ] as $i => $cat)
      <a href="{{ route('register') }}" class="reveal reveal-delay-{{ ($i % 4) + 1 }} card card-hover rounded-xl p-6 block no-underline">
        <div class="text-3xl mb-3">{{ $cat['icon'] }}</div>
        <div class="font-semibold text-lg mb-2" style="color:var(--text-primary);">{{ $cat['title'] }}</div>
        <p class="text-sm mb-3" style="color:var(--text-secondary);">{{ $cat['desc'] }}</p>
        <div class="flex items-center gap-1.5 text-sm font-medium text-gold">
          Start a case
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
      </a>
      @endforeach
    </div>
  </div>
</section>

<!-- ================= COMPARISON ================= -->
<section class="py-24 px-6 border-t" style="border-color:var(--border); background:var(--bg-alt);">
  <div class="max-w-5xl mx-auto">
    <div class="reveal section-label text-xs font-semibold tracking-widest uppercase mb-4 text-gold">Why First Mediator</div>
    <h2 class="reveal text-4xl sm:text-5xl mb-12" style="color:var(--text-primary);">There's no fair comparison.</h2>

    <div class="reveal card rounded-2xl overflow-x-auto">
      <table class="w-full min-w-[680px] border-collapse text-left text-sm">
        <thead>
          <tr class="border-b" style="border-color:var(--border);">
            <th class="p-4 font-medium border-r" style="color:var(--text-muted); border-color:var(--border);">Feature</th>
            <th class="p-4 font-semibold text-center border-r" style="color:var(--text-secondary); border-color:var(--border);">Traditional Lawyer</th>
            <th class="p-4 font-semibold text-center border-r" style="color:var(--text-secondary); border-color:var(--border);">Arbitration</th>
            <th class="p-4 font-semibold text-center border-r" style="color:var(--text-secondary); border-color:var(--border);">WhatsApp</th>
            <th class="p-4 font-bold text-center text-gold" style="background:var(--card-bg);">First Mediator</th>
          </tr>
        </thead>
        <tbody>
          @foreach ([
            ['Speed','Weeks–months','Days–weeks','Never','Under 90 minutes'],
            ['Cost','£500–£10k+','£200–£2k','Free (no result)','Flat, transparent fee'],
            ['Structure','Formal courts','Moderately formal','None','AI-guided process'],
            ['Evidence handling','In-person bundles','Submitted manually','Screenshots','Secure uploads'],
            ['Privacy','Public record','Mostly private','None','Fully encrypted'],
            ['Accessibility','In person','Hybrid','Phone/text','100% online'],
            ['Formal report','Court judgement','Award letter','None','PDF report'],
          ] as $row)
          <tr class="border-b last:border-b-0" style="border-color:var(--border);">
            <td class="p-4 font-medium border-r" style="color:var(--text-primary); border-color:var(--border);">{{ $row[0] }}</td>
            <td class="p-4 text-center border-r" style="color:var(--text-muted); border-color:var(--border);">{{ $row[1] }}</td>
            <td class="p-4 text-center border-r" style="color:var(--text-muted); border-color:var(--border);">{{ $row[2] }}</td>
            <td class="p-4 text-center border-r" style="color:var(--text-muted); border-color:var(--border);">{{ $row[3] }}</td>
            <td class="p-4 text-center font-semibold text-gold">{{ $row[4] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- ================= SECURITY ================= -->
<section id="security" class="py-24 px-6 border-t" style="border-color:var(--border);">
  <div class="max-w-5xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
    <div class="reveal">
      <div class="section-label text-xs font-semibold tracking-widest uppercase mb-4 text-gold">Security</div>
      <h2 class="text-4xl sm:text-5xl mb-6" style="color:var(--text-primary);">Your case stays <span class="text-gold">completely private.</span></h2>
      <p class="text-base sm:text-lg mb-8" style="color:var(--text-secondary);">We've built First Mediator with privacy as the foundation, not an afterthought. Every room is isolated, every file encrypted, and every party verified.</p>
    </div>
    <div class="flex flex-col gap-4">
      @foreach ([
        ['title'=>'Encrypted by Design','desc'=>'Evidence, messages, and reports are encrypted at rest and in transit.'],
        ['title'=>'Private Rooms','desc'=>'Every case exists in an isolated room. Only invited parties can access it.'],
        ['title'=>'AI Transparency','desc'=>'First Mediator explains its reasoning step-by-step — every finding comes with a confidence score.'],
        ['title'=>'Confidentiality by Default','desc'=>'Session data is retained for 12 months then deleted, in line with our privacy policy.'],
      ] as $i => $item)
      <div class="reveal reveal-delay-{{ $i+1 }} card rounded-xl p-5 flex gap-4 items-start">
        <div class="p-2.5 rounded-lg border text-gold flex-shrink-0" style="border-color:var(--border);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 1l8 4v6c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V5l8-4z"/></svg>
        </div>
        <div>
          <div class="font-semibold text-base mb-1" style="color:var(--text-primary);">{{ $item['title'] }}</div>
          <p class="text-sm" style="color:var(--text-secondary);">{{ $item['desc'] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{--
  TESTIMONIALS SECTION INTENTIONALLY OMITTED.
  The prototype's Testimonials.tsx contained fabricated customer names, roles, and
  quotes (e.g. "Sarah Mitchell", "James Okonkwo") with stock avatar images. Publishing
  invented testimonials as genuine customer social proof would be misleading, so this
  section was dropped rather than shipped with placeholder/fake content. When real,
  verified customer quotes are available, a section can be added here reusing the same
  card/grid pattern used elsewhere on this page.
--}}

<!-- ================= PRICING (teaser — real tiers/prices are dynamic, see /pricing) ================= -->
<section id="pricing" class="py-24 px-6 border-t text-center" style="border-color:var(--border); background:var(--bg-alt);">
  <div class="max-w-2xl mx-auto">
    <div class="reveal section-label text-xs font-semibold tracking-widest uppercase mb-4 text-gold">Pricing</div>
    <h2 class="reveal text-4xl sm:text-5xl mb-4" style="color:var(--text-primary);">Subscribe and save, or pay per session.</h2>
    <p class="reveal mb-10" style="color:var(--text-secondary);">No hidden fees. Split the cost with the other party if you prefer. See current plans and per-session rates on our pricing page.</p>
    <a href="{{ route('pricing') }}" class="reveal reveal-delay-1 btn-gold inline-flex items-center gap-2 font-semibold px-8 py-3.5 rounded-lg">
      See full pricing
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </a>
  </div>
</section>

<!-- ================= FAQ (Alpine accordion) ================= -->
<section id="faq" class="py-24 px-6 border-t" style="border-color:var(--border);" x-data="{ open: 0 }">
  <div class="max-w-2xl mx-auto text-center">
    <div class="reveal section-label text-xs font-semibold tracking-widest uppercase mb-4 text-gold">FAQ</div>
    <h2 class="reveal text-4xl sm:text-5xl mb-14" style="color:var(--text-primary);">Questions people ask.</h2>
  </div>

  <div class="reveal max-w-2xl mx-auto flex flex-col gap-3 text-left">
    @foreach ([
      ['q'=>'Is the First Mediator report legally binding?','a'=>'No. The Mediation Report is not a court order and does not constitute legal advice. However, it is a timestamped, formal document that can be used as supporting evidence if your dispute proceeds to court. First Mediator is a technology tool, not a law firm.'],
      ['q'=>'What if the other party refuses to join?','a'=>'We send the other party a room invite link via email. If they decline to join, the session does not proceed and you are not charged. Their refusal to engage is itself documented — which can be relevant if the matter escalates.'],
      ['q'=>'How does First Mediator stay impartial?','a'=>'First Mediator has no relationship with either party and no financial stake in the outcome. It analyses both positions, the uploaded evidence, and the applicable legal framework for your jurisdiction. Every finding comes with a confidence score so you can see how certain the analysis is.'],
      ['q'=>'Is my case data private and secure?','a'=>'Yes. Session data is encrypted at rest and in transit and stored on secure cloud infrastructure. Session transcripts are retained for 12 months then deleted, in line with our privacy policy.'],
      ['q'=>'Can I escalate to a real lawyer after?','a'=>'Yes — this is FMRefer. At the end of any session you can escalate to a verified lawyer from our directory, filtered by jurisdiction and speciality. Your full case file (transcript, evidence, First Mediator report) is packaged and sent to the lawyer automatically. They respond within 48–72 hours.'],
    ] as $i => $faq)
    <div class="card rounded-xl overflow-hidden">
      <button type="button" @click="open = (open === {{ $i }} ? null : {{ $i }})" class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left">
        <span class="font-semibold text-lg" style="color:var(--text-primary);">{{ $faq['q'] }}</span>
        <span class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 text-lg font-light transition-transform"
              :class="open === {{ $i }} ? 'rotate-45' : ''"
              :style="open === {{ $i }} ? 'background:var(--gold-pale); color:var(--gold);' : 'background:var(--bg-alt); color:var(--text-secondary);'">+</span>
      </button>
      <div x-show="open === {{ $i }}" x-cloak
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="opacity-0 -translate-y-1"
           x-transition:enter-end="opacity-100 translate-y-0"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="px-6 pb-5 text-sm leading-relaxed" style="color:var(--text-secondary);">
        {{ $faq['a'] }}
      </div>
    </div>
    @endforeach
  </div>
</section>

<!-- ================= FINAL CTA ================= -->
<section class="py-28 px-6 border-t text-center" style="border-color:var(--border);">
  <div class="max-w-xl mx-auto">
    <div class="reveal section-label text-xs font-semibold tracking-widest uppercase mb-5 text-gold">Get started</div>
    <h2 class="reveal reveal-delay-1 text-4xl sm:text-5xl mb-6" style="color:var(--text-primary);">Your dispute deserves<br><em class="accent-serif text-gold">a proper resolution.</em></h2>
    <p class="reveal reveal-delay-2 text-lg font-light mb-10" style="color:var(--text-secondary);">Stop going back and forth. Get a room, present your case, and walk away with a formal report.</p>
    <a href="{{ route('register') }}" class="reveal reveal-delay-3 btn-gold inline-flex items-center gap-2 text-base font-semibold px-8 py-3.5 rounded-lg">
      Create a Mediation Room
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </a>
    <p class="reveal reveal-delay-4 text-sm mt-5" style="color:var(--text-muted);">
      No subscription required · Setup in 15 minutes · <a href="{{ route('pricing') }}" class="underline text-gold">See pricing</a>
    </p>
  </div>
</section>

{{-- ================= FOOTER (ported from first-mediator (2) Footer.tsx, real routes) ================= --}}
<footer class="border-t" style="border-color:var(--border); background:var(--bg-alt);">
  <div class="max-w-6xl mx-auto px-6 py-16">
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-10 mb-14">

      <!-- Brand -->
      <div class="col-span-2 sm:col-span-3 lg:col-span-1">
        <a href="{{ url('/') }}" class="flex items-center gap-2 mb-4">
          <img src="{{ asset('assets/images/logos/FM_Icon.svg') }}" alt="First Mediator" class="w-7 h-7">
          <span class="font-semibold text-base" style="color:var(--text-primary);">First Mediator</span>
        </a>
        <p class="text-sm leading-relaxed mb-5" style="color:var(--text-secondary);">AI-powered online mediation for everyday disputes.</p>
        @php
          $socialLinks = [
            'facebook'  => ['url' => \App\Models\Setting::get('social_facebook_url', ''),  'svg' => '<path d="M22 12a10 10 0 10-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.2c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0022 12z"/>'],
            'instagram' => ['url' => \App\Models\Setting::get('social_instagram_url', ''), 'svg' => '<rect x="2" y="2" width="20" height="20" rx="5" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="4.2" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="17.4" cy="6.6" r="1.1"/>'],
            'x'         => ['url' => \App\Models\Setting::get('social_x_url', ''),         'svg' => '<path d="M4 3l7 9.2L4.3 21H7l5.4-6.4L17 21h4l-7.4-9.6L20.4 3H18l-5 5.9L9 3z" fill="currentColor"/>'],
            'linkedin'  => ['url' => \App\Models\Setting::get('social_linkedin_url', ''),  'svg' => '<rect x="2" y="9" width="4" height="12" fill="currentColor"/><circle cx="4" cy="4" r="2.2" fill="currentColor"/><path d="M10 9h4v2c.7-1.2 2-2.3 4-2.3 3 0 4.5 2 4.5 5.4V21h-4v-6c0-1.6-.6-2.6-2-2.6-1.1 0-1.7.7-2 1.5-.1.3-.1.6-.1 1V21h-4z"/>'],
          ];
        @endphp
        <div class="flex gap-2.5">
          @foreach ($socialLinks as $network => $social)
            @if (!empty($social['url']))
            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ ucfirst($network) }}"
               class="w-8 h-8 rounded-lg border flex items-center justify-center card-hover" style="border-color:var(--border); color:var(--gold);">
              <svg width="15" height="15" viewBox="0 0 24 24">{!! $social['svg'] !!}</svg>
            </a>
            @endif
          @endforeach
        </div>
      </div>

      <!-- Company -->
      <div>
        <div class="font-semibold text-sm mb-4" style="color:var(--text-primary);">Company</div>
        <div class="flex flex-col gap-2.5 text-sm" style="color:var(--text-secondary);">
          <a href="{{ route('about') }}" class="hover:text-gold">About Us</a>
          <a href="mailto:info@firstmediator.com" class="hover:text-gold">Contact</a>
          <a href="{{ route('lawyers.apply') }}" class="font-semibold hover:underline text-gold">Join Lawyers Panel</a>
        </div>
      </div>

      <!-- Product -->
      <div>
        <div class="font-semibold text-sm mb-4" style="color:var(--text-primary);">Product</div>
        <div class="flex flex-col gap-2.5 text-sm" style="color:var(--text-secondary);">
          <a href="{{ url('/#how') }}" class="hover:text-gold">How it Works</a>
          <a href="{{ url('/#features') }}" class="hover:text-gold">Features</a>
          <a href="{{ url('/#pricing') }}" class="hover:text-gold">Pricing</a>
          <a href="{{ url('/#security') }}" class="hover:text-gold">Security</a>
          <a href="{{ route('register') }}" class="hover:text-gold">Create Room</a>
        </div>
      </div>

      <!-- Resources -->
      <div>
        <div class="font-semibold text-sm mb-4" style="color:var(--text-primary);">Resources</div>
        <div class="flex flex-col gap-2.5 text-sm" style="color:var(--text-secondary);">
          <a href="{{ url('/#faq') }}" class="hover:text-gold">Help Centre &amp; FAQs</a>
          <a href="{{ route('about') }}" class="hover:text-gold">Case Method</a>
          <a href="{{ route('gdpr') }}" class="hover:text-gold">Data Rights</a>
        </div>
      </div>

      <!-- Legal -->
      <div>
        <div class="font-semibold text-sm mb-4" style="color:var(--text-primary);">Legal</div>
        <div class="flex flex-col gap-2.5 text-sm" style="color:var(--text-secondary);">
          <a href="{{ route('privacy') }}" class="hover:text-gold">Privacy Policy</a>
          <a href="{{ route('terms') }}" class="hover:text-gold">Terms of Service</a>
          <a href="{{ route('privacy') }}" class="hover:text-gold">Cookie Policy</a>
          <a href="{{ route('gdpr') }}" class="hover:text-gold">GDPR Compliance</a>
          <a href="{{ route('disclaimer') }}" class="hover:text-gold">Legal Disclaimer</a>
        </div>
      </div>
    </div>

    <div id="newsletter" class="flex flex-col sm:flex-row items-center justify-between gap-4 py-8 border-t border-b mb-8" style="border-color:var(--border);">
      <div>
        <div class="font-semibold text-base mb-1" style="color:var(--text-primary);">Stay in the loop</div>
        <div class="text-sm" style="color:var(--text-secondary);">Updates on features, legal-tech insights, and dispute resolution tips.</div>
      </div>

      <form method="POST" action="{{ route('newsletter.subscribe') }}" class="flex gap-2 w-full sm:w-auto">
        @csrf
        <input type="email" name="email" required placeholder="Enter your email"
               class="px-4 py-2.5 rounded-md border text-sm outline-none flex-1 sm:w-64"
               style="border-color:var(--border); background:var(--bg); color:var(--text-primary);">
        <button type="submit" class="btn-gold px-5 py-2.5 rounded-md text-sm font-semibold whitespace-nowrap">
          {{ session('subscribed') ? 'Subscribed ✓' : 'Subscribe' }}
        </button>
      </form>
    </div>

    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-8 border-t text-xs" style="border-color:var(--border); color:var(--text-muted);">
      <span>&copy; {{ date('Y') }} First Mediator Ltd. All rights reserved.</span>
      <div class="flex gap-4">
        <a href="{{ route('terms') }}" class="hover:underline">Terms</a>
        <a href="{{ route('privacy') }}" class="hover:underline">Privacy</a>
        <a href="{{ route('disclaimer') }}" class="hover:underline">Disclaimer</a>
      </div>
    </div>
  </div>
</footer>

{{-- ================= COOKIE CONSENT (ported from first-mediator (2) CookieConsentBanner.tsx) ================= --}}
<div
  x-data="{
    visible: false,
    showPreferences: false,
    analyticsConsent: true,
    init() {
      if (!localStorage.getItem('first_mediator_cookie_consent')) {
        setTimeout(() => { this.visible = true }, 1000);
      }
    },
    save(analytics) {
      localStorage.setItem('first_mediator_cookie_consent', JSON.stringify({
        essential: true, analytics: analytics, timestamp: new Date().toISOString()
      }));
      this.visible = false;
    }
  }"
  x-show="visible"
  x-cloak
  x-transition:enter="transition ease-out duration-300"
  x-transition:enter-start="opacity-0 translate-y-4"
  x-transition:enter-end="opacity-100 translate-y-0"
  class="fixed bottom-4 left-4 right-4 md:left-auto md:right-6 md:max-w-md z-50"
>
  <div class="card rounded-xl p-5 relative" style="box-shadow:var(--shadow-lg);">
    <button type="button" @click="save(false)" aria-label="Close and accept essential only"
            class="absolute top-3 right-3 p-1" style="color:var(--text-muted);">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
    </button>

    <div class="flex items-start gap-3 mb-3 pr-6">
      <div class="p-2 rounded-lg flex-shrink-0 mt-0.5" style="background:var(--gold-pale); color:var(--gold);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><circle cx="9" cy="9" r=".5" fill="currentColor"/><circle cx="14" cy="8.5" r=".5" fill="currentColor"/><circle cx="15" cy="13" r=".5" fill="currentColor"/><circle cx="9.5" cy="14" r=".5" fill="currentColor"/></svg>
      </div>
      <div>
        <h4 class="font-semibold text-sm" style="color:var(--text-primary);">Cookie &amp; Privacy Notice</h4>
        <p class="text-xs mt-1 leading-relaxed" style="color:var(--text-secondary);">We use essential cookies to secure your mediation sessions and maintain room state. We also use analytics cookies to help us improve the platform.</p>
      </div>
    </div>

    <template x-if="showPreferences">
      <div class="my-3 p-3 rounded-lg space-y-2 text-xs border" style="background:var(--bg-alt); border-color:var(--border);">
        <div class="flex items-center justify-between pb-2 border-b" style="border-color:var(--border);">
          <div>
            <span class="font-semibold block" style="color:var(--text-primary);">Essential Session Cookies</span>
            <span style="color:var(--text-muted);">Required for room encryption and auth.</span>
          </div>
          <span class="font-semibold px-2 py-0.5 rounded" style="color:#22C55E; background:rgba(34,197,94,.1);">Always Active</span>
        </div>
        <div class="flex items-center justify-between pt-1">
          <div>
            <span class="font-semibold block" style="color:var(--text-primary);">Analytics &amp; Performance</span>
            <span style="color:var(--text-muted);">Helps us optimise resolution speeds.</span>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" x-model="analyticsConsent" class="sr-only peer">
            <div class="w-9 h-5 rounded-full peer transition-colors" :style="analyticsConsent ? 'background:var(--gold)' : 'background:var(--border)'">
              <div class="bg-white rounded-full h-4 w-4 mt-0.5 transition-transform" :style="analyticsConsent ? 'transform:translateX(18px)' : 'transform:translateX(2px)'"></div>
            </div>
          </label>
        </div>
        <div class="pt-2">
          <button type="button" @click="save(analyticsConsent)" class="btn-gold w-full py-1.5 text-xs font-bold rounded">Save Preferences</button>
        </div>
      </div>
    </template>

    <template x-if="!showPreferences">
      <div class="flex items-center gap-2 text-xs mb-4" style="color:var(--text-muted);">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2"><path d="M12 1l8 4v6c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V5l8-4z"/></svg>
        <span>Your choice, your control</span>
        <span>&middot;</span>
        <a href="{{ route('privacy') }}" class="hover:underline text-gold">Cookie Policy</a>
        <span>&middot;</span>
        <a href="{{ route('privacy') }}" class="hover:underline text-gold">Privacy Policy</a>
      </div>
    </template>

    <div class="flex flex-wrap items-center gap-2" x-show="!showPreferences">
      <button type="button" @click="save(true)" class="btn-gold flex-1 py-2 px-3 text-xs font-bold rounded">Accept All</button>
      <button type="button" @click="save(false)" class="btn-outline py-2 px-3 text-xs rounded">Essential Only</button>
      <button type="button" @click="showPreferences = true" class="py-2 px-2 text-xs underline" style="color:var(--text-muted);">Customize</button>
    </div>
  </div>
</div>

<script>
// Scroll reveal — same IntersectionObserver mechanism used across the site
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
</script>
</body>
</html>
