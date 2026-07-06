<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::query()->latest('created_at');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('actor_email')) {
            $query->where('actor_email', 'like', '%' . $request->input('actor_email') . '%');
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('until')) {
            $query->whereDate('created_at', '<=', $request->input('until'));
        }

        $logs = $query->paginate(30)->withQueryString();

        $actions = AuditLog::query()->distinct()->orderBy('action')->pluck('action');

        return view('admin.audit.index', compact('logs', 'actions'));
    }
}
