@extends('admin.layouts.app')
@section('title', 'Subscription Plans')
@section('page-title', 'Subscription Plans')

@section('content')
<div x-data="{ addModal: false, editId: null }" class="space-y-4">

    <div class="flex justify-end">
        <button @click="addModal = true"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90"
                style="background: var(--gold); color: #0D1B2A;">
            + Add Plan
        </button>
    </div>

    @foreach($plans as $plan)
    <div x-data="{ editing: false }" class="rounded-xl overflow-hidden"
         style="background: var(--bg-card); border: 1px solid var(--border-color); {{ !$plan->is_active ? 'opacity:0.6;' : '' }}">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid var(--border-color);">
            <div class="flex items-center gap-3">
                <h3 class="text-base font-semibold">{{ $plan->name }}</h3>
                <span class="text-xs font-mono px-2 py-0.5 rounded" style="background: rgba(201,168,76,0.12); color: var(--gold);">{{ $plan->slug }}</span>
                @if(!$plan->is_active)
                    <span class="badge" style="background: rgba(239,68,68,0.12); color: #F87171;">Disabled</span>
                @endif
            </div>
            <div class="flex gap-2">
                <button @click="editing = !editing"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium hover:opacity-80"
                        style="background: rgba(59,130,246,0.1); color: #60A5FA; border: 1px solid rgba(59,130,246,0.2);">
                    Edit
                </button>
                <form method="POST" action="{{ route('admin.plans.toggle', $plan) }}">
                    @csrf
                    <button type="submit"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium hover:opacity-80"
                            style="{{ $plan->is_active
                                ? 'background: rgba(239,68,68,0.1); color: #F87171; border: 1px solid rgba(239,68,68,0.2);'
                                : 'background: rgba(74,222,128,0.1); color: #4ADE80; border: 1px solid rgba(74,222,128,0.2);' }}">
                        {{ $plan->is_active ? 'Disable' : 'Enable' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Summary --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 px-5 py-4">
            @foreach([
                ['Credits/cycle', '£' . number_format($plan->credits_per_cycle, 2)],
                ['Monthly',       '£' . number_format($plan->price_monthly, 2)],
                ['Quarterly',     '£' . number_format($plan->price_quarterly, 2)],
                ['Yearly',        '£' . number_format($plan->price_yearly, 2)],
            ] as [$label, $val])
                <div>
                    <p class="text-xs uppercase tracking-wider mb-0.5" style="color: var(--text-secondary);">{{ $label }}</p>
                    <p class="text-sm font-semibold" style="color: var(--gold);">{{ $val }}</p>
                </div>
            @endforeach
        </div>

        {{-- Stripe IDs --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 px-5 pb-4">
            @foreach([
                ['Monthly Price ID',    $plan->stripe_monthly_price_id],
                ['Quarterly Price ID',  $plan->stripe_quarterly_price_id],
                ['Yearly Price ID',     $plan->stripe_yearly_price_id],
            ] as [$label, $val])
                <div>
                    <p class="text-xs mb-0.5" style="color: var(--text-secondary);">{{ $label }}</p>
                    <p class="text-xs font-mono truncate {{ str_starts_with($val ?? '', 'STRIPE_') ? 'text-yellow-400' : 'text-green-400' }}">
                        {{ $val ?? '—' }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Edit form --}}
        <div x-show="editing" x-cloak style="border-top: 1px solid var(--border-color);">
            <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="p-5 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Name</label>
                        <input type="text" name="name" value="{{ $plan->name }}" required
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Credits per cycle (£)</label>
                        <input type="number" name="credits_per_cycle" value="{{ $plan->credits_per_cycle }}" step="0.01" required
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Monthly price (£)</label>
                        <input type="number" name="price_monthly" value="{{ $plan->price_monthly }}" step="0.01" required
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Quarterly price (£)</label>
                        <input type="number" name="price_quarterly" value="{{ $plan->price_quarterly }}" step="0.01" required
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Yearly price (£)</label>
                        <input type="number" name="price_yearly" value="{{ $plan->price_yearly }}" step="0.01" required
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Description</label>
                        <input type="text" name="description" value="{{ $plan->description }}"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Stripe Monthly Price ID</label>
                        <input type="text" name="stripe_monthly_price_id" value="{{ $plan->stripe_monthly_price_id }}"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none font-mono"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Stripe Quarterly Price ID</label>
                        <input type="text" name="stripe_quarterly_price_id" value="{{ $plan->stripe_quarterly_price_id }}"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none font-mono"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Stripe Yearly Price ID</label>
                        <input type="text" name="stripe_yearly_price_id" value="{{ $plan->stripe_yearly_price_id }}"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none font-mono"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Features (one per line)</label>
                        <textarea name="features" rows="4"
                                  class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                                  style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">{{ implode("\n", $plan->features ?? []) }}</textarea>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="editing = false"
                            class="px-4 py-2 rounded-lg text-sm font-medium"
                            style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-secondary);">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-medium hover:opacity-80"
                            style="background: var(--gold); color: #0D1B2A;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    {{-- Add Plan Modal --}}
    <div x-show="addModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         style="background: rgba(0,0,0,0.8); backdrop-filter: blur(4px);">
        <div class="w-full max-w-2xl rounded-2xl p-6 space-y-6 my-8"
             style="background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);"
             @click.away="addModal = false">
            
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-white">Create New Subscription Plan</h3>
                <button @click="addModal = false" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.plans.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="e.g. Pro Plan"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Slug <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" required placeholder="e.g. pro-plan"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none font-mono"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Credits per cycle (£) <span class="text-red-500">*</span></label>
                        <input type="number" name="credits_per_cycle" step="0.01" required placeholder="0.00"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Monthly price (£) <span class="text-red-500">*</span></label>
                        <input type="number" name="price_monthly" step="0.01" required placeholder="0.00"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Quarterly price (£) <span class="text-red-500">*</span></label>
                        <input type="number" name="price_quarterly" step="0.01" required placeholder="0.00"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Yearly price (£) <span class="text-red-500">*</span></label>
                        <input type="number" name="price_yearly" step="0.01" required placeholder="0.00"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Description</label>
                        <input type="text" name="description" placeholder="Brief description of the plan"
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Stripe Monthly Price ID</label>
                        <input type="text" name="stripe_monthly_price_id" placeholder="price_..."
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none font-mono"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Stripe Quarterly Price ID</label>
                        <input type="text" name="stripe_quarterly_price_id" placeholder="price_..."
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none font-mono"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Stripe Yearly Price ID</label>
                        <input type="text" name="stripe_yearly_price_id" placeholder="price_..."
                               class="w-full px-3 py-2 rounded-lg text-sm outline-none font-mono"
                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Features (one per line)</label>
                        <textarea name="features" rows="4" placeholder="Feature 1&#10;Feature 2&#10;Feature 3"
                                  class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                                  style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="addModal = false"
                            class="flex-1 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors"
                            style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-secondary);">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 rounded-lg text-sm font-medium hover:opacity-90 transition-opacity"
                            style="background: var(--gold); color: #0D1B2A;">
                        Create Plan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
