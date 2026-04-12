<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserApprovedNotification extends Notification
{
    use Queueable;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'user_approved',
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'actor_name' => auth()->user()->name,
            'message' => $this->user->name . ' was approved by ' . auth()->user()->name,
        ];
    }
}
