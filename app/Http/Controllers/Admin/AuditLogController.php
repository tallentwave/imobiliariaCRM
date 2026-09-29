<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:admin|compliance'),
        ];
    }

    public function index(Request $request)
    {
        $logs = AuditLog::query()
            ->where('organization_id', Auth::user()->organization_id)
            ->when($request->event, fn ($q, $v) => $q->where('event', 'like', "%{$v}%"))
            ->when($request->user_id, fn ($q, $v) => $q->where('user_id', $v))
            ->with('user')
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }
}
