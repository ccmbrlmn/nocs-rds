<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requests;
use Carbon\Carbon;

class DashboardController extends Controller
{
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

}

