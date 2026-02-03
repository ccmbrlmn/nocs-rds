<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requests; 
use App\Models\User; 
use App\Mail\NewRequestNotification;
use Illuminate\Support\Facades\Mail;

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

        $requests = $query->get(); 
    
        return view('admin.admin-requests', compact('requests'));
    }


    public function userRequest(Request $request) {
        $userId = auth()->id(); 
    
        $status = $request->query('status');
        $dateFilter = $request->query('date_filter');
    
        $query = Requests::where('requested_by', $userId);
    
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

        if ($request->has('specific_date')) {
            $query->whereDate('created_at', $request->input('specific_date'));
        }
        
    
        $requests = $query->get(); 
    
        return view('request', compact('requests'));
    }
    

    public function show($id) {
        $request = Requests::with('handledByAdmin')->findOrFail($id); 
        return view('admin.request-details', compact('request')); 
    }

    public function accept(Request $request, $id)
    {
        $deploymentRequest = Requests::findOrFail($id); 


        $deploymentRequest->other_equipments = $request->other_equipments;
        $deploymentRequest->status = 'In Progress';
        $deploymentRequest->handled_by = auth()->id();

        $deploymentRequest->save();

        return redirect()->back()->with('success', 'Request updated successfully.');
    }

    public function complete($id)
    {
        $requestRecord = Requests::findOrFail($id); 
        if( $requestRecord->status === 'In Progress'){
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
            $requestRecord->save();
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

        return redirect()->back()->with('success', 'Request cancel with reason.');
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

        Requests::create([
            'representative_name' => $validated['representative_name'], 
            'event_name' => $validated['event_name'],
            'purpose' => $validated['purpose'],
            'items' => json_encode($validated['items']),
            'other_purpose' => $validated['other_purpose'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'setup_date' => $validated['setup_date'] ?? null,
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
        
        $requestData = $validated;
        $requestData['requested_by'] = $userName;

        Mail::to(config('mail.admin'))->send(
            new NewRequestNotification($requestData)
        );


        return redirect()->back()->with('success', 'Request submitted successfully!');
    }
    
            public function update(Request $request, $id)
        {
            // Find the request
            $req = Requests::where('id', $id)
                ->where('requested_by', auth()->id())
                ->firstOrFail();


            // Validate incoming data
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


            // Update the request
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
            $req->items = $validated['items'];

            $req->save();

            return redirect()->back()->with('success', 'Request updated successfully!');
        }

}
