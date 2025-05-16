<?php

namespace App\Notifications;

use App\Lib\Firebase;
use App\Lib\NotificationMessageParser;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\DeviceToken;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CustomerUserCouponNotification extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Customer $customer,public  Coupon $coupon) {
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
        $title = $this->getTitle($notifiable);
        $body = $this->getBody($notifiable);
        $tokens = DeviceToken::where('user_id', $notifiable->id)->pluck('token')->unique()->toArray();
        return Firebase::make()
            ->setTokens($tokens)
            ->setTitle($title[$notifiable->preferredLocale()])
            ->setBody($body[$notifiable->preferredLocale()])
            ->setMoreData([
                'entity_id' => "{$this->coupon->id}",
                'entity_type' => 'coupon',
            ]);
    }

    public function toArray($notifiable): array {
        $this->toFirebase($notifiable);

        return [
            'title' => json_encode($this->getTitle($notifiable)),
            'body' => json_encode($this->getBody($notifiable)),
            'format' => 'filament',
            'viewData' => [
                'entity_id' => $this->coupon->id,
                'entity_type' => 'coupon',
            ],
            'duration' => 'persistent'
        ];

    }
    public function getTitle($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.customer_user_coupon')
            ->adminMessage('panel.notifications.customer_user_coupon')
            ->parse();
    }

    public function getBody($notifiable) {
        return  NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.customer_user_coupon_body',['customer_name' => $this->customer->name, 'coupon_code' => $this->coupon->code])
            ->adminMessage('panel.notifications.customer_user_coupon_body', ['customer_name' => $this->customer->name, 'coupon_code' => $this->coupon->code])
            ->parse();
    }
}
