<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum OrderTypeStatuses: string implements HasLabel {
    case INDIVIDUAL = 'individual';
    case FACILITY = 'facility';
    public function getLabel(): ?string {
        return __("panel.enums.order.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            'facility', => 'warning',
            'individual'=> 'primary',
        };

    }

}
