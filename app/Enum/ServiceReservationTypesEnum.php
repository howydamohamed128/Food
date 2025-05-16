<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum ServiceReservationTypesEnum: string implements HasLabel {
    case TIMED = 'timed';
    case NOT_TIMED = 'not_timed';
    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            'timed', => 'warning',
            'NOT_TIMED'=> 'success',
        };

    }

}
