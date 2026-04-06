<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestAcceptedNotification extends Notification
{
    use Queueable;

    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->name,
            'request_id' => $this->request->id,
            'request_name' => $this->request->event_name,
            'type' => 'request_accepted',
            'message' => 'Your request "' . $this->request->event_name . '" has been accepted.',
        ];
    }
}
