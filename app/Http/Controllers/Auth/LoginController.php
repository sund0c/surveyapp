<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Totp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        // If 2FA is enabled, do NOT log in yet - stash a pending user id and
        // force the separate challenge step. Never trust just email+password
        // once 2FA is turned on.
        if ($user->two_factor_enabled) {
            $request->session()->put('2fa_pending_user_id', $user->id);
            $request->session()->put('2fa_remember', $request->boolean('remember'));

            return redirect()->route('login.2fa');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function showTwoFactorForm(Request $request): RedirectResponse|View
    {
        if (! $request->session()->has('2fa_pending_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function verifyTwoFactor(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('2fa_pending_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $request->validate(['code' => ['required', 'string']]);

        $user = User::findOrFail($userId);
        $code = $request->input('code');

        $valid = Totp::verify($user->two_factor_secret, $code);

        // Fallback: allow a recovery code, consume it so it can't be reused
        if (! $valid && $user->two_factor_recovery_codes) {
            $codes = $user->two_factor_recovery_codes;
            $normalizedInput = strtoupper(trim($code));

            if (in_array($normalizedInput, $codes, true)) {
                $valid = true;
                $user->update([
                    'two_factor_recovery_codes' => array_values(array_diff($codes, [$normalizedInput])),
                ]);
            }
        }

        if (! $valid) {
            return back()->withErrors(['code' => 'Kode tidak valid atau sudah kedaluwarsa.']);
        }

        $remember = $request->session()->pull('2fa_remember', false);
        $request->session()->forget('2fa_pending_user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
