<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum OrderPaymentStatus: string implements HasLabel {
    case PENDING = 'pending';
    case PAID = 'paid';
    case PAID_PARTIALLY = 'paid_partially';
    case REFUNDED = 'refunded';
    // يدوي
    case MANUAL ='manual';


    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }
    public function getColor(): string {
        return match ($this->value) {
            'pending','paid_partially' => 'warning',
            'refunded' => 'danger',
            'paid' ,'manual'=> 'success',
        };
    }
}