<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestReturnAcceptedNotification extends Notification
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
            'request_id' => $this->request->id,
            'message' => 'Your return request has been accepted. Please wait for retrieval.',
        ];
    }
}
