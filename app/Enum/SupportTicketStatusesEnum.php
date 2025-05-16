<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum SupportTicketStatusesEnum: string implements HasLabel {
    case OPEN = 'open';
    case REPLIED = 'replied';
    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            'open', => 'primary',
            'replied'=> 'success',
        };

    }

}
