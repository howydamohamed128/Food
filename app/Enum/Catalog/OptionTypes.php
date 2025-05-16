<?php

namespace App\Enum\Catalog;

use Filament\Support\Contracts\HasLabel;

enum OptionTypes: string implements  HasLabel {
    case TEXTAREA = 'textarea';
    case RADIO = 'radio';
    case CHECKBOX = 'checkbox';
    case DATE   = 'date';

    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }
}
