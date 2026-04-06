<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use App\Mail\EmailOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    /**
     * Generate and send email OTP for a user
     * 
     * @param User $user
     * @return Otp
     */
    public static function generateAndSendEmailOtp(User $user)
    {
        // Generate OTP
        $otp = Otp::generateForUser($user->id, 'email');

        // Send email OTP
        try {
            Mail::to($user->email)->send(new EmailOtpMail($otp->code));
            Log::info("Email OTP sent to {$user->email}: {$otp->code}");
        } catch (\Exception $e) {
            Log::error("Failed to send email OTP to {$user->email}: " . $e->getMessage());
            // Fallback: still log it so developers can see it
            Log::info("FALLBACK: Email OTP for {$user->email}: {$otp->code}");
        }

        return $otp;
    }
    public function showVerification(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $verifyPhone = $request->query('verify') === 'phone';
        
        // If user is fully verified and not explicitly requesting phone verification, redirect to dashboard
        if ($user->isFullyVerified() && !$verifyPhone) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-otp', [
            'needsEmailVerification' => !$user->hasVerifiedEmail(),
            'needsPhoneVerification' => $verifyPhone && !$user->hasVerifiedPhone(),
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

        if ($type === 'email') {
            self::generateAndSendEmailOtp($user);
            return back()->with('success', "OTP sent to your email address. Check your email for the 6-digit code.");
        } else {
            // Phone OTP - always 111111 for now
            $otp = Otp::generateForUser($user->id, 'phone');
            Log::info("Phone OTP for {$user->phone}: {$otp->code}");
            
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
            // For debugging - log what OTPs exist for this user
            \Log::info("OTP verification failed for user {$user->id}, type: {$type}, code: {$code}");
            $existingOtps = Otp::where('user_id', $user->id)->where('type', $type)->get();
            \Log::info("Existing OTPs:", $existingOtps->toArray());
            
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
        $user = $user->fresh();
        if ($user->isFullyVerified()) {
            // Send welcome email (Sync instead of queue for shared hosting)
            try {
                Mail::to($user->email)->send(new \App\Mail\WelcomeEmail($user));
                \Log::info("Successfully sent welcome email to " . $user->email);
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome email: ' . $e->getMessage());
            }
            
            return redirect()->route('dashboard')->with('success', $message . ' Welcome to FirstMediator!');
        }

        return back()->with('success', $message);
    }

    public function resendOtp(Request $request)
    {
        return $this->sendOtp($request);
    }
}