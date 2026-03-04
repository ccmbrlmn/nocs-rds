<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminCreateController extends Controller
{
    public function create()
    {
        return view('auth.create-admin');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.dashboard')
                         ->with('success', 'New admin created successfully.');
    }
    
    public function indexCreatedAdmins()
{
    $admins = User::where('role', 'admin')
                  ->where('created_by', auth()->id())
                  ->get();

    return view('auth.admin-list', compact('admins'));
}

public function edit($id)
{
    $admin = User::where('id', $id)
                 ->where('role', 'admin')
                 ->firstOrFail();

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
    ]);

    $admin->update([
        'name' => $request->name,
        'email' => $request->email,
    ]);

    return redirect()->route('admin.created-admins')
                     ->with('success', 'Admin updated successfully.');
}

public function destroy($id)
{
    $admin = User::where('id', $id)
                 ->where('role', 'admin')
                 ->firstOrFail();

    // Prevent deleting yourself
    if (auth()->id() === $admin->id) {
        return back()->with('error', 'You cannot delete your own account.');
    }

    // Allow only FIRST admin to delete
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

}

