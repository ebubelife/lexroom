<!-- NAV -->
<nav>
  <div class="nav-inner">
    <a href="{{ url('/') }}" class="logo">
      <img 
        src="{{ asset('assets/images/logos/fm-darkmode.png') }}" 
        alt="FirstMediator" 
        class="logo-text logo-dark"
        style="height: 110px; width: auto; display: var(--logo-dark-display) !important;"
      >
      <img 
        src="{{ asset('assets/images/logos/fm-lightmode.png') }}" 
        alt="First Mediator" 
        class="logo-text logo-light"
        style="height: 110px; width: auto; display: var(--logo-light-display) !important;"
      >
    </a>
    <ul class="nav-links">
      <li><a href="{{ url('/#features') }}">Product</a></li>
      <li><a href="{{ url('/#how') }}">How it Works</a></li>
      <li><a href="{{ url('/#pricing') }}">Pricing</a></li>
      <li><a href="{{ url('/#faq') }}">FAQs</a></li>
      <li><a href="{{ route('about') }}">About</a></li>
      <li><a href="{{ route('lawyers.apply') }}" class="nav-link-lawyers">Lawyers Panel</a></li>
    </ul>
    <div class="nav-right">
      <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
        <span id="theme-icon">🌙</span>
      </button>
      @auth
        <a href="{{ route('dashboard') }}" class="btn-primary">Dashboard</a>
      @else
        <a href="{{ route('login') }}" class="btn-ghost">Log in</a>
        <a href="{{ route('register') }}" class="btn-primary">Create a Room</a>
      @endauth
      
      <button class="mobile-menu-btn" onclick="toggleMobileMenu()" aria-label="Toggle menu">
        <svg id="menu-icon-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
        <svg id="menu-icon-close" class="hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
  </div>
  
  <!-- Mobile Nav Dropdown -->
  <div id="mobile-nav" class="mobile-nav">
    <ul style="list-style: none; display: flex; flex-direction: column; gap: 16px; padding: 8px 0;">
      <li><a href="{{ url('/#features') }}" style="text-decoration: none; color: var(--text-secondary); font-size: 15px;" onclick="toggleMobileMenu()">Product</a></li>
      <li><a href="{{ url('/#how') }}" style="text-decoration: none; color: var(--text-secondary); font-size: 15px;" onclick="toggleMobileMenu()">How it Works</a></li>
      <li><a href="{{ url('/#pricing') }}" style="text-decoration: none; color: var(--text-secondary); font-size: 15px;" onclick="toggleMobileMenu()">Pricing</a></li>
      <li><a href="{{ url('/#faq') }}" style="text-decoration: none; color: var(--text-secondary); font-size: 15px;" onclick="toggleMobileMenu()">FAQs</a></li>
      <li><a href="{{ route('about') }}" style="text-decoration: none; color: var(--text-secondary); font-size: 15px;" onclick="toggleMobileMenu()">About</a></li>
      <li><a href="{{ route('lawyers.apply') }}" style="text-decoration: none; color: var(--gold); font-weight: 600; font-size: 15px;" onclick="toggleMobileMenu()">Lawyers Panel</a></li>
    </ul>

    <div style="height: 1px; background: var(--border); margin: 8px 0;"></div>

    @auth
      <a href="{{ route('dashboard') }}" class="btn-primary" onclick="toggleMobileMenu()">Dashboard</a>
    @else
      <a href="{{ route('login') }}" class="btn-ghost" onclick="toggleMobileMenu()">Log in</a>
      <a href="{{ route('register') }}" class="btn-primary" onclick="toggleMobileMenu()">Create a Room</a>
    @endauth
  </div>
</nav>
