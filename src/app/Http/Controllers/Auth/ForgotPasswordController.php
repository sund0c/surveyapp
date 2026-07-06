<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        // Password::sendResetLink handles: generating a random token, storing
        // only its HASH in password_reset_tokens (not the raw token), setting
        // expiry (config/auth.php -> passwords.users.expire, default 60 min),
        // and throttling repeated requests for the same email (default 60s).
        $status = Password::sendResetLink($request->only('email'));

        // Log server-side for anomaly monitoring (e.g. repeated RESET_LINK_THROTTLED
        // for the same email could indicate a targeted account takeover attempt) -
        // but never surface this detail to the client response itself.
        \Illuminate\Support\Facades\Log::info('Password reset requested', [
            'email' => $request->input('email'),
            'status' => $status,
            'ip' => $request->ip(),
        ]);

        // Deliberately identical response whether the email exists or not -
        // never reveal which emails are registered admins in this system.
        return back()->with('status', 'Kalau email terdaftar, link reset password sudah dikirim. Silakan cek inbox Anda.');
    }
}
