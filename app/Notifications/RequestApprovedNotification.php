<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class RequestApprovedNotification extends Notification
{
    use Queueable;

    public $request;
    public $actor;

    /**
     * Create a new notification instance.
     *
     * @param mixed $request
     * @param User $actor (admin who approved)
     */
    public function __construct($request, User $actor)
    {
        $this->request = $request;
        $this->actor = $actor;
    }

    /**
     * Delivery channels
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'request_approved',

            'request_id' => $this->request->id,
            'request_name' => $this->request->event_name,

            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,

            'user_id' => $notifiable->id,
            'user_name' => $notifiable->name,

            'message' => "Your request \"{$this->request->event_name}\" has been approved.",
        ];
    }

    /**
     * Fallback array representation
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'request_approved',

            'request_id' => $this->request->id,
            'request_name' => $this->request->event_name,

            'actor_name' => $this->actor->name,
        ];
    }
}
