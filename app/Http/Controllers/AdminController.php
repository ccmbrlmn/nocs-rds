<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Requests as UserRequest;
use App\Models\UserLog;

class AdminController extends Controller
{
    public function logs(User $admin)
    {
        $logs = UserRequest::where('handled_by', $admin->id)
                    ->orWhere('created_by', $admin->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('auth.admin-logs', compact('admin', 'logs'));
    }

    public function listUsers()
    {
        $users = User::where('role', 'user')->orderBy('created_at', 'desc')->get();

        return view('admin.user-list', compact('users'));
    }

}

