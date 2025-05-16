<?php

namespace App\Lib;

use App\Models\Customer;
use App\Models\User;
use App\Notifications\Notification;

class NotificationMessageParser {
    public $adminMessage = null;
    public $mangaerMessage = null;
    public $customerMessage = null;
    private User $notifiable;

    public function __construct(User $notifiable) {
        $this->notifiable = $notifiable;
    }

    public static function init(User $notifiable) {
        return new static($notifiable);
    }

    public function adminMessage($text, $params = []): static {
        $this->adminMessage = Utils::convertStringToArrayLanguage($text, $params);
        return $this;
    }

    public function managerMessage($text, $params = []): static {
        $this->mangaerMessage = Utils::convertStringToArrayLanguage($text, $params);
        return $this;
    }

    public function customerMessage($text, $params = []): static {
        $this->customerMessage = Utils::convertStringToArrayLanguage($text, $params);
        return $this;
    }

    public function parse() {

        return match ($this->notifiable->roles()?->latest()?->first()->name) {
            Customer::ROLE,'panel_user' => $this->customerMessage,
            default => $this->adminMessage,
        };

    }

}
