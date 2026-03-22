@extends('layouts.app')

@section('title', 'Settings — LexRoom')
@section('page-title', 'Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="rounded-xl shadow-sm border p-6 lg:p-8 mb-6"
         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        
        <h2 class="text-2xl font-serif mb-6" style="color: var(--text-primary);">Profile Settings</h2>
        
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Name Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">First Name</label>
                        <input type="text" 
                               name="first_name" 
                               value="{{ auth()->user()->first_name }}"
                               class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                               style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Last Name</label>
                        <input type="text" 
                               name="last_name" 
                               value="{{ auth()->user()->last_name }}"
                               class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                               style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Email Address</label>
                    <input type="email" 
                           name="email" 
                           value="{{ auth()->user()->email }}"
                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Phone Number</label>
                    <input type="tel" 
                           name="phone" 
                           value="{{ auth()->user()->phone }}"
                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

                <!-- BVN (Optional) -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        BVN (Optional)
                        <span class="text-xs font-normal" style="color: var(--text-secondary);">— For identity verification</span>
                    </label>
                    <input type="text" 
                           name="bvn" 
                           value="{{ auth()->user()->bvn }}"
                           maxlength="11"
                           placeholder="12345678901"
                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

                <!-- NIN (Optional) -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        NIN (Optional)
                        <span class="text-xs font-normal" style="color: var(--text-secondary);">— National Identification Number</span>
                    </label>
                    <input type="text" 
                           name="nin" 
                           value="{{ auth()->user()->nin }}"
                           maxlength="11"
                           placeholder="12345678901"
                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

                <!-- Save Button -->
                <div class="flex justify-end pt-4">
                    <button type="submit" 
                            class="px-6 py-3 rounded-lg text-white font-medium transition-colors hover:opacity-90"
                            style="background-color: var(--gold);">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Change Password Section -->
    <div class="rounded-xl shadow-sm border p-6 lg:p-8"
         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        
        <h2 class="text-2xl font-serif mb-6" style="color: var(--text-primary);">Change Password</h2>
        
        <form action="{{ route('settings.password') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Current Password</label>
                    <input type="password" 
                           name="current_password"
                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">New Password</label>
                    <input type="password" 
                           name="password"
                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Confirm New Password</label>
                    <input type="password" 
                           name="password_confirmation"
                           class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-gold focus:border-gold"
                           style="background-color: var(--bg-primary); color: var(--text-primary); border-color: var(--border-color);">
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" 
                            class="px-6 py-3 rounded-lg text-white font-medium transition-colors hover:opacity-90"
                            style="background-color: var(--gold);">
                        Update Password
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
