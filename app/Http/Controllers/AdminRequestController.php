<?php

namespace App\Http\Controllers;

use App\Models\Requests as RequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRequestController extends Controller
{
public function index(Request $request)
{
    $query = RequestModel::with('user'); 

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

    public function accept($id)
    {
        $req = RequestModel::findOrFail($id);
        $req->status = 'Active';
        $req->handled_by = Auth::id();
        $req->handled_at = now();
        $req->save();

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

    return redirect()->back()->with('success', 'Request has been declined.');
}
}

