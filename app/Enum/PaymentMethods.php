<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethods: string implements HasLabel {
    case CASH = 'cash';
    case TAMARA = 'tamara';
    case TABBY = 'tabby';
    case VISA_MASTER = 'VISA/MASTER';
    case KNET = 'KNET';
    case BANK_TRANSFER = 'bank_transfer';

    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            'VISA&MASTER','KNET' => 'warning',
            'cash'=> 'success',
            'default' => 'primary'
        };

    }

}
