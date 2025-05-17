<?php

namespace App\Filament\Resources\Catalog\ProductResource\Pages;

use App\Filament\Resources\Catalog\ProductResource;
use App\Filament\Resources\Catalog\ProductResource\Widgets\ProductStats;
use App\Filament\Resources\Catalog\ProductResource\Widgets\TopCustomerOrderedTable;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord {
    use Translatable;

    protected static string $resource = ProductResource::class;


    protected function getHeaderActions(): array {
        return [

            // Actions\LocaleSwitcher::make(),
        ];
    }

}
