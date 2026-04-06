<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

public function storeAdmin(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed|min:8',
        'admin_key' => 'required|string',
    ]);

    if ($request->admin_key !== config('app.admin_register_key')) {
        return back()->withErrors([
            'admin_key' => 'Invalid admin registration key.',
        ]);
    }

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'admin',
        'created_by' => auth()->id() ?? null,
        'is_approved' => 1,                  
    ]);

    return redirect('/login')->with('success', 'Admin account created successfully.');
}

public function storeFirstAdmin(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed|min:8',
        'admin_key' => 'required|string',
    ]);

    if ($request->admin_key !== config('app.admin_register_key')) {
        return back()->withErrors([
            'admin_key' => 'Invalid admin registration key.',
        ]);
    }

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'admin',
        'is_approved' => 1,
        'created_by' => null, // FIRST admin has no creator
    ]);

    return redirect('/login')->with('success', 'First admin account created successfully.');
}
