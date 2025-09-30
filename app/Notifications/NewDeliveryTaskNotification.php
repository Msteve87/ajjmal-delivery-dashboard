<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use NotificationChannels\Fcm\FcmChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kreait\Firebase\Messaging\CloudMessage;

use Kreait\Firebase\Messaging\WebPushConfig;
use Illuminate\Notifications\Messages\MailMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class NewDeliveryTaskNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public \App\Models\SubOrder $subOrder)
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
        return [FcmChannel::class];

    }

    public function toFcm($notifiable)
    {
        $messaging = (new \Kreait\Firebase\Factory)
            ->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'))
            ->createMessaging();

        return CloudMessage::new()
            ->withTarget('token', $notifiable->routeNotificationForFcm())
            ->withNotification(FirebaseNotification::create(
                'New Order',
                'You have a new delivery task',
            ))
            ->withData([
                'tracking_id' => $this->subOrder->tracking_id,
            ]);
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
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
