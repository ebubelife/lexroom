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
