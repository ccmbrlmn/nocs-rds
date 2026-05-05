<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Requests;
use Illuminate\Support\Facades\DB;
use PDF;
use Carbon\Carbon;

class AssetController extends Controller
{

public function index(Request $request)
{
    $query = Asset::query();

    /* =========================
       STATUS FILTER
    ========================= */
    if ($request->filled('status') && $request->status !== 'All') {
        $query->where('asset_status', $request->status);
    }

    /* =========================
       DATE FILTER
    ========================= */
    if ($request->filled('date')) {

        switch ($request->date) {

            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;

            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case '7_days':
                $query->where('created_at', '>=', now()->subDays(7))
                      ->orderBy('created_at', 'desc');
                break;

            case '30_days':
                $query->where('created_at', '>=', now()->subDays(30))
                      ->orderBy('created_at', 'desc');
                break;

            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

    } else {
        $query->orderBy('created_at', 'desc');
    }

    $assets = $query->get();

    $statusData = Asset::select('asset_status')
        ->get()
        ->groupBy('asset_status')
        ->map->count();

    $categoryData = Asset::select('asset_category')
        ->get()
        ->groupBy('asset_category')
        ->map->count();

    $categoryStatusData = Asset::all()
        ->groupBy('asset_category')
        ->map(function ($items) {
            return [
                'Available' => $items->where('asset_status', 'Available')->count(),
                'In Use' => $items->where('asset_status', 'In Use')->count(),
                'Maintenance' => $items->where('asset_status', 'Maintenance')->count(),
            ];
        });

    return view('admin.asset-list', compact(
        'assets',
        'statusData',
        'categoryData',
        'categoryStatusData'
    ));
}

    public function store(Request $request)
    {
        $request->validate([
            'asset_name' => 'required|string',
            'asset_tag' => 'nullable|string',
            'asset_serial' => 'nullable|string',
            'asset_model' => 'nullable|string',
            'asset_category' => 'nullable|string',
            'asset_status' => 'required|string',
            'created_by' => 'nullable|string',
        ]);

        $asset = Asset::create([
            'asset_name' => $request->asset_name,
            'asset_tag' => $request->asset_tag,
            'asset_serial' => $request->asset_serial,
            'asset_model' => $request->asset_model,
            'asset_category' => $request->asset_category,
            'asset_status' => $request->asset_status,
            'created_by' => auth()->id()
        ]);
        
        
        // LOG: asset created
        \App\Models\UserLog::create([
            'actor_id' => auth()->id(),
            'user_id' => auth()->id(),

            'action' => 'asset_created',

            'description' => json_encode([
                'asset_name' => $asset->asset_name,
                'asset_category' => $asset->asset_category,
                'asset_status' => $asset->asset_status,
            ]),

            'target_user_id' => null,
            'target_user_name' => null,
            'actor_name' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Asset created successfully!');
    }
    
    public function update(Request $request, $id)
    {
$asset = Asset::findOrFail($id);

$oldAsset = clone $asset;

$asset->update($request->only([
    'asset_name',
    'asset_tag',
    'asset_serial',
    'asset_model',
    'asset_category',
    'asset_status',
]));

$changes = [];

$fields = [
    'asset_name',
    'asset_tag',
    'asset_serial',
    'asset_model',
    'asset_category',
    'asset_status',
];

foreach ($fields as $field) {
    if ($oldAsset->$field != $asset->$field) {
        $changes[$field] = [
            'old' => $oldAsset->$field ?? '-',
            'new' => $asset->$field ?? '-',
        ];
    }
}

if (!empty($changes)) {
    \App\Models\UserLog::create([
        'actor_id' => auth()->id(),
        'user_id' => auth()->id(),

        'action' => 'asset_updated',

        'description' => json_encode($changes),

        'target_user_id' => null,
        'target_user_name' => null,
        'actor_name' => auth()->user()->name,
    ]);
}

        return redirect()->route('admin.assets')
            ->with('success', 'Asset updated successfully!');
    }
    
    public function retrieved(Request $request, $assetId)
    {
            
        $asset = Asset::findOrFail($assetId);

        DB::transaction(function () use ($asset, $request) {

            $asset->update([
                'asset_status' => 'Available',
            ]);

            $asset->transactions()->create([
                'status' => 'Retrieved',
                'retrieved_at' => now(),
                'personnel_name' => $request->personnel ?? null,
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()->back()->with('success', 'Asset retrieved successfully.');
    }

    public function show($id)
    {
        $asset = Asset::with([
            'creator',
            'transactions.user',
            'transactions.request'
        ])->findOrFail($id);

        return view('admin.asset-details', compact('asset'));
    }
    
    public function exportPdf(Request $request)
    {
        $query = Asset::query();

        if ($request->status && $request->status != 'All') {
            $query->where('asset_status', $request->status);
        }

        if ($request->category) {
            $query->where('asset_category', $request->category);
        }

        $assets = $query->orderBy('created_at', 'desc')->get();

        $exportedAt = Carbon::now()->format('M d, Y - h:i A');

        return PDF::loadView('admin.assets_pdf', compact('assets', 'exportedAt'))
            ->download('assets.pdf');
    }

public function exportCsv(Request $request)
    {
        $query = Asset::query();

        if ($request->status && $request->status != 'All') {
            $query->where('asset_status', $request->status);
        }

        if ($request->category) {
            $query->where('asset_category', $request->category);
        }

        $assets = $query->orderBy('created_at', 'desc')->get();

        $filename = "assets.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($assets) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Name',
                'Tag',
                'Serial',
                'Model',
                'Category',
                'Status',
                'Created At'
            ]);

            foreach ($assets as $asset) {
                fputcsv($handle, [
                    $asset->id,
                    $asset->asset_name,
                    $asset->asset_tag,
                    $asset->asset_serial,
                    $asset->asset_model,
                    $asset->asset_category,
                    $asset->asset_status,
                    $asset->created_at,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    
}
