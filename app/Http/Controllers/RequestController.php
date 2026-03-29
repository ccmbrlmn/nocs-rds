<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requests; 
use App\Models\User; 
use App\Mail\NewRequestNotification;
use Illuminate\Support\Facades\Mail;
use App\Models\UserLog;
use App\Models\Notification;
use PDF;
use Carbon\Carbon;

class RequestController extends Controller
{
    public function index(){
        $requests = Requests::where('status', 'Open')->get(); 
        return view('admin.admin-requests', compact('requests'));
    }

    public function adminRequest(Request $request){
        $status = $request->query('status');
        $dateFilter = $request->query('date_filter');
        $specificDate = $request->query('specific_date');

        $query = Requests::query(); 

        if ($status) {
            $query->where('status', $status);
        }

        if ($dateFilter) {
            $now = now();
            if ($dateFilter === '30_days') {
                $query->where('created_at', '>=', $now->subDays(30));
            } elseif ($dateFilter === '7_days') {
                $query->where('created_at', '>=', $now->subDays(7));
            } elseif ($dateFilter === '24_hours') {
                $query->where('created_at', '>=', $now->subHours(24));
            }
        }

        if ($specificDate) {
            $query->whereDate('created_at', $specificDate);
        }
        
        $query->orderBy('created_at', $sort);

        $requests = $query->get();
        $totalRequests = $requests->count();
 
        return view('admin.user-requests', compact('requests', 'logs', 'totalRequests'));
    }

    public function userRequest(Request $request, $userId = null)
    {
        $userId = $userId ?? auth()->id();

        $user = User::findOrFail($userId);

        $logs = UserLog::with('request')
            ->where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.user-logs', compact('logs', 'user'));
    }

    public function show($id) {
        $request = Requests::with('handledByAdmin')->findOrFail($id); 
        return view('admin.user-request-details', compact('request')); 
    }

    public function complete($id)
    {
        $requestRecord = Requests::findOrFail($id); 
        if( $requestRecord->status === 'Active'){
            $requestRecord->status = 'Closed';
            $requestRecord->save();
        }

        return redirect()->back()->with('success', 'Request marked as completed.');
    }

    public function decline(Request $request, $id)
    {
        $requestRecord = Requests::findOrFail($id);

        if ($requestRecord->status === 'Open') {
            $requestRecord->status = 'Declined';
            $requestRecord->decline_reason = $request->input('decline_reason');

            // Set who handled and when
            $requestRecord->handled_by = auth()->id();
            $requestRecord->handled_at = now();

            $requestRecord->save();

            UserLog::create([
                'user_id' => auth()->id(),
                'request_id' => $requestRecord->id,
                'action' => 'request_declined',
                'description' => 'Declined request: ' . $requestRecord->event_name,
            ]);
        }

        return redirect()->back()->with('success', 'Request declined with reason.');
    }


    public function cancel(Request $request, $id)
    {
        $requestRecord = Requests::findOrFail($id);

        if ($requestRecord->status === 'Open') {
            $requestRecord->status = 'Declined';
            $requestRecord->cancel_reason = $request->input('cancel_reason');
            $requestRecord->save();
        }

        UserLog::create([
            'user_id' => auth()->id(),
            'request_id' => $requestRecord->id,
            'action' => 'request_cancelled',
            'description' => 'Cancelled request: ' . $requestRecord->event_name,
        ]);
        
        $admins = User::whereIn('role', ['first_admin', 'admin'])->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'sender_id' => auth()->id(),
                'type' => 'request_cancelled',
                'message' => $userName . ' cancelled the request: ' . $requestRecord->event_name,
                'data' => json_encode(['request_id' => $requestRecord->id]),
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Request cancelled with reason.');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'representative_name' => 'required|string',
            'event_name' => 'required|string',
            'purpose' => 'required|string',
            'items' => 'required|array|max:5',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'other_purpose' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'setup_date' => 'nullable|date',
            'setup_time' => 'nullable',
            'location' => 'required|string',
            'users' => 'required|integer',
        ]);

        $requestedBy = auth()->id(); 
        $user = User::find($requestedBy);
        $userName = $user ? $user->name : 'Unknown User'; 

        $req = Requests::create([
            'representative_name' => $validated['representative_name'], 
            'event_name' => $validated['event_name'],
            'purpose' => $validated['purpose'],
            'items' => json_encode($validated['items']),
            'other_purpose' => $validated['other_purpose'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'setup_date' => $validated['setup_date'] ?? $validated['start_date'],
            'setup_time' => $validated['setup_time'] ?? null,
            'location' => $validated['location'],
            'users' => $validated['users'],
            'requested_by' => $requestedBy,
            'status' => 'Open',
            'personnel_name' => $validated['personnel_name'] ?? null,
            'other_equipments' => $validated['other_equipments'] ?? null,
            'decline_reason' => $validated['decline_reason'] ?? null,
            'cancel_reason' => $validated['cancel_reason'] ?? null,
        ]);
        
        $admins = User::whereIn('role', ['first_admin', 'admin'])->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'sender_id' => auth()->id(),
                'type' => 'request_created',
                'message' => $userName . ' created a new request: ' . $validated['event_name'],
                'data' => json_encode(['request_id' => $req->id]),
                'is_read' => false,
            ]);
        }

        $requestData = $validated;
        $requestData['requested_by'] = $userName;

        Mail::to(config('mail.admin'))->send(
            new NewRequestNotification($requestData)
        );

        return redirect()->back()->with('success', 'Request submitted successfully!');
    }

    public function update(Request $request, $id)
    {
        $req = Requests::where('id', $id)
            ->where('requested_by', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'representative_name' => 'required|string',
            'event_name' => 'required|string',
            'purpose' => 'required|string',
            'items' => 'required|array|max:5',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'other_purpose' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'setup_date' => 'nullable|date|before_or_equal:end_date',
            'setup_time' => 'nullable',
            'location' => 'required|string',
            'users' => 'required|integer',
        ]);

        $req->representative_name = $validated['representative_name'];
        $req->event_name = $validated['event_name'];
        $req->purpose = $validated['purpose'];
        $req->other_purpose = $validated['other_purpose'] ?? null;
        $req->start_date = $validated['start_date'];
        $req->end_date = $validated['end_date'];
        $req->setup_date = $validated['setup_date'] ?? null;
        $req->setup_time = $validated['setup_time'] ?? null;
        $req->location = $validated['location'];
        $req->users = $validated['users'];
        $req->items = json_encode($validated['items']);

        $req->save();

        return redirect()->back()->with('success', 'Request updated successfully!');
    }

    public function myLogs()
    {
        $userId = auth()->id();
        $user = User::findOrFail($userId);

        $logs = UserLog::with('request')
            ->where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.user-logs', compact('logs', 'user'));
    }

    public function myRequests(Request $request)
    {
        $userId = auth()->id();

        $query = Requests::where('requested_by', $userId);

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
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
        $query->orderBy('created_at', $sort);

        $requests = $query->get();
        $totalRequests = $requests->count();

        return view('admin.user-requests', compact('requests', 'totalRequests'));
    }

    public function getNotifications()
    {
        $userId = auth()->id();

        $notifications = UserLog::with('request')
            ->where('user_id', $userId)
            ->whereIn('action', ['request_accepted', 'request_declined'])
            ->where('is_read', false)
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    public function accept(Request $request, $id)
    {
        $deploymentRequest = Requests::findOrFail($id); 

        $deploymentRequest->other_equipments = $request->other_equipments;
        $deploymentRequest->status = 'Active';
        $deploymentRequest->handled_by = auth()->id();
        $deploymentRequest->handled_at = now();
        $deploymentRequest->save();

        UserLog::create([
            'user_id' => $deploymentRequest->requested_by,
            'request_id' => $deploymentRequest->id,
            'action' => 'request_accepted',
            'description' => 'Your request "' . $deploymentRequest->event_name . '" has been accepted.',
            'is_read' => false
        ]);

        return redirect()->back()->with('success', 'Request updated successfully.');
    }

    public function getAdminNotifications()
    {
        $userId = auth()->id();

        $notifications = Notification::with('sender')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($notif) {
                $data = json_decode($notif->data, true);

                return [
                    'id' => $notif->id,
                    'message' => $notif->message,
                    'type' => $notif->type,
                    'is_read' => $notif->is_read,
                    'request_id' => $data['request_id'] ?? null,

                    'created_at' => $notif->created_at
                        ? $notif->created_at->format('M d, Y h:i A')
                        : null,
                ];
            });

        return response()->json($notifications);
    }

    public function markNotificationsRead()
    {
        $userId = auth()->id();
        Notification::where('user_id', $userId)->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function exportUserPdf(Request $request)
    {
        $userId = auth()->id();

        $query = Requests::where('requested_by', $userId);

        if ($request->status && $request->status != 'All') {
            $query->where('status', $request->status);
        }

        if ($request->specific_date) {
            $query->whereDate('created_at', $request->specific_date);
        } elseif ($request->date_filter) {
            switch ($request->date_filter) {
                case '30_days': $query->where('created_at', '>=', now()->subDays(30)); break;
                case '7_days': $query->where('created_at', '>=', now()->subDays(7)); break;
                case '24_hours': $query->where('created_at', '>=', now()->subDay()); break;
            }
        }

        $sort = $request->sort ?? 'desc';
        $requests = $query->orderBy('created_at', $sort)->get();

        $statusLabel = $request->status ?? 'All';

        $dateLabel = $request->specific_date 
            ? Carbon::parse($request->specific_date)->format('M d, Y') 
            : match($request->date_filter) {
                '30_days' => 'Last 30 Days',
                '7_days' => 'Last 7 Days',
                '24_hours' => 'Last 24 Hours',
                default => 'All Time',
            };

        $sortLabel = $sort === 'asc' ? 'Oldest First' : 'Newest First';

        $exportedAt = Carbon::now()->format('M d, Y - h:i A');

        return Pdf::loadView('admin.requests_pdf', compact(
            'requests',
            'statusLabel',
            'dateLabel',
            'sortLabel',
            'exportedAt'
        ))->download('my-requests.pdf');
    }

    public function exportUserCsv(Request $request)
    {
        $userId = auth()->id();

        $query = Requests::where('requested_by', $userId);

        if ($request->status && $request->status != 'All') {
            $query->where('status', $request->status);
        }

        if ($request->specific_date) {
            $query->whereDate('created_at', $request->specific_date);
        } elseif ($request->date_filter) {
            switch ($request->date_filter) {
                case '30_days': $query->where('created_at', '>=', now()->subDays(30)); break;
                case '7_days': $query->where('created_at', '>=', now()->subDays(7)); break;
                case '24_hours': $query->where('created_at', '>=', now()->subDay()); break;
            }
        }

        $sort = $request->sort ?? 'desc';
        $requests = $query->orderBy('created_at', $sort)->get();

        $filename = "my-requests.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($requests) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Request No.', 'Event', 'Date', 'Purpose', 'Status']);

            foreach ($requests as $req) {
                fputcsv($handle, [
                    $req->id,
                    $req->event_name,
                    $req->created_at,
                    $req->purpose,
                    $req->computed_status
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

