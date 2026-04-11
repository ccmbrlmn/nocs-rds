<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Requests;
use Illuminate\Http\Request;
use PDF;
use App\Models\UserLog;

class UserController extends Controller
{

public function logs($id)
{
    $user = User::withTrashed()->findOrFail($id);

    $logs = UserLog::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($log) {
            $log->event_name = match($log->action) {
                'request_created' => 'Created',
                'request_edited'  => 'Edited',
                'request_accepted'=> 'Accepted',
                'request_declined'=> 'Declined',
                'request_cancelled'=> 'Cancelled',

                'user_registered' => 'User Registration',
                'profile_updated' => 'Profile Updated',
                'user_delete_requested' => 'Account Deletion Requested',

                default => '-',
            };

            if ($log->request_id) {
                $request = Requests::find($log->request_id);
                $log->request_name = $request->title ?? 'Unnamed Request';
            } else {
                $log->request_name = '-';
            }

            return $log;
        });

    return view('admin.user-logs', compact('user', 'logs'));
}
    
    
    
    public function edit(User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
 
        if ($user->deleted_at) {
            return redirect()->route('admin.users')
                ->with('error', 'Restore this user first before editing.');
        }

        return view('admin.user-edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $firstAdmin = User::where('role', 'admin')->orderBy('id')->first();

        if ($firstAdmin && $firstAdmin->id !== auth()->id()) {
            $firstAdmin->notify(new class($user) extends \Illuminate\Notifications\Notification {
            protected $user;

            public function __construct($user) {
                $this->user = $user;
            }

            public function via($notifiable) {
                return ['database'];
            }

            public function toDatabase($notifiable) {
                return [
                    'type' => 'user_management',
                    'action' => 'updated',
                    'user_id' => $this->user->id,
                    'user_name' => $this->user->name,
                    'actor_name' => auth()->user()->name,
                ];
            }
        });
        }

        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'office' => 'required|string|max:255', 
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'office' => $request->office,
        ]);
        
        UserLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_updated',
            'updated_at' => now(),
        ]);
        
        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $firstAdmin = User::where('role', 'admin')->orderBy('id')->first();

        if ($firstAdmin && $firstAdmin->id !== auth()->id()) {
            $firstAdmin->notify(new class($user) extends \Illuminate\Notifications\Notification {
            protected $user;

            public function __construct($user) {
                $this->user = $user;
            }

            public function via($notifiable) {
                return ['database'];
            }

            public function toDatabase($notifiable) {
                return [
                    'type' => 'user_management',
                    'action' => 'deleted',
                    'user_id' => $this->user->id,
                    'user_name' => $this->user->name,
                    'actor_name' => auth()->user()->name,
                ];
            }
        });
        }

        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user->delete();
        
        UserLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_deleted',
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

public function approve(User $user)
{

    if (auth()->user()->role !== 'admin') {
        abort(403);
    }

    $user->update([
        'is_approved' => true
    ]);

    UserLog::create([
        'user_id' => auth()->id(),
        'action' => 'user_approved',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $user->notify(new \App\Notifications\UserApprovedNotification(
        $user,
        auth()->user()
    ));

    return redirect()->route('admin.users')
        ->with('success', 'User approved successfully.');
}

    public function exportPdf(Request $request)
    {
        $firstAdminId = User::where('role', 'admin')->orderBy('id')->first()->id ?? null;

        $query = User::withTrashed()
                     ->where('role', '!=', 'admin');

        if ($firstAdminId) {
            $query->where('id', '!=', $firstAdminId);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($request->status === 'deleted') {
                $query->onlyTrashed();
            }
        }

        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case '30_days': $query->where('created_at', '>=', now()->subDays(30)); break;
                case '7_days':  $query->where('created_at', '>=', now()->subDays(7));  break;
                case '24_hours': $query->where('created_at', '>=', now()->subHours(24)); break;
            }
        }

        if ($request->filled('specific_date')) {
            $query->whereDate('created_at', $request->specific_date);
        }

        $sort = $request->sort ?? 'desc';
        $users = $query->orderBy('created_at', $sort)->get();

        $statusLabel = match ($request->status) {
            'pending' => 'Pending',
            'active' => 'Active',
            'deleted' => 'Deleted',
            default => 'All'
        };
        
        $dateLabel = $request->specific_date ?? ($request->date_filter ?? 'All Time');
        $sortLabel = $sort === 'asc' ? 'Oldest First' : 'Newest First';
        $exportedAt = now()->format('M d, Y - h:i A');

        return Pdf::loadView('admin.user-pdf', compact(
            'users',
            'statusLabel',
            'dateLabel',
            'sortLabel',
            'exportedAt'
        ))
        ->setPaper('a4', 'landscape')
        ->download('users-report.pdf');
    }

    public function exportCsv(Request $request)
    {
        $firstAdminId = User::where('role', 'admin')->orderBy('id')->first()->id ?? null;

        $query = User::withTrashed()
                     ->where('role', '!=', 'admin');

        if ($firstAdminId) {
            $query->where('id', '!=', $firstAdminId);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($request->status === 'deleted') {
                $query->onlyTrashed();
            } elseif ($request->status === 'all' || $request->status === null || $request->status === '') {
                $query->withTrashed();
            }
        } else {
            $query->withTrashed();
        }

        if ($request->filled('specific_date')) {
            $query->whereDate('created_at', $request->specific_date);
        } elseif ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case '30_days':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
                case '7_days':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case '24_hours':
                    $query->where('created_at', '>=', now()->subHours(24));
                    break;
            }
        }

        $sort = $request->sort ?? 'desc';
        $users = $query->orderBy('created_at', $sort)->get();

        $filename = "users_report.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'Name', 'Email', 'Created At', 'Status']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->created_at,
                    $user->deleted_at ? 'Deleted' : 'Active'
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        $firstAdmin = User::where('role', 'admin')->orderBy('id')->first();

        if ($firstAdmin && $firstAdmin->id !== auth()->id()) {
            $firstAdmin->notify(new class($user) extends \Illuminate\Notifications\Notification {
            protected $user;

            public function __construct($user) {
                $this->user = $user;
            }

            public function via($notifiable) {
                return ['database'];
            }

            public function toDatabase($notifiable) {
                return [
                    'type' => 'user_management',
                    'action' => 'restored',
                    'user_id' => $this->user->id,
                    'user_name' => $this->user->name,
                    'actor_name' => auth()->user()->name,
                ];
            }
        });
        }

        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user->restore();
        
        UserLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_restored',
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'User restored successfully.');
    }

    public function index(Request $request)
    {
        $query = User::withTrashed()
                     ->where('role', '!=', 'admin');

        if ($request->has('status')) {

            switch ($request->status) {

                case 'pending':
                    $query->whereNull('deleted_at')
                          ->where(function ($q) {
                              $q->where('is_approved', false)
                                ->orWhereNull('is_approved');
                          });
                    break;

                case 'active':
                    $query->whereNull('deleted_at')
                          ->where('is_approved', true);
                    break;

                case 'deleted':
                    $query->onlyTrashed();
                    break;

                case 'all':
                default:
                    $query->withTrashed();
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
                    $query->where('created_at', '>=', now()->subHours(24));
                    break;
            }
        }

        if ($request->filled('specific_date')) {
            $query->whereDate('created_at', $request->specific_date);
        }

        $sort = $request->get('sort', 'desc');
        $users = $query->orderBy('created_at', $sort)->get();

        return view('admin.user-list', compact('users'));
    }
}


