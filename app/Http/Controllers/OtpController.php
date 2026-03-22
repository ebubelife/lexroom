<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    public function showVerification()
    {
        $user = Auth::user();
        
        // If user is fully verified, redirect to dashboard
        if ($user->isFullyVerified()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-otp', [
            'needsEmailVerification' => !$user->hasVerifiedEmail(),
            'needsPhoneVerification' => !$user->hasVerifiedPhone(),
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:email,phone']
        ]);

        $user = Auth::user();
        $type = $request->type;

        // Check if already verified
        if ($type === 'email' && $user->hasVerifiedEmail()) {
            return back()->with('error', 'Email is already verified.');
        }

        if ($type === 'phone' && $user->hasVerifiedPhone()) {
            return back()->with('error', 'Phone number is already verified.');
        }

        // Generate OTP
        $otp = Otp::generateForUser($user->id, $type);

        if ($type === 'email') {
            // Send email OTP (for now, just log it)
            \Log::info("Email OTP for {$user->email}: {$otp->code}");
            
            // TODO: Send actual email when mail is configured
            // Mail::to($user->email)->send(new EmailOtpMail($otp->code));
            
            return back()->with('success', "OTP sent to your email address. Check your email for the 6-digit code.");
        } else {
            // Phone OTP - always 111111 for now
            \Log::info("Phone OTP for {$user->phone}: {$otp->code}");
            
            // TODO: Send SMS via Termii when account is ready
            
            return back()->with('success', "OTP sent to your phone number. For testing, use: 111111");
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:email,phone'],
            'code' => ['required', 'string', 'size:6']
        ]);

        $user = Auth::user();
        $type = $request->type;
        $code = $request->code;

        // Find the OTP
        $otp = Otp::where('user_id', $user->id)
            ->where('type', $type)
            ->where('code', $code)
            ->whereNull('verified_at')
            ->first();

        if (!$otp) {
            return back()->withErrors(['code' => 'Invalid or expired OTP code.']);
        }

        if ($otp->isExpired()) {
            return back()->withErrors(['code' => 'OTP code has expired. Please request a new one.']);
        }

        // Mark OTP as verified
        $otp->update(['verified_at' => now()]);

        // Update user verification status
        if ($type === 'email') {
            $user->update(['email_verified_at' => now()]);
            $message = 'Email verified successfully!';
        } else {
            $user->update(['phone_verified_at' => now()]);
            $message = 'Phone number verified successfully!';
        }

        // Check if user is now fully verified
        if ($user->fresh()->isFullyVerified()) {
            return redirect()->route('dashboard')->with('success', $message . ' Welcome to LexRoom!');
        }

        return back()->with('success', $message);
    }

    public function resendOtp(Request $request)
    {
        return $this->sendOtp($request);
    }
}