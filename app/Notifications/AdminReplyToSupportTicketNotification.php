<?php

namespace App\Notifications;

use App\Lib\Firebase;
use App\Lib\NotificationMessageParser;
use App\Models\DeviceToken;
use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminReplyToSupportTicketNotification extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public SupportTicket $supportTicket) {
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
                'entity_id' => "{$this->supportTicket->id}",
                'entity_type' => 'support_ticket',
            ]);
    }

    public function toArray($notifiable): array {
        $this->toFirebase($notifiable);

        return [
            'title' => json_encode($this->getTitle($notifiable)),
            'body' => json_encode($this->getBody($notifiable)),
            'format' => 'filament',
            'viewData' => [
                'entity_id' => $this->supportTicket->id,
                'entity_type' => 'support_ticket',
            ],
            'duration' => 'persistent'
        ];

    }
    public function getTitle($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.admin_reply_to_support_ticket',['id' => $this->supportTicket->id])
            ->parse();
    }

    public function getBody($notifiable) {
        return  NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.admin_reply_to_support_ticket_body',['id' => $this->supportTicket->id])

            ->parse();
    }
}
