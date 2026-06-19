@extends('admin.layouts.app')

@section('title', 'Add Admin')
@section('page-title', 'Add Admin')

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">

    <div class="mb-8">
        <h1 class="text-2xl font-serif" style="color: var(--gold);">Add Admin</h1>
        <p class="mt-1 text-sm" style="color: var(--text-secondary);">Create a new admin account and assign a role.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-lg text-sm" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #f87171;">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.admin-users.store') }}">
        @csrf

        <div class="rounded-xl p-6 space-y-5" style="background: var(--bg-card); border: 1px solid var(--border-color);">

            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest mb-1.5" style="color: var(--text-secondary);">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2.5 rounded-lg text-sm"
                       style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest mb-1.5" style="color: var(--text-secondary);">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 rounded-lg text-sm"
                       style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest mb-1.5" style="color: var(--text-secondary);">Role</label>
                <select name="role" required
                        class="w-full px-4 py-2.5 rounded-lg text-sm"
                        style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                            {{ \App\Support\AdminPermissions::ROLE_LABELS[$role] ?? ucfirst(str_replace('_', ' ', $role)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest mb-1.5" style="color: var(--text-secondary);">Password</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full px-4 py-2.5 rounded-lg text-sm"
                       style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-widest mb-1.5" style="color: var(--text-secondary);">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-4 py-2.5 rounded-lg text-sm"
                       style="background: var(--bg-primary); border: 1px solid var(--border-color); color: var(--text-primary); outline: none;">
            </div>

        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('admin.admin-users.index') }}"
               class="text-sm" style="color: var(--text-secondary);">← Cancel</a>
            <button type="submit"
                    class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-all hover:opacity-90"
                    style="background: var(--gold); color: var(--navy);">
                Create Account
            </button>
        </div>

    </form>
</div>
@endsection
