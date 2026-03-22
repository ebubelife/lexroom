<?php

namespace App\Http\Controllers;

use App\Helpers\PhoneHelper;
use App\Models\User;
use App\Rules\NigerianPhone;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', new NigerianPhone],
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
            'terms' => ['required', 'accepted'],
        ], [
            'terms.accepted' => 'You must agree to the Terms of Service and Privacy Policy',
        ]);

        // Normalize phone number
        $normalizedPhone = PhoneHelper::validateAndNormalize($request->phone);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $normalizedPhone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect to verification instead of dashboard
        return redirect()->route('verification.notice');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Check if user needs verification
            if (!$user->isFullyVerified()) {
                return redirect()->route('verification.notice');
            }
            
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // Google OAuth implementation
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->id)->first();
            
            if ($user) {
                // User exists with Google ID, check if fully verified
                Auth::login($user);
                
                // Google users only need phone verification (email is pre-verified)
                if (!$user->phone_verified_at) {
                    return redirect()->route('verification.notice');
                }
                
                return redirect()->intended('/dashboard');
            }
            
            // Check if user exists with this email
            $existingUser = User::where('email', $googleUser->email)->first();
            
            if ($existingUser) {
                // User exists with email, link Google account and verify email
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'email_verified_at' => now(), // Verify email since it's from Google
                ]);
                Auth::login($existingUser);
                
                // Google users only need phone verification
                if (!$existingUser->phone_verified_at) {
                    return redirect()->route('verification.notice');
                }
                
                return redirect()->intended('/dashboard');
            }
            
            // Store Google user data in session for completion
            session([
                'google_user_id' => $googleUser->id,
                'google_user_name' => $googleUser->name,
                'google_user_email' => $googleUser->email,
            ]);
            
            // Redirect to completion form
            return redirect()->route('auth.google.complete.form');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google authentication failed. Please try again.');
        }
    }
    
    public function showGoogleComplete()
    {
        // Check if Google user data exists in session
        if (!session('google_user_id')) {
            return redirect()->route('login')->with('error', 'Google authentication session expired. Please try again.');
        }
        
        return view('auth.google-complete');
    }
    
    public function completeGoogleSignup(Request $request)
    {
        // Validate session data
        if (!session('google_user_id')) {
            return redirect()->route('login')->with('error', 'Google authentication session expired. Please try again.');
        }
        
        $request->validate([
            'phone' => ['required', 'string', new NigerianPhone],
            'terms' => ['required', 'accepted'],
        ], [
            'terms.accepted' => 'You must agree to the Terms of Service and Privacy Policy',
        ]);
        
        // Normalize phone number
        $normalizedPhone = PhoneHelper::validateAndNormalize($request->phone);
        
        // Create new user with Google data
        $newUser = User::create([
            'name' => session('google_user_name'),
            'email' => session('google_user_email'),
            'phone' => $normalizedPhone,
            'google_id' => session('google_user_id'),
            'email_verified_at' => now(), // Google accounts are pre-verified
            'password' => Hash::make(Str::random(24)), // Random password for security
        ]);
        
        // Clear session data
        session()->forget(['google_user_id', 'google_user_name', 'google_user_email']);
        
        Auth::login($newUser);
        
        // Google users only need phone verification
        if (!$newUser->phone_verified_at) {
            return redirect()->route('verification.notice');
        }
        
        return redirect()->intended('/dashboard');
    }
}