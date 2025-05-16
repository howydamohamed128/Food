<?php

namespace App\Filament\Resources\Content\BannerResource\Pages;

use App\Filament\Resources\Catalog\OptionResource;
use App\Filament\Resources\Content\BannerResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateBanner extends CreateRecord {
    use Translatable;

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

