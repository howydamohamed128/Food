<?php

namespace App\Notifications;

use App\Lib\Firebase;
use App\Lib\NotificationMessageParser;
use App\Models\DeviceToken;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderPaidNotification extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order) {
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
                'entity_id' => "{$this->order->id}",
                'entity_type' => 'order',
            ]);
    }

    public function toArray($notifiable): array {
        $this->toFirebase($notifiable);


        return [
            'title' => json_encode($this->getTitle($notifiable)),
            'body' => json_encode($this->getBody($notifiable)),
            'format' => 'filament',
            'viewData' => [
                'entity_id' => $this->order->id,
                'entity_type' => 'order',
            ],
            'duration' => 'persistent'
        ];

    }

    public function getTitle($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.order_paid', ['id' => $this->order->id])
            ->adminMessage('panel.notifications.order_paid', ['id' => $this->order->id])
            ->parse();
    }

    public function getBody($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.order_paid_body', ["order_number" => $this->order->order_number, 'customer_name' => $this->order->customer->name])
            ->adminMessage('panel.notifications.order_paid_body', ["order_number" => $this->order->order_number, 'customer_name' => $this->order->customer->name])
            ->parse();
    }
}
