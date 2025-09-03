<?php

namespace App\Notifications;

use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Generic user message notification supporting mail and database channels.
 */
class UserMessageNotification extends Notification
{
    use Queueable;

    /**
     * @param array<int, string> $channels e.g. ['mail'] or ['database']
     */
    public function __construct(
        public readonly string $subject,
        public readonly string $body,
        public readonly array  $channels = ['database'],
    )
    {
    }

    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = property_exists($notifiable, 'name') ? (string)$notifiable->name : '';
        $appName = (string)config('app.name');

        return (new MailMessage)
            ->subject($this->subject)
            ->view('mail.user-message', [
                'name' => $name,
                'appName' => $appName,
                'html' => $this->body,
                'subject' => $this->subject,
            ]);
    }

    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->title($this->subject)
            ->body($this->body)
            ->getDatabaseMessage();
    }

    /**
     * Payload saved to the notifications table for the database channel.
     *
     * @return array{subject:string,body:string}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => $this->subject,
            'body' => $this->body,
        ];
    }
}
