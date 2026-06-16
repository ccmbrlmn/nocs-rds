<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class RequestCreatedNotification extends Notification
{
    use Queueable;

    public $user;
    public $requestId;
    public $eventName;
    public $type;

    public function __construct(User $user, $requestId, $eventName, $type = 'created')
    {
        $this->user = $user;
        $this->requestId = $requestId;
        $this->eventName = $eventName;
        $this->type = $type;
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
            'requester_name' => $this->user->name,
            'request_id' => $this->requestId,
            'request_name' => $this->eventName,
            'type' => $this->type,
            'type_label' => ucfirst($this->type),
            'message' => $this->user->name . ' ' . $this->type . ' a request: ' . $this->eventName,
        ];
    }
}
