<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Requests as UserRequest;
use App\Models\UserLog;

class AdminController extends Controller
{
    public function logs($id)
    {
        $admin = User::withTrashed()
            ->where('id', $id)
            ->where('role', 'admin')
            ->firstOrFail();

        $logs = UserRequest::where(function ($query) use ($admin) {
                $query->where('handled_by', $admin->id)
                      ->orWhere('requested_by', $admin->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('auth.admin-logs', compact('admin', 'logs'));
    }

    public function listUsers(Request $request)
    {
        $query = User::query()->where('role', 'user');

        if ($request->filled('status') && $request->status !== 'All') {
            switch ($request->status) {
                case 'pending':
                    $query->where('is_approved', false);
                    break;
                case 'approved':
                    $query->where('is_approved', true);
                    break;
                case 'deleted':
                    $query->onlyTrashed(); // assumes soft deletes enabled
                    break;
            }
        }

        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case '30_days':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
                case '7_days':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case '24_hours':
                    $query->where('created_at', '>=', now()->subDay());
                    break;
            }
        }

        if ($request->filled('specific_date')) {
            $query->whereDate('created_at', $request->specific_date);
        }

        $sort = $request->get('sort', 'desc');
        $query->orderBy('created_at', $sort);

        $users = $query->get();

        return view('admin.user-list', compact('users'));
    }
}

