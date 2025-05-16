<?php

namespace App\Http\Resources\Api\Customer;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enum\CustomerTypeStatuses;
use App\Enum\GenderEnum;
use App\Http\Resources\Api\Shared\ZoneResource;

class CustomerResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'avatar' => $this->getFirstMediaUrl(),
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email ?? '',
            'phone' => $this->phone,
            'balance' => $this->balance,

            'api_token' => $this->api_token,
            'notification_status' => $this->settings['notification_status'] ?? 1,
            'preferred_language' => $this->settings['preferred_language'] ?? 'ar',
            "phone_verified" => (int)!is_null($this->phone_verified_at),
            'unread_notifications_count' => $this->unreadNotifications()->count(),
        ];
    }
}
