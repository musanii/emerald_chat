<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Invite $invite)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Points to your frontend web application route
        $acceptUrl = config('app.frontend_url', 'http://localhost:3000') . '/accept-invite?token=' . $this->invite->token;

        return (new MailMessage)
            ->subject('You have been invited to join the team')
            ->greeting('Hello!')
            ->line('An administrator has invited you to join the portal.')
            ->action('Accept Invitation & Set Password', $acceptUrl)
            ->line('This invitation link will expire in 7 days.')
            ->line('If you did not expect this invitation, you can ignore this email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
