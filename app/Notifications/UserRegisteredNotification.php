<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserRegisteredNotification extends Notification
{
    use Queueable;

    public $user;
    public $actor;

    public function __construct(User $user, ?User $actor = null)
    {
        $this->user = $user;
        $this->actor = $actor;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'actor_name' => $this->actor?->name,
            'type' => 'user_registration',
            'type_label' => 'User Registration',
            'message' => $this->user->name . ' registered and is awaiting approval',
        ];
    }
}
