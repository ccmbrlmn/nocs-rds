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

}

