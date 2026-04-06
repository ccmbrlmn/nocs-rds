<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
     
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:'.User::class,
                'regex:/^[\w\.\-]+@gbox\.adnu\.edu\.ph$/i',
            ],
            'office' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.regex' => 'You can only register with your gbox.adnu.edu.ph email.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'office' => $request->office,
            'password' => Hash::make($request->password),
            'is_approved' => false,
            'role' => 'user', // ensure new accounts default to 'user'
        ]);

        event(new Registered($user));

        return redirect(route('dashboard', absolute: false))
               ->with('success', 'Account created successfully! Please wait for admin approval.');
    }
    
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'department' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'admin_key' => ['required'],
        ]);

        if ($request->admin_key !== config('app.admin_register_key')) {
            return back()->withErrors([
                'admin_key' => 'Invalid admin registration key.'
            ])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->department,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        Auth::login($user);

        return redirect()->route('admin-dashboard')
            ->with('success', 'Admin account created successfully.');
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
        'created_by' => null,
    ]);

    return redirect('/login')->with('success', 'First admin account created successfully.');
}
}
