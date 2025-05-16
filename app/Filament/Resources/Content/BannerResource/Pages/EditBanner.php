<?php

namespace App\Filament\Resources\Content\BannerResource\Pages;

use App\Filament\Resources\Content\BannerResource;
use Filament\Resources\Pages\EditRecord;

class EditBanner extends EditRecord {
    use EditRecord\Concerns\Translatable;

    protected static string $resource = BannerResource::class;


    protected function getHeaderActions(): array {
        return [
//            Actions\LocaleSwitcher::make(),

        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}
