<?php

namespace App\Notifications;

use App\Lib\Firebase;
use App\Lib\NotificationMessageParser;
use App\Models\DeviceToken;
use App\Models\Gift;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewGiftReceivedNotification extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Gift $gift) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array {
        return ['database'];
    }


    public function toFirebase($notifiable) {
        $tokens = DeviceToken::where('user_id', $notifiable->id)->pluck('token')->unique()->toArray();

        return Firebase::make()
            ->setTokens($tokens)
            ->setTitle($this->getTitle($notifiable)[$notifiable->preferredLocale()])
            ->setBody($this->getBody($notifiable)[$notifiable->preferredLocale()])
            ->setMoreData([
                'entity_id' => "{$this->gift->id}",
                'entity_type' => 'gift',
            ]);
    }

    public function toArray($notifiable): array {
        $this->toFirebase($notifiable);


        return [
            'title' => json_encode($this->getTitle($notifiable)),
            'body' => json_encode($this->getBody($notifiable)),
            'format' => 'filament',
            'viewData' => [
                'entity_id' => $this->gift->id,
                'entity_type' => 'gift',
            ],
            'duration' => 'persistent'
        ];

    }

    public function getTitle($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.gift_received')
            ->adminMessage('panel.notifications.gift_received')
            ->parse();
    }

    public function getBody($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.gift_received_body', ['ID' => $this->gift->id, 'sender_name' => $this->gift?->sender?->name, 'amount' => $this->gift?->price])
            ->adminMessage('panel.notifications.gift_received_body', ['ID' => $this->gift->id, 'sender_name' => $this->gift?->sender?->name, 'amount' => $this->gift?->price])
            ->parse();
    }
}
