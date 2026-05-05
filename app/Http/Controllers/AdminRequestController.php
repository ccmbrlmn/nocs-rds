<?php

namespace App\Http\Controllers;

use App\Models\Requests as RequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Carbon\Carbon;
use App\Notifications\RequestApprovedNotification;
use App\Notifications\RequestCancelledNotification;
use App\Models\AssetTransaction;
use App\Models\Asset;
use App\Notifications\RequestReturnAcceptedNotification;
use App\Notifications\RequestReturnNotification;
use App\Models\UserLog;

class AdminRequestController extends Controller
{
    public function index(Request $request)
    {
        $highlightId = $request->query('highlight');
        
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

        $allRequests = RequestModel::with(['user' => function ($q) {
            $q->withTrashed();
        }])->get();

        $statusData = $allRequests
            ->groupBy('computed_status')
            ->map->count();

        $statusLabels = $statusData->keys()->values();
        $statusValues = $statusData->values();
        
        $assets = Asset::where('asset_status', 'Available')
            ->pluck('asset_category')
            ->unique()
            ->values();

        return view('admin.admin-requests', compact(
            'requests',
            'allRequests',
            'statusData',
            'statusLabels',
            'statusValues',
            'assets',
            'highlightId'
        ));
    }

    public function show($id)
    {
        $request = RequestModel::with(['user', 'handler', 'transactions'])->findOrFail($id);
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
    
public function approve($id)
{
    $req = RequestModel::findOrFail($id);

    $items = json_decode($req->items, true);

    if ($req->status === 'Open') {

    foreach ($items as $item) {

        $availableAssets = \App\Models\Asset::whereRaw(
                'LOWER(TRIM(asset_category)) = ?',
                [strtolower(trim($item['name']))]
            )
            ->where('asset_status', 'Available')
            ->get();

        if ($availableAssets->count() < $item['quantity']) {
            return back()->with('error', 'Not enough ' . $item['name'] . ' available.');
        }

        $availableAssets = $availableAssets->take($item['quantity']);

        foreach ($availableAssets as $asset) {
                AssetTransaction::create([
                    'asset_id' => $asset->id,
                    'request_id' => $req->id,
                    'user_id' => $req->requested_by,
                    'actor_id' => Auth::id(),
                    'status' => 'Borrowed',
                    'borrowed_at' => now(),
                    
                ]);
            }
        }

        $req->status = 'Active';
        $req->active_at = now();
    }


    // Common updates
    $req->handled_by = Auth::id();
    $req->handled_at = now();
    $req->approved_at = now();
    $req->save();
    
    UserLog::create([
        'actor_id' => Auth::id(),
        'user_id' => $req->requested_by,
        'request_id' => $req->id,
        'action' => 'request_approved',
        'description' => 'Approved request: ' . $req->event_name,
    ]);

    $req->user->notify(new RequestApprovedNotification($req));

    return redirect()->back()->with('success', 'Request processed successfully.');
}

public function cancel(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);
        $req->status = 'Cancelled';
        $req->cancel_reason = $request->input('cancel_reason');
        $req->handled_by = Auth::id();
        $req->handled_at = now();
        $req->save();
        
        UserLog::create([
            'actor_id' => Auth::id(),
            'user_id' => $req->requested_by,
            'request_id' => $req->id,
            'action' => 'request_cancelled_admin',
            'description' => 'Cancelled request: ' . $req->event_name,
        ]);

        $req->user->notify(new RequestCancelledNotification($req));

        return redirect()->back()->with('success', 'Request has been cancelled.');
    }

    public function getUserNotifications()
    {
        $user = auth()->user();
        return $user->notifications()->orderBy('created_at', 'desc')->get();
    }
    
    public function acceptReturn(Request $request, $id)
    {
        $req = RequestModel::findOrFail($id);

        if ($req->status === 'Pending Return') {
            AssetTransaction::where('request_id', $req->id)
                ->where('status', 'Borrowed')
                ->update([
                    'status' => 'Returned',
                    'returned_at' => now(),
                ]);

            $req->status = 'Pending Retrieval'; 
            $req->handled_by = Auth::id();
            $req->handled_at = now();
            $req->returned_at = now();
            $req->personnel_name = $request->input('personnel');
            $req->save();
            
            UserLog::create([
                'actor_id' => Auth::id(),
                'user_id' => $req->requested_by,
                'request_id' => $req->id,
                'action' => 'return_accepted',
                'description' => json_encode([
                    'event_name' => $req->event_name,
                    'personnel_name' => $req->personnel_name,
                    'assets' => AssetTransaction::where('request_id', $req->id)
                        ->where('status', 'Returned')
                        ->with('asset')
                        ->get()
                        ->map(function ($tx) {
                            return [
                                'asset_name' => $tx->asset->asset_name ?? $tx->asset->name ?? 'Unnamed Asset'
                            ];
                        })
                ])
            ]);

            $req->user->notify(
                new RequestReturnAcceptedNotification($req)
            );
        }

        return redirect()->back()->with('success', 'Return approved.');
    }
    
    public function cancelReturn($id)
    {
        $req = RequestModel::findOrFail($id);

        if ($req->status === 'Pending Return') {
            $req->status = 'Active'; // BACK TO ACTIVE
            $req->handled_by = Auth::id();
            $req->handled_at = now();
            $req->save();

            $req->user->notify(
                new RequestCancelledNotification($req)
            );
        }

        return redirect()->back()->with('success', 'Return cancelled.');
    }


public function markRetrieved($id)
{
    $req = RequestModel::findOrFail($id);

    if ($req->status === 'Pending Retrieval') {

        $assetIds = AssetTransaction::where('request_id', $req->id)
            ->pluck('asset_id')
            ->unique();

        Asset::whereIn('id', $assetIds)
            ->update([
                'asset_status' => 'Available'
            ]);

        AssetTransaction::where('request_id', $req->id)
            ->where('status', 'Returned')
            ->update([
                'status' => 'Retrieved',
                'retrieved_at' => now(),
            ]);
            
        $req->retrieved_at = now();

        $req->status = 'Closed';
        $req->handled_by = Auth::id();
        $req->handled_at = now();
        $req->save();
        
        UserLog::create([
            'actor_id' => Auth::id(),
            'user_id' => $req->requested_by,
            'request_id' => $req->id,
            'action' => 'assets_retrieved',
            'description' => json_encode([
                'event_name' => $req->event_name,
                'handled_by' => Auth::user()->name ?? 'Admin',
                'assets' => AssetTransaction::where('request_id', $req->id)
                    ->where('status', 'Retrieved')
                    ->with('asset')
                    ->get()
                    ->map(function ($tx) {
                        return [
                            'asset_name' => $tx->asset->asset_name ?? $tx->asset->name ?? 'Unnamed Asset'
                        ];
                    })
            ])
        ]);

    }

    return redirect()->back()->with('success', 'Asset retrieved successfully.');
}
    
    public function assignAssetsPage($id)
    {
        $request = \App\Models\Requests::with('user')->findOrFail($id);

        $assets = Asset::where('asset_status', 'Available')->get();

        return view('admin.assign-assets', compact('request', 'assets'));
    }

    public function storeAssignedAssets(Request $req, $id)
    {
        $request = \App\Models\Requests::with('user')->findOrFail($id);

        $assetIds = $req->assets ?? [];

        foreach ($assetIds as $assetId) {
            \App\Models\AssetTransaction::create([
                'asset_id' => $assetId,
                'request_id' => $request->id,
                'user_id' => auth()->id(),
                'status' => 'Borrowed',
                'borrowed_at' => now(),
            ]);
        }

        Asset::whereIn('id', $assetIds)
            ->update(['asset_status' => 'In Use']);

        $request->status = 'Active';
        $request->active_at = now();
        $request->approved_at = now();
        $request->save();
        
        UserLog::create([
            'actor_id' => Auth::id(),
            'user_id' => $request->requested_by,
            'request_id' => $request->id,
            'action' => 'request_approved',
            'description' => 'Approved request: ' . $request->event_name,
        ]);

        return redirect()->route('admin.requests')
            ->with('success', 'Assets assigned successfully.');
            
    }

}

