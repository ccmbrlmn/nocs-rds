<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLog;
use App\Models\Requests;

class AdminController extends Controller
{
    public function logs($id)
    {
        $admin = User::withTrashed()
            ->where('id', $id)
            ->where('role', 'admin')
            ->firstOrFail();

        $handledRequests = Requests::where('handled_by', $admin->id)->get();

        $combinedLogs = collect();
        
        $userLogs = UserLog::where('user_id', $admin->id)->get();

        // User logs
        foreach ($userLogs as $log) {
            $combinedLogs->push([
                'type' => 'user_log',
                'id' => $log->id,
                'action' => $log->action,
                'updated_at' => $log->updated_at,
                'event_name' => $log->action,
            ]);
        }

        // Handled requests
        foreach ($handledRequests as $req) {
            $combinedLogs->push([
                'type' => 'handled_request',
                'id' => $req->id,
                'status' => $req->status,
                'handled_at' => $req->handled_at ?? $req->created_at,
                'event_name' => $req->event_name ?? 'Request',
                'user_name' => optional($req->user)->name ?? 'N/A',
            ]);
        }

        $combinedLogs = $combinedLogs->sortByDesc(function ($item) {
            return $item['type'] === 'user_log'
                ? $item['updated_at']
                : $item['handled_at'];
        })->values();

        return view('auth.admin-logs', compact('admin', 'combinedLogs'));
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

    public function approve(User $user)
    {
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'Unauthorized.');
        }

        $user->update([
            'is_approved' => true
        ]);

        UserLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_approved',
            'updated_at' => now(),
        ]);

        return back()->with('success', 'User approved successfully.');
    }

    public function destroy($id)
    {
        $user = User::where('id', $id)
            ->where('role', 'user')
            ->firstOrFail();

        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'Unauthorized.');
        }

        UserLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_declined',
            'updated_at' => now(),
        ]);

        $user->delete();

        return back()->with('success', 'User declined and removed.');
    }
}
