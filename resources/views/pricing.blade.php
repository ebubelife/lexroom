@extends('layouts.app')

@section('title', 'Pricing — First Mediator')
@section('page-title', 'Pricing')

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ cycle: 'monthly' }">

    {{-- Header --}}
    <div class="text-center mb-10">
        <h1 class="text-3xl lg:text-4xl font-serif mb-3" style="color: var(--text-primary);">
            Simple, transparent pricing
        </h1>
        <p class="text-base mb-6" style="color: var(--text-secondary);">
            Subscribe and save, or pay per session — no hidden fees.
        </p>

        {{-- Billing cycle toggle --}}
        <div class="inline-flex rounded-xl p-1 gap-1" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
            @foreach(['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'yearly' => 'Yearly'] as $key => $label)
                <button @click="cycle = '{{ $key }}'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
                        :style="cycle === '{{ $key }}'
                            ? 'background: var(--gold); color: #0D1B2A;'
                            : 'color: var(--text-secondary);'">
                    {{ $label }}
                    @if($key !== 'monthly')
                        <span class="ml-1 text-xs opacity-70">
                            {{ $key === 'quarterly' ? 'Save ~11%' : 'Save ~20%' }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- Plan cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-14">
        @foreach($plans as $plan)
            @php $isPopular = $plan->slug === 'standard'; @endphp
            <div class="rounded-2xl p-6 flex flex-col relative {{ $isPopular ? 'ring-2' : '' }}"
                 style="{{ $isPopular
                     ? 'background: var(--bg-secondary); border: 1px solid var(--gold); ring-color: var(--gold);'
                     : 'background: var(--bg-secondary); border: 1px solid var(--border-color);' }}">

                @if($isPopular)
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                              style="background: var(--gold); color: #0D1B2A;">Most Popular</span>
                    </div>
                @endif

                <div class="mb-4">
                    <h2 class="text-xl font-serif font-bold mb-1" style="color: var(--text-primary);">{{ $plan->name }}</h2>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ $plan->description }}</p>
                </div>

                {{-- Price --}}
                <div class="mb-6">
                    <div x-show="cycle === 'monthly'">
                        <span class="text-4xl font-bold" style="color: var(--gold);">£{{ number_format($plan->price_monthly, 0) }}</span>
                        <span class="text-sm" style="color: var(--text-secondary);">/month</span>
                    </div>
                    <div x-show="cycle === 'quarterly'" x-cloak>
                        <span class="text-4xl font-bold" style="color: var(--gold);">£{{ number_format($plan->price_quarterly, 0) }}</span>
                        <span class="text-sm" style="color: var(--text-secondary);">/quarter</span>
                        <p class="text-xs mt-1" style="color: #4ADE80;">Save £{{ number_format(($plan->price_monthly * 3) - $plan->price_quarterly, 0) }}</p>
                    </div>
                    <div x-show="cycle === 'yearly'" x-cloak>
                        <span class="text-4xl font-bold" style="color: var(--gold);">£{{ number_format($plan->price_yearly, 0) }}</span>
                        <span class="text-sm" style="color: var(--text-secondary);">/year</span>
                        <p class="text-xs mt-1" style="color: #4ADE80;">Save £{{ number_format(($plan->price_monthly * 12) - $plan->price_yearly, 0) }}</p>
                    </div>
                    <p class="text-xs mt-2" style="color: var(--text-secondary);">
                        £{{ number_format($plan->credits_per_cycle, 0) }} session credits per month
                    </p>
                </div>

                {{-- Features --}}
                <ul class="space-y-2 mb-8 flex-1">
                    @foreach($plan->features ?? [] as $feature)
                        <li class="flex items-start gap-2 text-sm" style="color: var(--text-primary);">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color: var(--gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>

                {{-- CTA --}}
                @auth
                    @if($current && $current->plan->slug === $plan->slug)
                        <div class="w-full py-3 rounded-xl text-center text-sm font-semibold"
                             style="background: rgba(201,168,76,0.12); color: var(--gold); border: 1px solid rgba(201,168,76,0.3);">
                            Current Plan ✓
                        </div>
                    @else
                        <form method="GET" action="{{ route('subscription.checkout') }}">
                            <input type="hidden" name="plan_slug" value="{{ $plan->slug }}">
                            <input type="hidden" name="cycle" x-bind:value="cycle">
                            <button type="submit"
                                    class="w-full py-3 rounded-xl text-sm font-bold uppercase tracking-wider hover:opacity-90 transition-opacity"
                                    style="{{ $isPopular
                                        ? 'background: var(--gold); color: #0D1B2A;'
                                        : 'background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);' }}">
                                Subscribe Now
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('register') }}"
                       class="w-full py-3 rounded-xl text-sm font-bold uppercase tracking-wider text-center hover:opacity-90 transition-opacity block"
                       style="{{ $isPopular
                           ? 'background: var(--gold); color: #0D1B2A;'
                           : 'background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);' }}">
                        Get Started
                    </a>
                @endauth
            </div>
        @endforeach
    </div>

    {{-- Pay per session --}}
    <div class="rounded-2xl p-6 mb-14 text-center" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
        <h3 class="text-lg font-serif mb-2" style="color: var(--text-primary);">Just need one session?</h3>
        <p class="text-sm mb-4" style="color: var(--text-secondary);">
            Pay per session with no commitment. Starter from <strong style="color: var(--gold);">£3.50</strong>,
            Standard <strong style="color: var(--gold);">£6.00</strong>,
            Extended <strong style="color: var(--gold);">£8.00</strong>.
        </p>
        <a href="{{ route('rooms.create') }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold hover:opacity-90 transition-opacity"
           style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
            Create a Room →
        </a>
    </div>

    {{-- Top-up packages --}}
    @if($topups->isNotEmpty())
    <div class="mb-10">
        <h2 class="text-2xl font-serif text-center mb-2" style="color: var(--text-primary);">Top-up Credits</h2>
        <p class="text-sm text-center mb-6" style="color: var(--text-secondary);">Already subscribed? Add extra credits anytime.</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($topups as $pkg)
                <div class="rounded-xl p-4 text-center" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
                    <p class="text-sm font-semibold mb-1" style="color: var(--text-primary);">{{ $pkg->label }}</p>
                    <p class="text-2xl font-bold mb-1" style="color: var(--gold);">£{{ number_format($pkg->price, 0) }}</p>
                    <p class="text-xs mb-3" style="color: var(--text-secondary);">
                        £{{ number_format($pkg->credits, 0) }} credits
                        @if($pkg->bonus_credits > 0)
                            <span style="color: #4ADE80;">+£{{ number_format($pkg->bonus_credits, 0) }} bonus</span>
                        @endif
                    </p>
                    @auth
                        <form method="GET" action="{{ route('subscription.topup') }}">
                            <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                            <button type="submit"
                                    class="w-full py-2 rounded-lg text-xs font-bold hover:opacity-80"
                                    style="background: rgba(201,168,76,0.12); color: var(--gold); border: 1px solid rgba(201,168,76,0.3);">
                                Buy
                            </button>
                        </form>
                    @else
                        <a href="{{ route('register') }}"
                           class="block w-full py-2 rounded-lg text-xs font-bold hover:opacity-80"
                           style="background: rgba(201,168,76,0.12); color: var(--gold); border: 1px solid rgba(201,168,76,0.3);">
                            Sign up
                        </a>
                    @endauth
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
