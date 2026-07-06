<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TemporaryPasswordMail;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        // By design, every admin can see every other admin's basic account info -
        // this is a small internal team dashboard, not a multi-tenant system.
        $users = User::orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $temporaryPassword = Str::password(16);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
        ]);

        // Plaintext only ever exists in memory long enough to hash it and email
        // it once - never logged, never stored anywhere.
        Mail::to($user->email)->send(
            new TemporaryPasswordMail($user->name, $temporaryPassword, isNewAccount: true)
        );

        AuditLogger::log(
            'user.created',
            Auth::user()->name . " membuat akun baru untuk {$user->name} ({$user->email}).",
            $user
        );

        return redirect()->route('admin.users.index')
            ->with('status', "User \"{$user->name}\" berhasil ditambahkan. Password sementara telah dikirim ke {$user->email}.");
    }

    /**
     * Admin-triggered reset: generates a new temporary password and emails it,
     * distinct from the self-service "forgot password" link-based flow.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        $temporaryPassword = Str::password(16);

        $user->update([
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
        ]);

        Mail::to($user->email)->send(
            new TemporaryPasswordMail($user->name, $temporaryPassword, isNewAccount: false)
        );

        AuditLogger::log(
            'user.password_reset_by_admin',
            Auth::user()->name . " me-reset password untuk {$user->name} ({$user->email}).",
            $user
        );

        return back()->with('status', "Password sementara baru telah dikirim ke {$user->email}.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'Anda tidak bisa menghapus akun Anda sendiri.']);
        }

        if (User::count() <= 1) {
            return back()->withErrors(['user' => 'Tidak bisa menghapus user terakhir di sistem.']);
        }

        $name = $user->name;
        $email = $user->email;

        // Capture the subject reference BEFORE deleting - subject_id/type are
        // just a snapshot (no live FK), so this is safe and preserves the trail.
        AuditLogger::log(
            'user.deleted',
            Auth::user()->name . " menghapus user {$name} ({$email}).",
            $user
        );

        $user->delete();

        return back()->with('status', "User \"{$name}\" telah dihapus.");
    }
}
