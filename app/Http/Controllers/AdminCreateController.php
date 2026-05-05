<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use PDF;

class AdminCreateController extends Controller
{
    public function create()
    {
        return view('auth.create-admin');
    }

    public function store(Request $request)
    {
        $firstAdminId = \App\Models\User::where('role', 'admin')
        ->orderBy('id')
        ->first()
        ->id;

        if (auth()->id() !== $firstAdminId) {
            abort(403, 'Only the first admin can create other admins.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                \Illuminate\Validation\Rule::unique('users')->whereNull('deleted_at'),
                function ($attribute, $value, $fail) {

                    $allowedEmails = config('services.allowed_admin_emails');

                    if (!in_array($value, $allowedEmails)) {
                        $fail('Only authorized NOCS admin emails are allowed.');
                    }
                }
            ],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ],
            'office' => 'nullable|string|max:255',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'office' => $request->office,
            'created_by' => auth()->id(),
        ]);
        
        \App\Models\UserLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_created',
        ]);
        
        \App\Models\UserLog::create([
            'user_id' => $admin->id,
            'action' => 'account_registered',
        ]);

        return redirect()->route('admin.dashboard')
                         ->with('success', 'New admin created successfully.');
    }
    
    public function indexCreatedAdmins(Request $request)
    {
        $query = User::withTrashed()
                 ->where('role', 'admin')
                 ->where('created_by', auth()->id());

        if ($request->filled('status') && $request->status !== 'All') {
            switch (strtolower($request->status)) {
                case 'active':
                    $query->whereNull('deleted_at');
                    break;

                case 'deleted':
                    $query->onlyTrashed();
                    break;
            }
        }

        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case '30_days':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
                case '7_days':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case '24_hours':
                    $query->where('created_at', '>=', now()->subHours(24));
                    break;
            }
        }

        if ($request->filled('specific_date')) {
            $query->whereDate('created_at', $request->specific_date);
        }

        $sort = $request->get('sort', 'desc');
        $query->orderBy('created_at', $sort);

        $admins = $query->get();

        return view('auth.admin-list', compact('admins'));
    }

    public function edit($id)
    {
        $admin = User::withTrashed()
                     ->where('id', $id)
                     ->where('role', 'admin')
                     ->firstOrFail();

        if ($admin->deleted_at) {
            return redirect()->back()
                ->with('error', 'Restore the admin first before editing.');
        }

        return view('auth.edit-admin', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = User::where('id', $id)
                     ->where('role', 'admin')
                     ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'office' => 'nullable|string|max:255',
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'office' => $request->office
        ]);

        return redirect()->route('admin.created-admins')
                         ->with('success', 'Admin updated successfully.');
    }

    public function destroy($id)
    {
        $admin = User::where('id', $id)
                     ->where('role', 'admin')
                     ->firstOrFail();

        if (auth()->id() === $admin->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $firstAdminId = User::where('role', 'admin')
                            ->orderBy('id')
                            ->first()
                            ->id;

        if (auth()->id() !== $firstAdminId) {
            abort(403);
        }

        $admin->delete();

        return back()->with('success', 'Admin deleted successfully.');
    }

    public function exportPdf(Request $request)
    {
        $query = User::withTrashed()
                     ->where('role', 'admin')
                     ->where('created_by', auth()->id());

        if ($request->filled('status')) {
            switch (strtolower($request->status)) {
                case 'deleted':
                    $query->onlyTrashed();
                    break;
                case 'active':
                    $query->whereNull('deleted_at');
                    break;
            }
        }

        if ($request->filled('specific_date')) {
            $query->whereDate('created_at', $request->specific_date);
        } elseif ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case '30_days': $query->where('created_at', '>=', now()->subDays(30)); break;
                case '7_days':  $query->where('created_at', '>=', now()->subDays(7)); break;
                case '24_hours': $query->where('created_at', '>=', now()->subHours(24)); break;
            }
        }

        $sort = $request->sort ?? 'desc';
        $admins = $query->orderBy('created_at', $sort)->get();

        $statusLabel = $request->status ?? 'All';
        $dateLabel   = $request->specific_date ?? ($request->date_filter ?? 'All Time');
        $sortLabel   = $sort === 'asc' ? 'Oldest First' : 'Newest First';
        $exportedAt  = now()->format('M d, Y - h:i A');

        return Pdf::loadView('admin.admin-pdf', compact('admins', 'statusLabel', 'dateLabel', 'sortLabel', 'exportedAt'))
                  ->setPaper('a4', 'landscape')
                  ->download('admin-report.pdf');
    }

    public function exportCsv(Request $request)
    {
        $query = User::withTrashed()
                     ->where('role', 'admin')
                     ->where('created_by', auth()->id());

        if ($request->filled('status')) {
            switch (strtolower($request->status)) {
                case 'deleted':
                    $query->onlyTrashed();
                    break;
                case 'active':
                    $query->whereNull('deleted_at');
                    break;
            }
        }

        if ($request->filled('specific_date')) {
            $query->whereDate('created_at', $request->specific_date);
        } elseif ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case '30_days': $query->where('created_at', '>=', now()->subDays(30)); break;
                case '7_days':  $query->where('created_at', '>=', now()->subDays(7)); break;
                case '24_hours': $query->where('created_at', '>=', now()->subHours(24)); break;
            }
        }

        $sort = $request->sort ?? 'desc';
        $admins = $query->orderBy('created_at', $sort)->get();

        $filename = "admin-report.csv";
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($admins) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'Name', 'Email', 'Created At', 'Status']);

            foreach ($admins as $admin) {
                fputcsv($handle, [
                    $admin->id,
                    $admin->name,
                    $admin->email,
                    $admin->created_at,
                    $admin->deleted_at ? 'Deleted' : 'Active'
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function restore($id)
    {
        $admin = User::withTrashed()
                     ->where('id', $id)
                     ->where('role', 'admin')
                     ->firstOrFail();
        $admin->restore();
        
        return back()->with('success', 'Admin restored successfully.');
    }
}
