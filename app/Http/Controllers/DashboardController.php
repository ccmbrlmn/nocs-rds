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


$calendarEvents = Requests::with('user')
    ->whereNotNull('setup_date')
    ->where('requested_by', auth()->id())
    ->get()
    ->map(function($ev) {
        $now = now();
        $setupDateTime = Carbon::parse(
            Carbon::parse($ev->setup_date)->toDateString() . ' ' . ($ev->setup_time ?? '00:00')
        );


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
            'computed_status' => $computed,
            'requester_name' => $ev->user->name ?? 'Unknown',
        ];
    });

        }

        return view('admin.user-dashboard', compact('scheduledRequests', 'calendarEvents'));
    }

}
