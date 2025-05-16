<?php

namespace App\Notifications;

use App\Lib\Firebase;
use App\Lib\NotificationMessageParser;
use App\Models\DeviceToken;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderCreatedNotification extends Notification {
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
        $title = NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.order_received')
            ->adminMessage('panel.notifications.new_order_arrival')
            ->parse();
        $body = NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.order_received_body', ["ID" => $this->order->order_number])
            ->adminMessage('panel.notifications.new_order_arrival_pending_for_arrival', ['ID' => $this->order->order_number])
            ->parse();
        $tokens = DeviceToken::where('user_id', $notifiable->id)->pluck('token')->unique()->toArray();

        return Firebase::make()
            ->setTokens($tokens)
            ->setTitle($title[$notifiable->preferredLocale()])
            ->setBody($body[$notifiable->preferredLocale()])
            ->setMoreData([
                'entity_id' => "{$this->order->id}",
                'entity_type' => 'order',
            ]);
    }

    public function toArray($notifiable): array {
        $this->toFirebase($notifiable);

        $title = NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.order_received')
            ->adminMessage('panel.notifications.new_order_arrival')
            ->parse();
        $body = NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.order_received_body', ["ID" => $this->order->order_number])
            ->adminMessage('panel.notifications.new_order_arrival_pending_for_arrival', ['ID' => $this->order->order_number])
            ->parse();
        return [
            'title' => json_encode($title),
            'body' => json_encode($body),
            'format' => 'filament',
            'viewData' => [
                'entity_id' => $this->order->id,
                'entity_type' => 'order',
            ],
            'duration' => 'persistent'
        ];

    }
}
