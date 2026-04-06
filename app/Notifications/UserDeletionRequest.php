<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserDeletionRequest extends Notification
{
    use Queueable;

    public $requestingUser;

    public function __construct(User $user)
    {
        $this->requestingUser = $user;
    }

    public function via($notifiable)
    {
        return ['database']; // use database channel
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->requestingUser->id,
            'user_name' => $this->requestingUser->name,
            'type' => 'user_deletion_request',
            'is_admin' => $this->requestingUser->role === 'admin',
            'message' => "User {$this->requestingUser->name} requested account deletion.",
        ];
    }
}
