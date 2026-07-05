<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ForcePasswordChangeController extends Controller
{
    public function show(): View
    {
        return view('auth.force-password-change');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:10'],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Password sementara yang dimasukkan salah.']);
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
            'must_change_password' => false,
        ]);

        AuditLogger::log(
            'user.password_changed_forced',
            "{$user->name} menyelesaikan wajib ganti password (setelah akun baru/reset admin).",
            $user
        );

        // Force full re-login with the new password - consistent with the
        // voluntary password-change flow elsewhere in this app.
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Password berhasil dibuat. Silakan login dengan password baru Anda.');
    }
}
