@extends('admin.layouts.app')
@section('title', 'Credit Settings')
@section('page-title', 'Credit Settings')

@section('content')
<div class="space-y-5">

    {{-- Credit Settings --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="px-5 py-3" style="border-bottom: 1px solid var(--border-color);">
            <h2 class="text-sm font-semibold">Credit Settings</h2>
        </div>
        <form method="POST" action="{{ route('admin.credits.update') }}" class="p-5 space-y-4">
            @csrf @method('PUT')

            <div class="flex items-center justify-between py-3" style="border-bottom: 1px solid var(--border-color);">
                <div>
                    <p class="text-sm font-medium">Expire credits on renewal</p>
                    <p class="text-xs" style="color: var(--text-secondary);">Reset balance to plan credits each billing cycle</p>
                </div>
                <label style="display:inline-block; cursor:pointer;">
                    @php $isOn = ($settings->get('credits_expire_on_renewal')?->value ?? 'true') === 'true'; @endphp
                    <input type="checkbox" name="credits_expire_on_renewal" value="1"
                           {{ $isOn ? 'checked' : '' }}
                           onchange="this.nextElementSibling.style.background=this.checked?'#C9A84C':'#1e2f42'; this.nextElementSibling.children[0].style.left=this.checked?'22px':'2px';"
                           style="position:absolute;opacity:0;width:0;height:0;">
                    <div style="width:44px;height:24px;border-radius:9999px;position:relative;transition:background 0.2s;background:{{ $isOn ? '#C9A84C' : '#1e2f42' }};border:1px solid #2a3f55;">
                        <div style="position:absolute;top:2px;left:{{ $isOn ? '22px' : '2px' }};width:18px;height:18px;background:white;border-radius:9999px;transition:left 0.2s;box-shadow:0 1px 4px rgba(0,0,0,0.4);"></div>
                    </div>
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach([
                    ['credits_to_minutes_rate', 'Minutes per £1 credit', 'e.g. 4'],
                    ['referral_reward_credits', 'Referral reward (£)', 'e.g. 2.00'],
                    ['gbp_to_usd_rate', 'GBP → USD rate', 'e.g. 1.27'],
                    ['gbp_to_eur_rate', 'GBP → EUR rate', 'e.g. 1.17'],
                ] as [$key, $label, $placeholder])
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">{{ $label }}</label>
                        <input type="text" name="{{ $key }}"
                               value="{{ $settings->get($key)?->value ?? '' }}"
                               placeholder="{{ $placeholder }}"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                @endforeach
            </div>

            <button type="submit"
                    class="px-5 py-2 rounded-lg text-sm font-medium hover:opacity-80"
                    style="background: var(--gold); color: #0D1B2A;">
                Save Settings
            </button>
        </form>
    </div>

    {{-- Top-up Packages --}}
    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <div class="flex items-center justify-between px-5 py-3" style="border-bottom: 1px solid var(--border-color);">
            <h2 class="text-sm font-semibold">Top-up Packages</h2>
        </div>

        @foreach($topups as $pkg)
        <div x-data="{ editing: false }" class="{{ !$pkg->is_active ? 'opacity-50' : '' }}"
             style="border-bottom: 1px solid var(--border-color);">
            <div class="flex items-center justify-between px-5 py-3">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium">{{ $pkg->label }}</span>
                    <span class="text-sm" style="color: var(--gold);">£{{ number_format($pkg->price, 2) }}</span>
                    <span class="text-xs" style="color: var(--text-secondary);">
                        £{{ number_format($pkg->credits, 2) }} credits
                        @if($pkg->bonus_credits > 0) +£{{ number_format($pkg->bonus_credits, 2) }} bonus @endif
                    </span>
                    <span class="text-xs font-mono {{ str_starts_with($pkg->stripe_price_id ?? '', 'STRIPE_') ? 'text-yellow-400' : 'text-green-400' }}">
                        {{ $pkg->stripe_price_id ?? '—' }}
                    </span>
                </div>
                <div class="flex gap-2">
                    <button @click="editing = !editing"
                            class="px-2.5 py-1 rounded text-xs hover:opacity-80"
                            style="background: rgba(59,130,246,0.1); color: #60A5FA;">Edit</button>
                    <form method="POST" action="{{ route('admin.credits.topup.toggle', $pkg) }}">
                        @csrf
                        <button type="submit" class="px-2.5 py-1 rounded text-xs hover:opacity-80"
                                style="{{ $pkg->is_active ? 'background:rgba(239,68,68,0.1);color:#F87171;' : 'background:rgba(74,222,128,0.1);color:#4ADE80;' }}">
                            {{ $pkg->is_active ? 'Off' : 'On' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.credits.topup.destroy', $pkg) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-2.5 py-1 rounded text-xs hover:opacity-80"
                                style="background:rgba(239,68,68,0.1);color:#F87171;"
                                onclick="return confirm('Delete {{ addslashes($pkg->label) }}?')">Del</button>
                    </form>
                </div>
            </div>
            <div x-show="editing" x-cloak class="px-5 pb-4">
                <form method="POST" action="{{ route('admin.credits.topup.update', $pkg) }}"
                      class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    @csrf @method('PUT')
                    @foreach([['label','Label',$pkg->label],['credits','Credits (£)',$pkg->credits],['price','Price (£)',$pkg->price],['bonus_credits','Bonus (£)',$pkg->bonus_credits],['stripe_price_id','Stripe Price ID',$pkg->stripe_price_id]] as [$n,$l,$v])
                        <div>
                            <label class="block text-xs mb-1" style="color:var(--text-secondary);">{{ $l }}</label>
                            <input type="text" name="{{ $n }}" value="{{ $v }}"
                                   class="w-full px-2 py-1.5 rounded text-xs outline-none"
                                   style="background:var(--bg-primary);border:1px solid var(--border-color);color:var(--text-primary);">
                        </div>
                    @endforeach
                    <div class="col-span-2 sm:col-span-5 flex gap-2">
                        <button type="submit" class="px-3 py-1.5 rounded text-xs font-medium hover:opacity-80"
                                style="background:var(--gold);color:#0D1B2A;">Save</button>
                        <button type="button" @click="editing=false" class="px-3 py-1.5 rounded text-xs"
                                style="background:var(--bg-primary);border:1px solid var(--border-color);color:var(--text-secondary);">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach

        {{-- Add new --}}
        <div x-data="{ adding: false }" class="p-5">
            <button @click="adding = !adding"
                    class="text-sm font-medium hover:opacity-80" style="color: var(--gold);">
                + Add Package
            </button>
            <form x-show="adding" x-cloak method="POST" action="{{ route('admin.credits.topup.store') }}"
                  class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-3">
                @csrf
                @foreach([['label','Label','Medium'],['credits','Credits (£)','10'],['price','Price (£)','10'],['bonus_credits','Bonus (£)','0'],['stripe_price_id','Stripe Price ID','']] as [$n,$l,$ph])
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--text-secondary);">{{ $l }}</label>
                        <input type="text" name="{{ $n }}" placeholder="{{ $ph }}"
                               class="w-full px-2 py-1.5 rounded text-xs outline-none"
                               style="background:var(--bg-primary);border:1px solid var(--border-color);color:var(--text-primary);">
                    </div>
                @endforeach
                <div class="col-span-2 sm:col-span-5 flex gap-2">
                    <button type="submit" class="px-3 py-1.5 rounded text-xs font-medium hover:opacity-80"
                            style="background:var(--gold);color:#0D1B2A;">Add</button>
                    <button type="button" @click="adding=false" class="px-3 py-1.5 rounded text-xs"
                            style="background:var(--bg-primary);border:1px solid var(--border-color);color:var(--text-secondary);">Cancel</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
