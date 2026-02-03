<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requests;
use Carbon\Carbon;

class DashboardController extends Controller
{
        public function showDashboard()
    {
        $scheduledRequests = collect();
        $calendarEvents = collect();

        if (auth()->check()) {
            $userId = auth()->id();
            $today = Carbon::today()->toDateString();

            $scheduledRequests = Requests::where('requested_by', $userId)
                ->where('status', 'Active')
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

        return view('admin.user-dashboard', compact('scheduledRequests', 'calendarEvents'));
    }

    

}

