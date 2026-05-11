<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class RequestEditedNotification extends Notification
{
    use Queueable;

    public $user;
    public $requestId;
    public $requestName;
    public $type;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @param int $requestId
     * @param string $requestName
     * @param string $type Optional type (default 'edited')
     */
    public function __construct(User $user, $requestId, $requestName, $type = 'edited')
    {
        $this->user = $user;
        $this->requestId = $requestId;
        $this->requestName = $requestName;
        $this->type = $type;
        
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
public function toDatabase($notifiable)
{
    return [
        'user_id' => $this->user->id,
        'user_name' => $this->user->name,
        'request_id' => $this->requestId,
        'request_name' => $this->requestName,
        'type' => $this->type,
        'type_label' => match ($this->type) {
            'edited' => 'Edited Request',
            default => ucfirst($this->type),
        },
        'message' => $this->user->name . ' ' . $this->type . ' a request: ' . $this->requestName,
    ];
}
}
