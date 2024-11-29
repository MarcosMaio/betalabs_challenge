<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public $token;
    public $email;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     * @param string $email
     * @return void
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(config('app.url') . '/api/reset-password?token=' . $this->token . '&email=' . urlencode($this->email));

        return (new MailMessage)
            ->subject('Password Reset')
            ->line('You are receiving this email because you requested a password reset.')
            ->action('Reset Password', $url)
            ->line('If you did not request a reset, no action is required.');
    }
}
