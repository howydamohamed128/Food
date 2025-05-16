<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum UserStatus: int implements HasLabel {
    case ACTIVE = 1;
    case IN_ACTIVE =0;


    public function getLabel(): ?string {
        return __("panel.enums.$this->name");
    }
    public function getColor(): string {
        return match ($this->value) {
            1 => 'success',
            0 => 'error',
        };
    }

}
