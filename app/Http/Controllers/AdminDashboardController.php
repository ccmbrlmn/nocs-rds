<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requests;
use Carbon\Carbon;
use App\Models\Asset; 

class AdminDashboardController extends Controller
{
public function index()
{
    $scheduledRequests = collect();
    $calendarEvents = collect();

    if (auth()->check() && auth()->user()->role === 'admin') {

        $scheduledRequests = Requests::all();

        $assets = Asset::latest()->get();
        $requests = Requests::with('user')->get();

        $statusData = $assets
            ->groupBy('computed_status')
            ->map->count();
            
        $categories = $assets
            ->pluck('asset_category')
            ->filter()
            ->unique()
            ->values();

        $categoryData = $assets
            ->groupBy(fn($item) => $item->asset_category ?? 'Uncategorized')
            ->map->count();

        $statusLabels = $statusData->keys()->values();
        $statusValues = $statusData->values();

        $categoryLabels = $categoryData->keys()->values();
        $categoryValues = $categoryData->values();

        $categoryStatusData = $assets
            ->groupBy(fn($item) => $item->asset_category ?? 'Uncategorized')
            ->map(function ($group) {
                return [
                    'Available' => $group->where('asset_status', 'Available')->count(),
                    'In Use' => $group->where('asset_status', 'In Use')->count(),
                    'Maintenance' => $group->where('asset_status', 'Maintenance')->count(),
                ];
            });
            
        $categories = $assets->pluck('asset_category')->unique()->values();

        $statusTypes = ['Available', 'In Use', 'Maintenance'];

        $categoryStatusChart = [
            'labels' => $categories->values(),

            'Available' => $categories->map(function ($category) use ($assets) {
                return $assets->where('asset_category', $category)
                    ->where('asset_status', 'Available')
                    ->count();
            })->values(),

            'In Use' => $categories->map(function ($category) use ($assets) {
                return $assets->where('asset_category', $category)
                    ->where('asset_status', 'In Use')
                    ->count();
            })->values(),

            'Maintenance' => $categories->map(function ($category) use ($assets) {
                return $assets->where('asset_category', $category)
                    ->where('asset_status', 'Maintenance')
                    ->count();
            })->values(),
        ];

        $calendarEvents = Requests::with('user')
            ->whereNotNull('setup_date')
            ->get()
            ->map(function($ev) {
                $now = now();

                $setupDateTime = $ev->setup_date
                    ? Carbon::parse($ev->setup_date->format('Y-m-d') . ' ' . ($ev->setup_time ?? '00:00'))
                    : null;

                if ($ev->status === 'Declined') {
                    $computed = 'Declined';
                } elseif ($ev->status === 'Active' && $setupDateTime) {
                    $computed = $now->gt($setupDateTime) ? 'Closed' : 'Active';
                } elseif (!$ev->status || $ev->status === 'Open') {
                    $computed = 'Open';
                } else {
                    $computed = $ev->status;
                }

                return [
                    'id' => $ev->id,
                    'event_name' => $ev->event_name,
                    'setup_date' => $ev->setup_date ? $ev->setup_date->format('Y-m-d') : null,
                    'location' => $ev->location,
                    'setup_time' => $ev->setup_time,
                    'computed_status' => $computed,
                    'requester_name' => $ev->user->name ?? 'Unknown',
                ];
            });

    } else {
        $scheduledRequests = collect();
        $calendarEvents = collect();
        $assets = collect();
        $statusData = collect();
        $categoryData = collect();
        $categoryStatusData = collect();
    }

    return view('admin.admin-dashboard', compact(
        'scheduledRequests',
        'calendarEvents',
        'assets',
        'statusData',
        'categoryData',
        'categoryStatusData',
        'statusLabels',
        'statusValues',
        'categoryLabels',
        'categoryValues',
        'categoryStatusChart',
        'categories',
        'requests'
    ));
}
}

