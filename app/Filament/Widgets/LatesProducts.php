<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Enum\OrderStatus;
use App\Models\Catalog\Product;
use App\Models\Order;

class LatesProducts extends BaseWidget {
    use HasWidgetShield;

    protected static ?int $sort = 5;
    public function table(Table $table): Table {
        return $table
            ->heading(__('sections.latest_products'))
            ->description(__('sections.latest_products_description'))
            ->query(
                Product::query()
                    ->latest()
                    ->limit(10)
            )
            ->emptyStateHeading(__('sections.no_data'))
            ->columns([
                TextColumn::make('index')->rowIndex()->toggleable(false),
                Tables\Columns\TextColumn::make('title')->toggleable(false),
                Tables\Columns\TextColumn::make('price')->toggleable(false),
                Tables\Columns\TextColumn::make('created_at')->label(__('forms.fields.created_at'))->dateTime()->toggleable(false),
            ]);
    }
    public function getTableHeading(): ?string {
        return __('sections.latest_products');
    }

}