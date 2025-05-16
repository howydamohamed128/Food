<?php

namespace App\Filament\Resources\Catalog\CategoryResource\Pages;

use App\Filament\Resources\Catalog\CategoryResource;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    public function infolist($infolist): \Filament\Infolists\Infolist {
        return  $infolist->schema([
            TextEntry::make('name'),

            SpatieMediaLibraryImageEntry::make('avatar')
                ->label(__('forms.fields.image')),

            TextEntry::make('products_count')
                ->state(fn($record) => $record->products()->count()  )
                ->label(__('forms.fields.products_count')),
        ])->columns(1);

    }

}
