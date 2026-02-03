<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requests;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $scheduledRequests = collect();
        $calendarEvents = collect();

        if (auth()->check() && auth()->user()->role === 'admin') {
            $today = Carbon::today()->toDateString();

            // Scheduled requests for admin (you may adjust query if needed)
            $scheduledRequests = Requests::where('status', 'Active')
                ->whereDate('setup_date', $today)
                ->orderBy('setup_time')
                ->get();

            // Calendar events for admin
            $calendarEvents = Requests::whereNotNull('setup_date')->get([
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

