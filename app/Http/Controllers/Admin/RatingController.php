<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RatingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Rating::with('application')->latest('created_at');

        if ($request->filled('application_id')) {
            $query->where('application_id', $request->input('application_id'));
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->input('rating'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('until')) {
            $query->whereDate('created_at', '<=', $request->input('until'));
        }

        $ratings = $query->paginate(20)->withQueryString();
        $applications = Application::orderBy('name')->get();

        return view('admin.ratings.index', compact('ratings', 'applications'));
    }
}
