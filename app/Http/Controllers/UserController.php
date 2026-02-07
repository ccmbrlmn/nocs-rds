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
}

