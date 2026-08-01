<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Join the Lawyers Panel — First Mediator</title>
<meta name="description" content="Apply to join the First Mediator lawyer referral panel and receive escalated cases in your jurisdiction and speciality.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/shared-layout.css') }}">
<script src="{{ asset('js/shared-layout.js') }}"></script>

<!-- Tailwind (compiled) -- required for the shared footer's utility classes -->
@vite(['resources/css/app.css'])

<style>
[data-theme="dark"] { --bg-alt: #0F2336; }

.page-container { max-width: 640px; margin: 120px auto 100px; padding: 0 24px; }
h1 {
  font-family: var(--serif);
  font-size: clamp(40px, 8vw, 56px);
  font-weight: 600;
  margin-bottom: 16px;
  line-height: 1.1;
  color: var(--text-primary);
}
.lede { color: var(--text-secondary); font-size: 17px; line-height: 1.6; margin-bottom: 40px; }

.field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
@media (max-width: 640px) { .field-grid { grid-template-columns: 1fr; } }

label { font-size: 13px; color: var(--text-secondary); display: block; margin-bottom: 6px; }
input, select, textarea {
  width: 100%; padding: 10px 14px; border-radius: 10px; font-size: 14px;
  background: var(--bg-alt); border: 1px solid var(--border); color: var(--text-primary);
  font-family: var(--sans);
}
textarea { resize: vertical; }
.field { margin-bottom: 16px; }

.btn-submit {
  font-family: var(--sans); font-weight: 600; font-size: 15px;
  background: var(--gold); color: var(--navy);
  padding: 13px 28px; border-radius: 10px; border: none; cursor: pointer;
  transition: background .2s;
}
.btn-submit:hover { background: var(--gold-light); }

.alert-success {
  background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.3); color: #16a34a;
  padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 14px;
}
.alert-errors {
  background: rgba(220,38,38,.08); border: 1px solid rgba(220,38,38,.25); color: #DC2626;
  padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 14px;
}
.alert-errors ul { margin: 0; padding-left: 18px; }
</style>
</head>
<body>

@include('partials.navbar')

<div class="page-container">
  <h1>Join the Lawyers Panel</h1>
  <p class="lede">When a mediation case can't be resolved online, we escalate it to a verified lawyer in our directory. Apply below to be considered — our team reviews every application before it goes live.</p>

  @if (session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert-errors">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('lawyers.apply.store') }}">
    @csrf

    <div class="field-grid">
      <div class="field">
        <label for="name">Full Name *</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
      </div>
      <div class="field">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
      </div>
    </div>

    <div class="field-grid">
      <div class="field">
        <label for="phone">Phone *</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required>
      </div>
      <div class="field">
        <label for="bar_number">Bar Number</label>
        <input type="text" id="bar_number" name="bar_number" value="{{ old('bar_number') }}">
      </div>
    </div>

    <div class="field-grid">
      <div class="field">
        <label for="jurisdiction">Jurisdiction *</label>
        <select id="jurisdiction" name="jurisdiction" required>
          @foreach ($jurisdictions as $j)
            <option value="{{ $j }}" {{ old('jurisdiction') === $j ? 'selected' : '' }}>{{ $j }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="speciality">Speciality *</label>
        <select id="speciality" name="speciality" required>
          @foreach ($specialities as $s)
            <option value="{{ $s }}" {{ old('speciality') === $s ? 'selected' : '' }}>{{ $s }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="field">
      <label for="years_experience">Years of Experience *</label>
      <input type="number" id="years_experience" name="years_experience" min="0" value="{{ old('years_experience', 0) }}" required>
    </div>

    <div class="field">
      <label for="bio">Short Bio</label>
      <textarea id="bio" name="bio" rows="4" maxlength="500">{{ old('bio') }}</textarea>
    </div>

    <button type="submit" class="btn-submit">Submit Application</button>
  </form>
</div>

@include('partials.footer')

</body>
</html>
