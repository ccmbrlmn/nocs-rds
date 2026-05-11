<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requests;
use App\Models\User;
use App\Mail\NewRequestNotification;
use App\Mail\EditedRequestNotification;
use Illuminate\Support\Facades\Mail;
use App\Models\UserLog;
use App\Models\Notification;
use PDF;
use Carbon\Carbon;
use App\Notifications\RequestCreatedNotification;
use App\Notifications\RequestApprovedNotification;
use App\Notifications\RequestCancelledNotification;
use App\Notifications\RequestReturnNotification;
use App\Models\Asset;


class RequestController extends Controller
{
    public function index(){
        $requests = Requests::where('status', 'Open')->get();

        return view('admin.admin-requests', [
            'requests' => $requests,
            'assets' => $this->getAssetCategories(),
            'personnel' => $this->getPersonnel(),
        ]);
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

        $sort = $request->get('sort', 'desc');
        $query->orderBy('created_at', $sort);

        $requests = $query->get();
        $totalRequests = $requests->count();

        return view('admin.user-requests', [
            'requests' => $requests,
            'totalRequests' => $totalRequests,
            'assets' => $this->getAssetCategories(),
            'personnel' => $this->getPersonnel(),
        ]);
    }

    public function userRequest(Request $request, $userId = null)
    {
        $userId = $userId ?? auth()->id();

        $user = User::findOrFail($userId);

        $logs = UserLog::with([
            'request.assetTransactions.asset'
        ])
        ->where('user_id', $userId)
        ->orWhereHas('request', function ($query) use ($userId) {
            $query->where('requested_by', $userId);
        })
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

        if ($requestRecord->status !== 'Active') {
            return redirect()->back()->with('error', 'Request is not active.');
        }

        $requestRecord->status = 'Closed';
        $requestRecord->save();

        $assets = Asset::where('request_id', $requestRecord->id)->get();

        foreach ($assets as $asset) {
            $asset->update([
                'asset_status' => 'Available',
                'request_id' => null,
            ]);
        }

        return redirect()->back()->with('success', 'Request completed and assets released.');
    }

    public function cancel(Request $request, $id)
    {
        $requestRecord = Requests::findOrFail($id);

        if ($requestRecord->status === 'Open') {
            $requestRecord->status = 'Cancelled';
            $requestRecord->cancel_reason = $request->input('cancel_reason');

            // Set who handled and when
            $requestRecord->handled_by = auth()->id();
            $requestRecord->handled_at = now();

            $requestRecord->save();

            UserLog::create([
                'user_id' => auth()->id(),
                'request_id' => $requestRecord->id,
                'action' => 'request_cancelled',
                'description' => 'Cancelled request: ' . $requestRecord->event_name,
            ]);
        }

        return redirect()->back()->with('success', 'Request cancelled with reason.');
    }


    public function store(Request $request){
        $validated = $request->validate([
            'representative_name' => 'required|string',
            'event_name' => 'required|string',
            'purpose' => 'required|string',
            'items' => 'required|array|max:20',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'other_purpose' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'setup_date' => 'nullable|date',
            'setup_time' => 'nullable',
            'location' => 'required|string',
            'users' => 'required|integer',
            'requested_employee' => 'nullable|string',
            'note' => 'nullable|string',
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
            'cancel_reason' => $validated['cancel_reason'] ?? null,
            'cancel_reason' => $validated['cancel_reason'] ?? null,
            'requested_employee' => $validated['requested_employee'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        UserLog::create([
            'user_id' => $requestedBy,
            'request_id' => $req->id,
            'action' => 'request_created',
            'description' => json_encode([
                'event_name' => $validated['event_name'],
                'requested_employee' => $validated['requested_employee'] ?? null,
                'note' => $validated['note'] ?? null,
            ])
        ]);

        $admins = User::whereIn('role', ['first_admin', 'admin'])->get();

        foreach ($admins as $admin) {
    $admin->notify(
        new RequestCreatedNotification(
            auth()->user(),
            $req->id,
            $validated['event_name'],
            'created'
        )
    );
}

        $requestData = $validated;
        $requestData['requested_by'] = $userName;

        $requestData['setup_date'] = $validated['setup_date'] ?? null;
        $requestData['setup_time'] = $validated['setup_time'] ?? null;
        $requestData['users'] = $validated['users'] ?? null;
        $requestData['requested_employee'] = $validated['requested_employee'] ?? null;
        $requestData['note'] = $validated['note'] ?? null;

        Mail::to(config('mail.admin'))->send(
            new NewRequestNotification($requestData)
        );

        return redirect()->back()->with('success', 'Request submitted successfully!');
    }

    public function requestReturn($id)
{
    $requestRecord = Requests::findOrFail($id);

    if ($requestRecord->requested_by !== auth()->id()) {
        abort(403);
    }

    if ($requestRecord->status === 'Pending Return') {
        return redirect()->back()->with('error', 'Return already requested.');
    }

    if ($requestRecord->status === 'Active') {

        $requestRecord->status = 'Pending Return';
        $requestRecord->save();

        $admins = User::whereIn('role', ['first_admin', 'admin'])->get();

        foreach ($admins as $admin) {
            $admin->notify(
                new \App\Notifications\RequestEditedNotification(
                    auth()->user(),
                    $requestRecord->id,
                    $requestRecord->event_name,
                    'return_requested'
                )
            );
        }

        UserLog::create([
            'user_id' => auth()->id(),
            'request_id' => $requestRecord->id,
            'action' => 'return_requested',
            'description' => 'User requested return for: ' . $requestRecord->event_name,
        ]);

        return redirect()->back()->with('success', 'Return request sent to admin.');
    }

    return redirect()->back()->with('error', 'Invalid request state.');
}

public function update(Request $request, $id)
{
    $req = Requests::where('id', $id)
        ->where('requested_by', auth()->id())
        ->firstOrFail();

    if ($req->is_edited) {
        return redirect()->back()->with('error', 'You can only edit this request once.');
    }

    $validated = $request->validate([
        'representative_name' => 'required|string',
        'event_name' => 'required|string',
        'purpose' => 'required|string',
        'items' => 'required|array|max:20',
        'items.*.name' => 'required|string',
        'items.*.quantity' => 'required|integer|min:1',
        'other_purpose' => 'nullable|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'setup_date' => 'nullable|date|before_or_equal:end_date',
        'setup_time' => 'nullable',
        'location' => 'required|string',
        'users' => 'required|integer',
        'requested_employee' => 'nullable|string',
        'note' => 'nullable|string',
    ]);

    $oldData = $req->getOriginal();

    $req->fill([
        'representative_name' => $validated['representative_name'],
        'event_name' => $validated['event_name'],
        'purpose' => $validated['purpose'],
        'other_purpose' => $validated['other_purpose'] ?? null,
        'start_date' => $validated['start_date'],
        'end_date' => $validated['end_date'],
        'setup_date' => $validated['setup_date'] ?? null,
        'setup_time' => $validated['setup_time'] ?? null,
        'location' => $validated['location'],
        'users' => $validated['users'],
        'requested_employee' => $validated['requested_employee'] ?? null,
        'note' => $validated['note'] ?? null,
        'items' => $validated['items'],
        'is_edited' => true,
    ]);

    $req->save();

    $admins = User::whereIn('role', ['first_admin', 'admin'])->get();

    $newData = $req->fresh()->toArray();

    $changes = [];

    $fields = [
        'representative_name',
        'event_name',
        'purpose',
        'other_purpose',
        'start_date',
        'end_date',
        'setup_date',
        'setup_time',
        'location',
        'users',
        'items',
        'requested_employee',
        'note',
    ];

    foreach ($fields as $field) {
        $old = $oldData[$field] ?? null;
        $new = $newData[$field] ?? null;

        if ($field === 'items') {
            $old = is_string($old) ? json_decode($old, true) : $old;
            $new = is_string($new) ? json_decode($new, true) : $new;
        }

        if ($old != $new) {
            $changes[$field] = [
                'old' => $old,
                'new' => $new,
            ];
        }
    }

    $requestData = $req->fresh()->toArray();

    foreach ($admins as $admin) {
        Mail::to($admin->email)->send(
            new EditedRequestNotification($requestData, $changes)
        );
    }


    UserLog::create([
        'user_id' => auth()->id(),
        'request_id' => $req->id,
        'action' => 'request_edited',
        'description' => json_encode($changes),
    ]);

    $admins = User::whereIn('role', ['first_admin', 'admin'])->get();

    foreach ($admins as $admin) {
        $admin->notify(
            new \App\Notifications\RequestEditedNotification(
                auth()->user(),
                $req->id,
                $validated['event_name'],
                'edited'
            )
        );
    }

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

        return view('admin.user-requests', [
            'requests' => $requests,
            'totalRequests' => $totalRequests,
            'assets' => $this->getAssetCategories(),
            'personnel' => $this->getPersonnel(),
        ]);
    }



    public function approve(Request $request, $id)
    {

        $deploymentRequest = Requests::findOrFail($id);

        $deploymentRequest->other_equipments = $request->other_equipments;
        $deploymentRequest->status = 'Active';
        $deploymentRequest->handled_by = auth()->id();
        $deploymentRequest->handled_at = now();
        $deploymentRequest->save();

        if ($request->has('asset_ids')) {
            foreach ($request->asset_ids as $assetId) {

                $asset = Asset::find($assetId);

                if ($asset) {
                    $asset->request_id = $deploymentRequest->id;
                    $asset->asset_status = 'In Use';
                    $asset->save();
                }
            }
        }

        $deploymentRequest->user->notify(
            new RequestApprovedNotification(
                $deploymentRequest,
                auth()->user()
            )
        );

        UserLog::create([
            'user_id' => auth()->id(),
            'request_id' => $deploymentRequest->id,
            'action' => 'request_approved',
            'description' => 'Approved request: ' . $deploymentRequest->event_name,
        ]);

        return redirect()->back()->with('success', 'Request updated successfully.');
    }



    public function getAdminNotifications()
    {
        $userId = auth()->id();

    $notifications = auth()->user()->notifications()
        ->latest()
        ->take(20)
        ->get()
        ->map(function ($notif) {
            $data = $notif->data;

            return [
                'id' => $notif->id,
                'message' => $data['message'] ?? '',
                'type' => $notif->type,
                'type_label' => $data['type_label'] ?? $data['action'] ?? '',
                'data' => $data,
                'is_read' => $notif->read_at ? true : false,
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
        auth()->user()->unreadNotifications->markAsRead();

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

    public function getUserNotifications()
    {
        return auth()->user()->notifications()
            ->whereIn('type', [
                \App\Notifications\RequestApprovedNotification::class,
                \App\Notifications\RequestRejectedNotification::class,
            ])
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($notif) {
                $data = $notif->data;

                return [
                    'id' => $notif->id,
                    'message' => $data['message'] ?? '',
                    'type' => $notif->type,
                    'type_label' => $data['type_label'] ?? $data['action'] ?? '',
                    'data' => $data,
                    'is_read' => $notif->read_at ? true : false,
                    'request_id' => $data['request_id'] ?? null,
                    'created_at' => optional($notif->created_at)->format('M d, Y h:i A'),
                ];
            });
    }


    public function edit($id)
    {
        $request = Requests::where('id', $id)
            ->where('requested_by', auth()->id())
            ->firstOrFail();

        if ($request->is_edited) {
            return response()->json([
                'message' => 'You are not allowed to edit this request anymore.'
            ], 403);
        }

        return view('admin.edit', [
            'request' => $request,
            'assets' => $this->getAssetCategories(),
            'personnel' => $this->getPersonnel(),
        ]);
    }

    private function getAssetCategories()
    {
        return Asset::where('asset_status', 'Available')
            ->pluck('asset_category')
            ->unique()
            ->values();
    }

    private function getPersonnel()
    {
        return User::where('role', 'personnel')
            ->select('id', 'name', 'office')
            ->orderBy('name')
            ->get();
    }

}

