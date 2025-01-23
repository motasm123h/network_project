<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnlockFileNotification extends Notification
{
    use Queueable;

    private $notificationData;

    public function __construct($notificationData)
    {
        $this->notificationData = $notificationData;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'file_id' => $this->notificationData['file_id'],
            'message' => $this->notificationData['message'],
        ];
    }
}
