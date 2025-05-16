<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum ProductTypes: string implements HasLabel {
    case PLAN = 'plan';
    case SERVICE = 'service';
    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            'plan', => 'primary',
            'service'=> 'success',
        };

    }

}
