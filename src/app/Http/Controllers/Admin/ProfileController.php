<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use App\Services\Totp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:10'],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        AuditLogger::log('user.password_changed_self', "{$user->name} mengganti password sendiri.", $user, $user);

        // Force logout after password change - standard security practice.
        // If the password was changed because of a suspected compromise,
        // keeping the current session alive would defeat the purpose.
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Password berhasil diubah. Silakan login kembali dengan password baru.');
    }

    /**
     * Step 1: generate a secret, show it to the user to enter manually into
     * their authenticator app. Not yet enabled until confirmed below.
     */
    public function setupTwoFactor(Request $request): View
    {
        $user = Auth::user();
        $secret = Totp::generateSecret();

        // Stash temporarily in session until confirmed - avoids partially
        // enabling 2FA if the user abandons the flow.
        $request->session()->put('2fa_setup_secret', $secret);

        return view('admin.profile.two-factor-setup', [
            'secret' => $secret,
            'uri' => Totp::provisioningUri($secret, $user->email, 'Survei Kepuasan Persandian'),
        ]);
    }

    public function confirmTwoFactor(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $secret = $request->session()->get('2fa_setup_secret');

        if (! $secret) {
            return redirect()->route('admin.profile.edit')
                ->withErrors(['code' => 'Sesi setup kedaluwarsa, mulai ulang.']);
        }

        if (! Totp::verify($secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'Kode tidak valid. Pastikan waktu di HP Anda akurat.']);
        }

        $recoveryCodes = Totp::generateRecoveryCodes();

        Auth::user()->update([
            'two_factor_secret' => $secret,
            'two_factor_enabled' => true,
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);

        AuditLogger::log('auth.2fa_enabled', Auth::user()->name . ' mengaktifkan 2FA.', Auth::user(), Auth::user());

        $request->session()->forget('2fa_setup_secret');

        return redirect()->route('admin.profile.edit')
            ->with('recovery_codes', $recoveryCodes)
            ->with('status', '2FA berhasil diaktifkan. Simpan recovery codes di bawah ini sekarang.');
    }

    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $request->validate(['current_password' => ['required']]);

        if (! Hash::check($request->input('current_password'), Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password salah.']);
        }

        Auth::user()->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_recovery_codes' => null,
        ]);

        AuditLogger::log('auth.2fa_disabled', Auth::user()->name . ' menonaktifkan 2FA.', Auth::user(), Auth::user());

        return back()->with('status', '2FA dinonaktifkan.');
    }
}
