<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A simple test mail notification used to verify mail transport configuration.
 */
class TestMailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly ?User $user = null)
    {
    }

    /**
     * Get the notification's delivery channels.
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
        $appName = (string)config('app.name');
        $name = $this->user?->name ?? (property_exists($notifiable, 'name') ? (string)$notifiable->name : '');

        return (new MailMessage)
            ->subject('Test email from ' . $appName)
            ->greeting('Hello ' . $name)
            ->line('This is a quick test email to confirm delivery. If you received this, SMTP is working correctly for ' . $appName . '.')
            ->salutation('Regards, ' . $appName);
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
