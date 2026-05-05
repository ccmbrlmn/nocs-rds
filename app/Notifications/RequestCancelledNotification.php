<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestCancelledNotification extends Notification
{
    use Queueable;

    protected $request;

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
            'message' => "Your request '{$this->request->event_name}' was cancelled. Reason: {$this->request->cancel_reason}",
            'type' => 'request_cancelled',
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->name,
            'actor_name' => auth()->user()->name,
            'request_id' => $this->request->id,
            'request_name' => $this->request->event_name,
            'type' => 'request_cancelled',
        ];
    }
}
