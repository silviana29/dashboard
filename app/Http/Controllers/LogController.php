<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogActivity;
use App\Models\User;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $orgId = $request->user()->organization_id;
        $organization = $request->user()->organization;
        $hasAuditLog = (bool) ($organization->plan_details->has_audit_log ?? false);

        $query = LogActivity::where('organization_id', $orgId);

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        if ($request->filled('role')) {
            $query->where('role', 'like', '%' . $request->role . '%');
        }

        if ($request->filled('user_id')) {
            $user = User::where('organization_id', $orgId)->find($request->user_id);
            if ($user) {
                $query->where('user', $user->username);
            }
        }

        $logs = $query->orderBy('created_at', 'desc')
                      ->paginate(20)
                      ->withQueryString();

        $users = User::where('organization_id', $orgId)->select('id', 'username')->orderBy('username')->get();

        return view('admin.log', compact('logs', 'users', 'organization', 'hasAuditLog'));
    }
}
