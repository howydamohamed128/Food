<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel {
    // case NEW = 'new';
    case PROCESSING = 'processing';
    case WORKING_ON_IT = 'working_on_it';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';

    public function getLabel($lang = null): ?string {
        $lang ??= app()->getLocale();
        return __("panel.enums.$this->value", [], $lang);
    }

    public function getColor(): string {
        return match ($this->value) {
            'processing','working_on_it',  => 'warning',
             'completed' => 'success',
            'canceled', => 'danger',
        };

    }


}