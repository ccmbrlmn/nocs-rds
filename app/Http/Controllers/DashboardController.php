<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requests;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // old
    /**
    public function showDashboard()
    {
        // Default empty collections
        $scheduledRequests = collect();
        $calendarEvents = collect();

        // Only fetch sensitive data if user is authenticated
        if (auth()->check()) {
            $today = \Carbon\Carbon::today()->toDateString();

            $scheduledRequests = \App\Models\Requests::where('status', 'In Progress')
                ->whereDate('start_date', $today)
                ->get();

            $calendarEvents = \App\Models\Requests::where('status', 'In Progress')
                ->get(['event_name', 'setup_date', 'location']);
        }

        return view('admin.admin-dashboard', compact('scheduledRequests', 'calendarEvents'));
    }
    **/
    
    // new
        public function showDashboard()
    {
        $scheduledRequests = collect();
        $calendarEvents = collect();

        if (auth()->check()) {
            $userId = auth()->id();
            $today = Carbon::today()->toDateString();

            // LEFT CARD: Today's scheduled (In Progress only)
            $scheduledRequests = Requests::where('requested_by', $userId)
                ->where('status', 'In Progress')
                ->whereDate('setup_date', $today)
                ->orderBy('setup_time')
                ->get();

            // CALENDAR: ALL user requests with setup_date
            $calendarEvents = Requests::where('requested_by', $userId)
                ->whereNotNull('setup_date')
                ->get([
                    'id',
                    'event_name',
                    'setup_date',
                    'status',
                    'location'
                ]);
        }

        return view('admin.admin-dashboard', compact('scheduledRequests', 'calendarEvents'));
    }

    

}

