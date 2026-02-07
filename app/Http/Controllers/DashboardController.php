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
                ->whereDate('setup_date', '>=', $today)
                ->orderBy('setup_time')
                ->get();


$calendarEvents = Requests::whereNotNull('setup_date')
    ->whereBetween('setup_date', [
        now()->subMonths(12)->startOfMonth(),
        now()->addMonths(12)->endOfMonth()
    ])
    ->get([
        'id',
        'event_name',
        'setup_date',
        'status',
        'location',
        'setup_time'
    ])
    ->map(function($ev) {

        $now = now();
        $setupDateTime = Carbon::parse($ev->setup_date . ' ' . ($ev->setup_time ?? '00:00'));

        // Compute status
        if ($ev->status === 'Declined') {
            $computed = 'Declined';
        } elseif ($ev->status === 'Active') {
            $computed = $now->gt($setupDateTime) ? 'Closed' : 'Active';
        } elseif (!$ev->status || $ev->status === 'Open') {
            $computed = 'Open';
        } else {
            $computed = $ev->status;
        }

        return [
            'id' => $ev->id,
            'event_name' => $ev->event_name,
            'setup_date' => Carbon::parse($ev->setup_date)->format('Y-m-d'),
            'location' => $ev->location,
            'setup_time' => $ev->setup_time,
            'computed_status' => $computed
        ];
    });



        }

        return view('admin.user-dashboard', compact('scheduledRequests', 'calendarEvents'));
    }

    

}

