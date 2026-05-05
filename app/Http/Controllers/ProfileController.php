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

    $original = $user->getOriginal();

    $data = $request->validated();

    $changes = [];

    foreach ($data as $field => $newValue) {
        $oldValue = $original[$field] ?? null;

        if ($oldValue != $newValue) {
            $changes[$field] = [
                'old' => $oldValue ?? 'N/A',
                'new' => $newValue
            ];
        }
    }

    $user->fill($data);

    if (isset($data['email']) && $user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();
    
    $descriptionArray = $changes;

    UserLog::create([
        'actor_id' => $user->id,
        'user_id' => $user->id,
        'action' => 'profile_updated',
            'description' => json_encode($descriptionArray),
        'request_id' => null,
        'status' => 'Updated',
        'event_name' => 'Profile Updated',
    ]);

    return Redirect::route('profile.edit');
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

        $user->deletion_status = 'pending';
        $user->save();
        
        UserLog::create([
            'actor_id' => $user->id,
            'user_id' => $user->id,
            'action' => 'user_delete_requested',
            'request_id' => null,
            'status' => 'Pending',
            'event_name' => 'Account Deletion Requested',
        ]);

        if ($user->role !== 'first_admin') {

            $firstAdmin = \App\Models\User::where('role', 'admin')
                ->orderBy('created_at')
                ->first();

            if ($firstAdmin && $firstAdmin->id !== $user->id) {
                $firstAdmin->notify(new \App\Notifications\UserDeletionRequest($user));
            }
        }

        $request->session()->flash('status', 'Your account deletion request has been sent. Wait for admin approval.');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
