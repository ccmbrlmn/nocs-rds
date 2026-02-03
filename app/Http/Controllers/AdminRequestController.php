<?php

namespace App\Http\Controllers;

use App\Models\Requests as RequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRequestController extends Controller
{
    public function index()
    {
        $requests = RequestModel::all();
        return view('admin.admin-requests', compact('requests'));
    }

    public function show($id)
    {
        $request = RequestModel::with(['user', 'handler'])->findOrFail($id);
        return view('admin.admin-request-details', compact('request'));
    }

    public function accept($id)
    {
        $request = RequestModel::findOrFail($id);
        $request->status = 'In Progress';
        $request->handled_by = Auth::id();
        $request->save();

        return redirect()->back()->with('success', 'Request accepted.');
    }

public function decline(Request $request, $id)
{
    $req = RequestModel::findOrFail($id); // <- model
    $req->status = 'Declined';
    $req->decline_reason = $request->input('decline_reason'); // <- HTTP request input
    $req->handled_by = Auth::id();
    $req->save();

    return redirect()->back()->with('success', 'Request has been declined.');
}
}

