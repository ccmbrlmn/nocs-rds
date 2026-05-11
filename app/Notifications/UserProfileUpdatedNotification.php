<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserProfileUpdatedNotification extends Notification
{
    use Queueable;

    public $user;

    public function __construct($user)
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
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'type' => 'profile_updated',
            'type_label' => 'Profile Updated',
            'is_admin' => in_array($this->user->role, ['admin', 'first_admin']),
            'message' => $this->user->name . ' updated their profile.',
        ];
    }
}
