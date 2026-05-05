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

    $adminLogs = UserLog::where(function ($q) use ($admin) {
            $q->where('actor_id', $admin->id)
              ->orWhere('target_user_id', $admin->id)
              ->orWhere('user_id', $admin->id);
        })
        ->latest()
        ->get()
        ->map(function ($log) {

            return [
                'type' => 'admin_log',
                'id' => $log->id,
                'action' => $log->action,

                'event_name' => match ($log->action) {
                    'user_updated' => 'Edited User Account',
                    'user_deleted' => 'Deleted User Account',
                    'user_restored' => 'Restored User Account',
                    'user_approved' => 'Approved User Registration',

                    'profile_updated' => 'Updated Profile',

                    default => ucfirst(str_replace('_', ' ', $log->action)),
                },

                'description' => $log->description,
                'updated_at' => $log->created_at,
                'target_user_name' => $log->target_user_name,
                'target_user_id' => $log->target_user_id,
            ];
        });

    return view('auth.admin-logs', [
        'admin' => $admin,
        'combinedLogs' => $adminLogs
    ]);
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
                    $query->onlyTrashed();
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
    
public function approveDeletion(User $user)
{
    if (auth()->user()->role !== 'admin') {
        return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
    }

    if (!$user->trashed()) {
        $user->delete();
    }

    $user->notify(new \App\Notifications\DeletionApproved($user->name));
    
    UserLog::create([
    'actor_id' => auth()->id(),
    'user_id' => $user->id,
    'action' => 'user_deletion_approved',
    'description' => 'Approved deletion request for ' . $user->name,
    'target_user_id' => $user->id,
    'target_user_name' => $user->name,
]);

    return response()->json([
        'status' => 'success',
        'message' => "User {$user->name}'s account deletion approved."
    ]);
}

public function declineDeletion(User $user)
{
    if (auth()->user()->role !== 'admin') {
        return response()->json(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
    }

    $user->notify(new \App\Notifications\DeletionDeclined($user->name));
    
    UserLog::create([
    'actor_id' => auth()->id(),
    'user_id' => $user->id,
    'action' => 'user_deletion_declined',
    'description' => 'Declined deletion request for ' . $user->name,
    'target_user_id' => $user->id,
    'target_user_name' => $user->name,
]);

    return response()->json([
        'status' => 'success',
        'message' => "User {$user->name}'s account deletion declined."
    ]);
}

public function getAdminUserDeletionNotifications()
{
    $notifications = auth()->user()->notifications()
        ->where('type', 'user_deletion_request')
        ->latest()
        ->take(20)
        ->get()
        ->map(function ($notif) {
            $data = $notif->data;

            return [
                'id' => $notif->id,
                'message' => $data['message'] ?? 'User requested deletion',
                'type' => $data['type'] ?? '',
                'is_read' => $notif->read_at ? true : false,
                'user_id' => $data['user_id'] ?? null,
                'created_at' => $notif->created_at
                    ? $notif->created_at->format('M d, Y h:i A')
                    : null,
            ];
        });

    return response()->json($notifications);
}
}

