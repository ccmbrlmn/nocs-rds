<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\UserLog;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();
        
        UserLog::create([
            'user_id' => $user->id,
            'action' => 'profile_updated',
            'request_id' => null,
            'status' => 'Updated',
            'event_name' => 'Profile Updated',
        ]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Set pending deletion
        $user->deletion_status = 'pending';
        $user->save();
        
        UserLog::create([
            'user_id' => $user->id,
            'action' => 'user_delete_requested',
            'request_id' => null, // no request involved
            'status' => 'Pending',
            'event_name' => 'Account Deletion Requested',
        ]);

        if ($user->role !== 'first_admin') {

            \App\Models\User::whereIn('role', ['admin', 'first_admin'])
                ->where('id', '!=', $user->id) // don't notify self
                ->get()
                ->each(function ($admin) use ($user) {
                    $admin->notify(new \App\Notifications\UserDeletionRequest($user));
                });
        }

        $request->session()->flash('status', 'Your account deletion request has been sent. Wait for admin approval.');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
