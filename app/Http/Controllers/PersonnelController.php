<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class PersonnelController extends Controller
{

public function index(Request $request)
{
    $query = \App\Models\User::where('role', 'personnel');

    if ($request->status === 'deleted') {
        $query->onlyTrashed();
    } elseif ($request->status === 'active') {
        $query->whereNull('deleted_at');
    } else {
        $query->withTrashed(); // show all
    }

    $personnel = $query->orderBy('created_at', 'desc')->get();

    return view('admin.personnel-list', compact('personnel'));
}

    public function create()
    {
        return redirect()->route('admin.personnel');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'office' => 'nullable|string|max:255',
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,

            'role' => 'personnel',

            'password' => bcrypt($request->password),

            'office' => $request->office,

            'is_approved' => 1,

            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.personnel')
            ->with('success', 'Personnel created successfully.');
    }


    public function exportPdf(Request $request)
    {
        $query = \App\Models\User::where('role', 'personnel');

        $statusLabel = 'All';
        $dateLabel = 'All Time';
        $sortLabel = 'Newest First';

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
                $statusLabel = 'Active';
            } elseif ($request->status === 'deleted') {
                $query->onlyTrashed();
                $statusLabel = 'Deleted';
            }
        }

        $sort = $request->sort ?? 'desc';
        $sortLabel = $sort === 'asc' ? 'Oldest First' : 'Newest First';

        $personnel = $query->orderBy('created_at', $sort)->get();

        return \PDF::loadView('admin.personnel-pdf', [
            'personnel' => $personnel,
            'statusLabel' => $statusLabel,
            'dateLabel' => $dateLabel,
            'sortLabel' => $sortLabel,
            'exportedAt' => now()->format('M d, Y - h:i A'),
        ])
        ->setPaper('a4', 'landscape')
        ->download('personnel-report.pdf');
    }

    public function exportCsv(Request $request)
    {
        $query = \App\Models\User::where('role', 'personnel');

        // STATUS FILTER
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($request->status === 'deleted') {
                $query->onlyTrashed();
            } else {
                $query->withTrashed();
            }
        } else {
            $query->withTrashed();
        }

        // SORT (added for consistency with PDF)
        $sort = $request->sort ?? 'desc';
        $query->orderBy('created_at', $sort);

        $personnel = $query->get();

        $filename = "personnel_report.csv";

        return response()->stream(function () use ($personnel) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Name',
                'Email',
                'Office',
                'Created At',
                'Status'
            ]);

            foreach ($personnel as $p) {
                fputcsv($handle, [
                    $p->id,
                    $p->name,
                    $p->email,
                    $p->office ?? '-',
                    $p->created_at,
                    $p->deleted_at ? 'Deleted' : 'Active'
                ]);
            }

            fclose($handle);
        }, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ]);
    }

    public function edit($id)
    {
        $personnel = User::where('role', 'personnel')->findOrFail($id);

        return view('admin.personnel-edit', compact('personnel'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'office' => 'nullable|string|max:255',
        ]);

        $personnel = User::where('role', 'personnel')->findOrFail($id);

        $personnel->update([
            'name' => $request->name,
            'email' => $request->email,
            'office' => $request->office,
        ]);

        return redirect()->route('admin.personnel')
            ->with('success', 'Personnel updated successfully.');
    }

    public function destroy($id)
    {
        $personnel = User::where('role', 'personnel')->findOrFail($id);

        $personnel->delete();

        return redirect()->route('admin.personnel')
            ->with('success', 'Personnel deleted successfully.');
    }

    public function restore($id)
    {
        $personnel = \App\Models\User::withTrashed()->findOrFail($id);

        $personnel->restore();

        return redirect()->route('admin.personnel')
            ->with('success', 'Personnel restored successfully.');
    }

}
