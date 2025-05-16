<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Lib\Firebase;
use App\Lib\NotificationMessageParser;
use App\Models\Catalog\Product;
use App\Models\DeviceToken;
use App\Models\Order;

class OTPCodeSentNotification extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $code) {
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

            ]);
    }

    public function toArray($notifiable): array {
        $this->toFirebase($notifiable);


        return [
            'title' => json_encode($this->getTitle($notifiable)),
            'body' => json_encode($this->getBody($notifiable)),
            'format' => 'filament',
            'viewData' => [

            ],
            'duration' => 'persistent'
        ];

    }

    public function getTitle($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.otp_code_sent_title')
            ->parse();
    }

    public function getBody($notifiable) {

        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.otp_code_sent_body')
            ->parse();
    }
}
