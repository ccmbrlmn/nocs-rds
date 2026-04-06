<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Messages\MailMessage;

class DeletionDeclined extends Notification
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

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Account Deletion Declined')
                    ->line("Hello {$this->userName}, your account deletion request has been declined.")
                    ->action('Go to Homepage', url('/'))
                    ->line('If you have questions, contact the admin.');
    }

    public function toDatabase($notifiable)
    {
        DB::table('notifications')->insert([
            'user_id'    => $notifiable->id,
            'sender_id'  => null,
            'type'       => self::class,
            'message'    => "Your account deletion request has been declined.",
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
            'message'   => "Your account deletion request has been declined.",
            'is_read'   => false,
        ];
    }
}
