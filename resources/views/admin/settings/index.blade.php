@extends('admin.layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div x-data="{ activeTab: '{{ request('tab', 'settings') }}' }" class="space-y-4">

    {{-- Tab bar --}}
    <div class="flex gap-1 p-1 rounded-xl w-fit" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <button @click="activeTab = 'settings'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
                :style="activeTab === 'settings'
                    ? 'background: var(--gold); color: #0D1B2A;'
                    : 'color: var(--text-secondary);'">
            ⚙ Platform Settings
        </button>
        <button @click="activeTab = 'audit'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
                :style="activeTab === 'audit'
                    ? 'background: var(--gold); color: #0D1B2A;'
                    : 'color: var(--text-secondary);'">
            📋 Audit Log
        </button>
    </div>

    {{-- ==================== SETTINGS TAB ==================== --}}
    <div x-show="activeTab === 'settings'">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            @foreach($groups as $groupName => $keys)
                <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
                    {{-- Group header --}}
                    <div class="px-5 py-3 flex items-center gap-2" style="border-bottom: 1px solid var(--border-color);">
                        <h2 class="text-sm font-semibold">{{ $groupName }}</h2>
                        <span class="text-xs px-2 py-0.5 rounded-full" style="background: rgba(201,168,76,0.12); color: var(--gold);">
                            {{ count($keys) }} setting{{ count($keys) !== 1 ? 's' : '' }}
                        </span>
                    </div>

                    <div class="divide-y" style="border-color: var(--border-color);">
                        @foreach($keys as $key => $meta)
                            @php $current = $settings->get($key)?->value ?? ''; @endphp
                            <div class="flex items-center justify-between gap-4 px-5 py-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium">{{ $meta['label'] }}</p>
                                    <p class="text-xs mt-0.5" style="color: var(--text-secondary);">{{ $meta['description'] }}</p>
                                    <p class="text-xs mt-0.5 font-mono" style="color: var(--text-secondary); opacity: 0.5;">{{ $key }}</p>
                                </div>

                                <div class="flex-shrink-0">
                                    @if($meta['type'] === 'boolean')
                                        {{-- Toggle switch --}}
                                        @php $isOn = filter_var($current, FILTER_VALIDATE_BOOLEAN); @endphp
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="{{ $key }}" value="1"
                                                   class="sr-only peer" {{ $isOn ? 'checked' : '' }}>
                                            <div class="w-11 h-6 rounded-full peer transition-all duration-200 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                                                 style="background: {{ $isOn ? 'var(--gold)' : 'var(--border-color)' }};"
                                                 :style="$el.previousElementSibling.checked ? 'background: var(--gold)' : 'background: var(--border-color)'"
                                                 x-init="$el.previousElementSibling.addEventListener('change', e => $el.style.background = e.target.checked ? 'var(--gold)' : 'var(--border-color)')">
                                            </div>
                                        </label>
                                    @elseif($meta['type'] === 'integer')
                                        <input type="number" name="{{ $key }}" value="{{ $current }}"
                                               min="0"
                                               class="w-28 px-3 py-1.5 rounded-lg text-sm text-right outline-none"
                                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                    @else
                                        <input type="text" name="{{ $key }}" value="{{ $current }}"
                                               class="w-56 px-3 py-1.5 rounded-lg text-sm outline-none"
                                               style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Save button --}}
            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity"
                        style="background: var(--gold); color: #0D1B2A;">
                    Save All Settings
                </button>
            </div>
        </form>
    </div>

    {{-- ==================== AUDIT LOG TAB ==================== --}}
    <div x-show="activeTab === 'audit'" x-cloak class="space-y-4">

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.settings.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="tab" value="audit">

            <input type="text" name="action_filter" value="{{ request('action_filter') }}"
                   placeholder="Filter by action…"
                   class="flex-1 px-3 py-2 rounded-lg text-sm outline-none"
                   style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">

            <select name="admin_filter" class="px-3 py-2 rounded-lg text-sm outline-none"
                    style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
                <option value="">All admins</option>
                @foreach($admins as $admin)
                    <option value="{{ $admin->id }}" {{ request('admin_filter') == $admin->id ? 'selected' : '' }}>
                        {{ $admin->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0"
                    style="background: var(--gold); color: #0D1B2A;">Filter</button>

            @if(request()->hasAny(['action_filter', 'admin_filter']))
                <a href="{{ route('admin.settings.index', ['tab' => 'audit']) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium flex-shrink-0 text-center"
                   style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary);">Clear</a>
            @endif
        </form>

        {{-- Audit table --}}
        <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
            <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
                <p class="text-sm" style="color: var(--text-secondary);">
                    {{ number_format($actions->total()) }} action{{ $actions->total() !== 1 ? 's' : '' }} logged
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full data-table">
                    <thead>
                        <tr>
                            <th>Admin</th>
                            <th>Action</th>
                            <th>Target</th>
                            <th>Details</th>
                            <th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($actions as $action)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                             style="background: rgba(201,168,76,0.15); color: var(--gold);">
                                            {{ strtoupper(substr($action->admin->name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm">{{ $action->admin->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(201,168,76,0.1); color: var(--gold);">
                                        {{ str_replace('_', ' ', $action->action) }}
                                    </span>
                                </td>
                                <td class="text-sm" style="color: var(--text-secondary);">
                                    @if($action->target_type && $action->target_id)
                                        <span class="font-mono text-xs">{{ $action->target_type }} #{{ $action->target_id }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-xs max-w-xs" style="color: var(--text-secondary);">
                                    @if($action->meta)
                                        @php
                                            $meta = $action->meta;
                                            // Show a compact summary
                                            $summary = collect($meta)->map(fn($v, $k) => "{$k}: " . (is_array($v) ? json_encode($v) : $v))->implode(' · ');
                                        @endphp
                                        <span class="truncate block max-w-[220px]" title="{{ $summary }}">
                                            {{ Str::limit($summary, 80) }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-xs" style="color: var(--text-secondary);">
                                    <span title="{{ $action->created_at->format('d M Y H:i:s') }}">
                                        {{ $action->created_at->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12" style="color: var(--text-secondary);">
                                    No audit log entries yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($actions->hasPages())
                <div class="px-4 py-3 flex items-center justify-between" style="border-top: 1px solid var(--border-color);">
                    <p class="text-xs" style="color: var(--text-secondary);">
                        Showing {{ $actions->firstItem() }}–{{ $actions->lastItem() }} of {{ $actions->total() }}
                    </p>
                    <div class="flex gap-1">
                        @if($actions->onFirstPage())
                            <span class="px-3 py-1.5 rounded-lg text-xs opacity-30"
                                  style="background: var(--bg-card); border: 1px solid var(--border-color);">← Prev</span>
                        @else
                            <a href="{{ $actions->previousPageUrl() . '&tab=audit' }}"
                               class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80"
                               style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">← Prev</a>
                        @endif
                        @if($actions->hasMorePages())
                            <a href="{{ $actions->nextPageUrl() . '&tab=audit' }}"
                               class="px-3 py-1.5 rounded-lg text-xs hover:opacity-80"
                               style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">Next →</a>
                        @else
                            <span class="px-3 py-1.5 rounded-lg text-xs opacity-30"
                                  style="background: var(--bg-card); border: 1px solid var(--border-color);">Next →</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
