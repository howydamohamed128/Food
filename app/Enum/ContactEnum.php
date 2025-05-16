<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum ContactEnum: string implements HasLabel {
    case CUSTOMER = 'customer';
    case WORKER = 'worker';
    case ADMIN = 'admin';


    public function getLabel(): ?string {
        return __("panel.enums.$this->name");
    }


}
