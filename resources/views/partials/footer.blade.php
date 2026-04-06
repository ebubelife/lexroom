<!-- FOOTER -->
<footer>
  <div class="footer-inner">
    <div class="footer-grid">
      <!-- Col 1: Logo -->
      <div class="footer-col">
        <a href="/" class="logo">
          <img 
            src="{{ asset('assets/images/logos/fm-darkmode.png') }}" 
            alt="FirstMediator" 
            class="logo-text logo-dark"
            style="height: 72px; display: var(--logo-dark-display) !important;"
          >
          <img 
            src="{{ asset('assets/images/logos/fm-lightmode.png') }}" 
            alt="First Mediator" 
            class="logo-text logo-light"
            style="height: 72px; display: var(--logo-light-display) !important;"
          >
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
