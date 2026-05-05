<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class RequestReturnNotification extends Notification
{
    use Queueable;

    public $user;
    public $requestId;
    public $requestName;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @param int $requestId
     * @param string $requestName
     */
    public function __construct(User $user, $requestId, $requestName)
    {
        $this->user = $user;
        $this->requestId = $requestId;
        $this->requestName = $requestName;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Store notification in database
     */
    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'request_id' => $this->requestId,
            'request_name' => $this->requestName,
            'type' => 'return_requested',
            'type_label' => 'Return Requested',
            'message' => $this->user->name . ' requested a return for: ' . $this->requestName,
        ];
    }
}
