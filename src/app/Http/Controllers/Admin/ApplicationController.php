<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(): View
    {
        $applications = Application::withCount('ratings')
            ->orderBy('name')
            ->get();

        return view('admin.applications.index', compact('applications'));
    }

    public function create(): View
    {
        return view('admin.applications.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $credentials = Application::generateCredentials();

        $application = Application::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'api_key' => $credentials['api_key'],
            'api_secret' => $credentials['api_secret'],
            'is_active' => true,
        ]);

        AuditLogger::log(
            'application.created',
            Auth::user()->name . " mendaftarkan aplikasi baru \"{$application->name}\".",
            $application
        );

        // Shown exactly once - never persisted/logged in plaintext anywhere else.
        return redirect()->route('admin.applications.index')
            ->with('new_credentials', $credentials)
            ->with('status', "Aplikasi \"{$application->name}\" berhasil didaftarkan.");
    }

    public function toggleActive(Application $application): RedirectResponse
    {
        $application->update(['is_active' => ! $application->is_active]);

        $statusLabel = $application->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        AuditLogger::log(
            $application->is_active ? 'application.activated' : 'application.deactivated',
            Auth::user()->name . " {$statusLabel} aplikasi \"{$application->name}\".",
            $application
        );

        return back()->with('status', "Aplikasi \"{$application->name}\" {$statusLabel}. Data rating tetap tersimpan.");
    }

    public function regenerateCredentials(Application $application): RedirectResponse
    {
        $credentials = Application::generateCredentials();
        $application->update($credentials);

        AuditLogger::log(
            'application.credentials_regenerated',
            Auth::user()->name . " regenerate credentials untuk \"{$application->name}\".",
            $application
        );

        return back()
            ->with('new_credentials', $credentials)
            ->with('status', "Credentials baru dibuat untuk \"{$application->name}\".");
    }
}
