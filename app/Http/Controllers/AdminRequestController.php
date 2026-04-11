<?php

namespace App\Http\Controllers;

use App\Models\Requests as RequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Carbon\Carbon;
use App\Notifications\RequestAcceptedNotification;
use App\Notifications\RequestRejectedNotification;

class AdminRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = RequestModel::with(['user' => function($q) {
            $q->withTrashed();
        }]);

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

        foreach ($requests as $req) {
            if ($req->computed_status === 'Closed' && $req->status !== 'Closed') {
                $req->status = 'Closed';
                $req->save();
            }
        }
        
        return view('admin.admin-requests', compact('requests'));
    }

    public function show($id)
    {
        $request = RequestModel::with(['user', 'handler'])->findOrFail($id);
        return view('admin.admin-request-details', compact('request'));
    }

    public function exportPdf(Request $request)
    {
        $query = RequestModel::query()->with('user');

        $status = $request->status;
        if ($status && $status != 'All') $query->where('status', $status);

        $dateFilter = $request->date_filter;
        $specificDate = $request->specific_date;

        if ($specificDate) {
            $query->whereDate('created_at', $specificDate);
        } elseif ($dateFilter) {
            switch ($dateFilter) {
                case '30_days': $query->where('created_at', '>=', now()->subDays(30)); break;
                case '7_days': $query->where('created_at', '>=', now()->subDays(7)); break;
                case '24_hours': $query->where('created_at', '>=', now()->subDay()); break;
            }
        }

        $sort = $request->sort ?? 'desc';
        $requests = $query->orderBy('created_at', $sort)->get();

        $statusLabel = $status ?? 'All';
        $dateLabel = $specificDate 
                        ? Carbon::parse($specificDate)->format('M d, Y') 
                        : match($dateFilter) {
                            '30_days' => 'Last 30 Days',
                            '7_days' => 'Last 7 Days',
                            '24_hours' => 'Last 24 Hours',
                            default => 'All Time',
                          };
        $sortLabel = $sort === 'asc' ? 'Oldest First' : 'Newest First';

        $exportedAt = Carbon::now()->format('M d, Y - h:i A');

        return PDF::loadView('admin.requests_pdf', compact(
            'requests', 'statusLabel', 'dateLabel', 'sortLabel', 'exportedAt'
        ))
        ->setPaper('a4', 'landscape')
        ->download('user-requests.pdf');
    }

    public function exportCsv(Request $request)
    {
        $query = RequestModel::query()->with('user');

        $status = $request->status;
        if ($status && $status != 'All') {
            $query->where('status', $status);
        }

        if ($request->specific_date) {
            $query->whereDate('created_at', $request->specific_date);
        } elseif ($request->date_filter) {
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

        $sort = $request->sort ?? 'desc';
        $requests = $query->orderBy('created_at', $sort)->get();

        $filename = "user-requests.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($requests) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Request No.',
                'Requester',
                'Event',
                'Date',
                'Purpose',
                'Status'
            ]);

            foreach ($requests as $req) {
                fputcsv($handle, [
                    $req->id,
                    $req->user->name ?? '',
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
    
public function accept($id)
{
    $req = RequestModel::findOrFail($id);
    $req->status = 'Active';
    $req->handled_by = Auth::id();
    $req->handled_at = now();
    $req->save();

    // Notify the user
    $req->user->notify(new RequestAcceptedNotification($req));

    return redirect()->back()->with('success', 'Request accepted.');
}

public function decline(Request $request, $id)
{
    $req = RequestModel::findOrFail($id);
    $req->status = 'Declined';
    $req->decline_reason = $request->input('decline_reason');
    $req->handled_by = Auth::id();
    $req->handled_at = now();
    $req->save();

    // Notify the user
    $req->user->notify(new RequestRejectedNotification($req));

    return redirect()->back()->with('success', 'Request has been declined.');
}

    public function getUserNotifications()
{
    $user = auth()->user();
    return $user->notifications()->orderBy('created_at', 'desc')->get();
}


}
