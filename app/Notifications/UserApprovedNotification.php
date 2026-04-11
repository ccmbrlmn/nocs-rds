<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserApprovedNotification extends Notification
{
    use Queueable;

    public $approvedUser;
    public $admin;

    public function __construct($approvedUser, $admin)
    {
        $this->approvedUser = $approvedUser;
        $this->admin = $admin;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->approvedUser->id,
            'user_name' => $this->approvedUser->name,
            'admin_name' => $this->admin->name,
            'type' => 'user_approved',
            'type_label' => 'User Approved',
            'message' => 'Your account has been approved by admin',
        ];
    }
}
