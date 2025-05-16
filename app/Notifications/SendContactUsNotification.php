<?php

namespace App\Notifications;

use App\Models\Content\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Lib\Firebase;
use App\Models\DeviceToken;
use App\Lib\NotificationMessageParser;

class SendContactUsNotification extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     *
     */

    private  Contact $contact;
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }


    public function via() {
        return ['database'];
    }

    public function toFirebase($notifiable)
    {
        $tokens = DeviceToken::where('user_id', $notifiable->id)->pluck('token')->unique()->toArray();
        return Firebase::make()
            ->setTokens($tokens)
            ->setTitle($this->getTitle($notifiable)[$notifiable->preferredLocale()])
            ->setBody($this->getBody($notifiable)[$notifiable->preferredLocale()])
            ->setMoreData([ 'entity_type' => 'contact_us',
            'entity_id' => $this->contact->id])
            ->do();
    }
    public function toArray($notifiable): array
    {
        $this->toFirebase($notifiable);
        return [
            'title' => json_encode($this->getTitle($notifiable)),
            'body' => json_encode($this->getBody($notifiable)),
            'format' => 'filament',
            'viewData' => [ 'entity_type' => 'contact_us',
            'entity_id' => $this->contact->id],
            'duration' => 'persistent'
        ];
    }

    


    public function getTitle($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.new_contact_us_title', ['id' => $this->id])
            ->adminMessage('panel.notifications.new_contact_us_title', ['id' => $this->id])
            ->parse();
    }

    public function getBody($notifiable) {
        return NotificationMessageParser::init($notifiable)
            ->customerMessage('panel.notifications.new_contact_us_body:name', ["name" => $this->contact->name])
            ->adminMessage('panel.notifications.new_contact_us_body:name', ["name" => $this->contact->name])
            ->parse();
    }
}