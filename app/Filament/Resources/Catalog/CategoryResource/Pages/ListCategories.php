<?php

namespace App\Filament\Resources\Catalog\CategoryResource\Pages;

use App\Filament\Resources\Catalog\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords {

    protected static string $resource = CategoryResource::class;

//    protected static string $view = 'filament.pages.listing.categories';

    protected function getHeaderActions(): array {
        return [
            Actions\CreateAction::make(),
//            Actions\LocaleSwitcher::make(),
        ];
    }


    // protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder {
    //     return parent::getTableQuery()->parent();
    // }

}
