<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Rating;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalRatings = Rating::count();

        return view('admin.dashboard', [
            'activeApps' => Application::where('is_active', true)->count(),
            'totalRatings' => $totalRatings,
            'avgRating' => $totalRatings > 0 ? round(Rating::avg('rating'), 2) : 0,
            'last7Days' => Rating::where('created_at', '>=', now()->subDays(7))->count(),
            'recentRatings' => Rating::with('application')->latest('created_at')->limit(5)->get(),
        ]);
    }
}
