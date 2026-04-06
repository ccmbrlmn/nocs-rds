<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class DeletionApproved extends Notification
{
    use Queueable;

    protected $userName;

    public function __construct($userName)
    {
        $this->userName = $userName;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    // Mail notification
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
                    ->subject('Account Deletion Approved')
                    ->line("Hello {$this->userName}, your account deletion request has been approved.")
                    ->action('Go to Homepage', url('/'))
                    ->line('Thank you for using our application!');
    }

    // Database notification
    public function toDatabase($notifiable)
    {
        DB::table('notifications')->insert([
            'user_id'    => $notifiable->id,
            'sender_id'  => null,
            'type'       => self::class,
            'message'    => "Your account deletion request has been approved.",
            'is_read'    => false,
            'data'       => json_encode([
                'user_id'   => $notifiable->id,
                'user_name' => $this->userName,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'user_id'   => $notifiable->id,
            'user_name' => $this->userName,
            'message'   => "Your account deletion request has been approved.",
            'is_read'   => false,
        ];
    }
}
