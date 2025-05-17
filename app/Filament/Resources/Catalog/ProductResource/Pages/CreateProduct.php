<?php

namespace App\Filament\Resources\Catalog\ProductResource\Pages;

use App\Filament\Resources\Catalog\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateProduct extends CreateRecord {
    use Translatable;

    protected function getHeaderActions(): array {

        return [
            // Actions\LocaleSwitcher::make(),
        ];
    }

    protected static string $resource = ProductResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
