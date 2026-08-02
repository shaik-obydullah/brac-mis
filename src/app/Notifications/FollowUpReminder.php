<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FollowUpReminder extends Notification
{
    use Queueable;

    public function __construct(
        public $followUp,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $model = class_basename($this->followUp);
        return (new MailMessage)
            ->subject("Follow-up Reminder: {$model}")
            ->line("You have a scheduled follow-up on {$this->followUp->next_date}.")
            ->action('View Details', url('/'))
            ->line('Thank you for using BRAC MIS.');
    }
}
