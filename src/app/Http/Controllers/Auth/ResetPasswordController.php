<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:10'],
        ]);

        // Password::reset() verifies the token against its stored HASH and
        // checks expiry internally - we never compare the raw token ourselves.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Note: this intentionally does NOT touch two_factor_enabled/secret.
                // 2FA is tied to the physical authenticator device, not the password,
                // so a password reset alone should not silently disable 2FA protection.
                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            // Generic error - don't reveal whether the token was invalid,
            // expired, or the email didn't match a real account.
            return back()->withErrors(['email' => 'Link reset password tidak valid atau sudah kedaluwarsa. Silakan minta link baru.']);
        }

        return redirect()->route('login')
            ->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}
