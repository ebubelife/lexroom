@extends('admin.layouts.app')

@section('title', 'Admin Users')
@section('page-title', 'Admin Users')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-serif" style="color: var(--gold);">Admin Users</h1>
            <p class="mt-1 text-sm" style="color: var(--text-secondary);">
                Manage admin accounts and role assignments.
            </p>
        </div>
        <a href="{{ route('admin.admin-users.create') }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-all hover:opacity-90"
           style="background: var(--gold); color: var(--navy);">
            + Add Admin
        </a>
    </div>

    <div class="rounded-xl overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border-color);">
        <table class="data-table w-full">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Last Login</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $adminUser)
                <tr>
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                 style="background: {{ $adminUser->isSuperAdmin() ? 'var(--gold)' : 'rgba(255,255,255,0.08)' }}; color: {{ $adminUser->isSuperAdmin() ? 'var(--navy)' : 'var(--text-secondary)' }};">
                                {{ strtoupper(substr($adminUser->name, 0, 1)) }}
                            </div>
                            <span class="font-medium" style="color: var(--text-primary);">{{ $adminUser->name }}</span>
                            @if($adminUser->id === auth('admin')->id())
                                <span class="text-xs px-1.5 py-0.5 rounded" style="background: rgba(201,168,76,0.15); color: var(--gold);">You</span>
                            @endif
                        </div>
                    </td>
                    <td style="color: var(--text-secondary);">{{ $adminUser->email }}</td>
                    <td>
                        @php
                            $roleColors = [
                                'super_admin'       => 'rgba(201,168,76,0.15); color: #C9A84C',
                                'platform_admin'    => 'rgba(59,130,246,0.15); color: #60a5fa',
                                'billing_admin'     => 'rgba(16,185,129,0.15); color: #34d399',
                                'case_manager'      => 'rgba(139,92,246,0.15); color: #a78bfa',
                                'document_reviewer' => 'rgba(236,72,153,0.15); color: #f472b6',
                                'lawyer_manager'    => 'rgba(245,158,11,0.15); color: #fbbf24',
                                'support_agent'     => 'rgba(20,184,166,0.15); color: #2dd4bf',
                                'auditor'           => 'rgba(107,114,128,0.15); color: #9ca3af',
                            ];
                            $style = $roleColors[$adminUser->role] ?? 'rgba(255,255,255,0.08); color: var(--text-secondary)';
                        @endphp
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                              style="background: {{ explode(';', $style)[0] }}; border: 1px solid {{ explode(';', $style)[0] }}; {{ explode(';', $style)[1] ?? '' }}">
                            {{ $adminUser->roleLabel() }}
                        </span>
                    </td>
                    <td style="color: var(--text-secondary);">
                        {{ $adminUser->last_login_at ? $adminUser->last_login_at->diffForHumans() : 'Never' }}
                    </td>
                    <td>
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.admin-users.edit', $adminUser) }}"
                               class="text-xs px-3 py-1.5 rounded-lg transition-colors"
                               style="background: rgba(255,255,255,0.05); color: var(--text-secondary);"
                               onmouseover="this.style.color='var(--text-primary)'"
                               onmouseout="this.style.color='var(--text-secondary)'">
                                Edit
                            </a>
                            @if($adminUser->id !== auth('admin')->id())
                            <form method="POST" action="{{ route('admin.admin-users.destroy', $adminUser) }}"
                                  onsubmit="return confirm('Delete {{ $adminUser->name }}? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-xs px-3 py-1.5 rounded-lg transition-colors"
                                        style="background: rgba(239,68,68,0.1); color: #f87171;"
                                        onmouseover="this.style.background='rgba(239,68,68,0.2)'"
                                        onmouseout="this.style.background='rgba(239,68,68,0.1)'">
                                    Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8" style="color: var(--text-secondary);">No admin accounts found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
