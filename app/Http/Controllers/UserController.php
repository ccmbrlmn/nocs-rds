<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Requests;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function logs(User $user)
    {
        $logs = Requests::where('requested_by', $user->id)
                    ->orWhere('handled_by', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('admin.user-logs', compact('user', 'logs'));
    }
    
    public function edit(User $user)
{
    // Only allow the first admin to edit
    $firstAdminId = User::where('role', 'admin')->orderBy('id')->first()->id ?? null;

    if (auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized action.');
    }

    return view('admin.user-edit', compact('user'));
}

public function update(Request $request, User $user)
{
    $firstAdminId = User::where('role', 'admin')->orderBy('id')->first()->id ?? null;

    if (auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
    ]);

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
    ]);

    return redirect()->route('admin.users')->with('success', 'User updated successfully.');
}

public function destroy(User $user)
{
    $firstAdminId = User::where('role', 'admin')->orderBy('id')->first()->id ?? null;

    if (auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized action.');
    }

    $user->delete();

    return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
}

public function approve(User $user)
{
    $firstAdminId = User::where('role', 'admin')
                        ->orderBy('id')
                        ->first()->id ?? null;

    if (auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized action.');
    }

    $user->update([
        'is_approved' => true
    ]);

    return redirect()->route('admin.users')
        ->with('success', 'User approved successfully.');
}

}

