@extends('admin.layouts.app')

@section('title', 'Jurisdictions')
@section('page-title', 'Jurisdictions')

@section('content')
<div x-data="{ addModal: false }" class="space-y-4">

    {{-- Toolbar --}}
    <div class="flex items-center justify-between">
        <p class="text-sm" style="color: var(--text-secondary);">
            {{ collect($jurisdictions)->flatten()->count() }} jurisdictions across
            {{ $jurisdictions->count() }} regions
            · <span style="color: #4ADE80;">{{ collect($jurisdictions)->flatten()->where('is_active', true)->count() }} active</span>
            · <span style="color: #F87171;">{{ collect($jurisdictions)->flatten()->where('is_active', false)->count() }} disabled</span>
        </p>
        <button @click="addModal = true"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90"
                style="background: var(--gold); color: #0D1B2A;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Jurisdiction
        </button>
    </div>

    {{-- Regions --}}
    @foreach($jurisdictions as $region => $items)
        @php $firstItem = $items->first(); @endphp
        <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">

            {{-- Region header --}}
            <div class="flex items-center justify-between px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
                <div class="flex items-center gap-2">
                    <span class="text-lg">{{ $firstItem->region_flag }}</span>
                    <h3 class="text-sm font-semibold">{{ $region }}</h3>
                    <span class="text-xs px-2 py-0.5 rounded-full" style="background: rgba(201,168,76,0.12); color: var(--gold);">
                        {{ $items->count() }}
                    </span>
                </div>
                <div class="flex gap-2 text-xs" style="color: var(--text-secondary);">
                    <span style="color: #4ADE80;">{{ $items->where('is_active', true)->count() }} on</span>
                    <span>·</span>
                    <span style="color: #F87171;">{{ $items->where('is_active', false)->count() }} off</span>
                </div>
            </div>

            {{-- Jurisdiction rows --}}
            <div class="divide-y" style="border-color: var(--border-color);">
                @foreach($items as $jurisdiction)
                    <div class="flex items-center justify-between px-4 py-2.5 {{ !$jurisdiction->is_active ? 'opacity-50' : '' }}">
                        <span class="text-sm {{ !$jurisdiction->is_active ? 'line-through' : '' }}"
                              style="color: var(--text-primary);">
                            {{ $jurisdiction->name }}
                        </span>
                        <div class="flex items-center gap-2">
                            {{-- Toggle --}}
                            <form method="POST" action="{{ route('admin.jurisdictions.toggle', $jurisdiction) }}">
                                @csrf
                                <button type="submit"
                                        title="{{ $jurisdiction->is_active ? 'Disable' : 'Enable' }}"
                                        style="display:inline-block; cursor:pointer;">
                                    <div style="width:36px;height:20px;border-radius:9999px;position:relative;transition:background 0.2s;background:{{ $jurisdiction->is_active ? '#C9A84C' : '#1e2f42' }};border:1px solid #2a3f55;">
                                        <div style="position:absolute;top:2px;left:{{ $jurisdiction->is_active ? '17px' : '2px' }};width:14px;height:14px;background:white;border-radius:9999px;transition:left 0.2s;box-shadow:0 1px 3px rgba(0,0,0,0.4);"></div>
                                    </div>
                                </button>
                            </form>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('admin.jurisdictions.destroy', $jurisdiction) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-1 rounded hover:opacity-80"
                                        style="color: #F87171;"
                                        onclick="return confirm('Delete {{ addslashes($jurisdiction->name) }}?')"
                                        title="Delete">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    {{-- Add Modal --}}
    <div x-show="addModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.75);">
        <div class="w-full max-w-sm rounded-xl p-5 space-y-4"
             style="background: var(--bg-card); border: 1px solid var(--border-color);" @click.stop>
            <h3 class="text-base font-semibold">Add Jurisdiction</h3>
            <form method="POST" action="{{ route('admin.jurisdictions.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">
                        Jurisdiction Name <span style="color: #F87171;">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="e.g. Lagos State"
                           class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                           style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">
                        Region <span style="color: #F87171;">*</span>
                    </label>
                    <input type="text" name="region" required placeholder="e.g. Nigeria"
                           list="region-list"
                           class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                           style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    <datalist id="region-list">
                        @foreach($jurisdictions->keys() as $r)
                            <option value="{{ $r }}">
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">
                        Region Flag (emoji)
                    </label>
                    <input type="text" name="region_flag" placeholder="🌐"
                           class="w-full px-3 py-2 rounded-lg text-sm outline-none"
                           style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                </div>
                <div class="flex gap-2 pt-1">
                    <button type="button" @click="addModal = false"
                            class="flex-1 py-2 rounded-lg text-sm font-medium"
                            style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-secondary);">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-2 rounded-lg text-sm font-medium hover:opacity-80"
                            style="background: var(--gold); color: #0D1B2A;">
                        Add
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
