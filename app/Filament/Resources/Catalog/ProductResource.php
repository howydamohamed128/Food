<?php

namespace App\Filament\Resources\Catalog;

use App\Enum\ModelStatus;
use App\Enum\ServiceReservationTypesEnum;
use App\Enum\ServiceTypesEnum;
use App\Filament\Resources\Catalog\ProductResource\Pages;
use App\Filament\Resources\Catalog\ProductResource\RelationManagers\addsRelationManager;
use App\Filament\Resources\Catalog\ProductResource\RelationManagers\OptionRelationManager;
use App\Filament\Resources\Catalog\ProductResource\RelationManagers\TopCustomersRelationManager;
use App\Filament\Resources\Catalog\ProductResource\RelationManagers\WorkersRelationManager;
use App\Models\Add;
use App\Models\Branch;
use App\Models\Catalog\Allergen;
use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use App\Models\Catalog\Service;
use App\Models\Option;
use App\Models\ServiceType;
use App\Models\Worker;
use App\Traits\Filament\HasTranslationLabel;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ProductResource extends Resource
{
    use Translatable;
    use HasTranslationLabel;

    protected static ?int $navigationSort = 4;
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make("general")->schema([
                    SpatieMediaLibraryFileUpload::make('image')->required(),
                    TextInput::make('title')
                        ->required()
                        ->translateLabel(),
                    Forms\Components\MarkdownEditor::make('description')
                        ->required()
                        ->translateLabel(),

                    Forms\Components\Select::make('category_id')
                        ->relationship('category')
                        ->required()
                        ->options(fn() => Category::pluck('name', 'id')->toArray())
                        ->searchable(['name->ar', 'name->en']),


                    TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->translateLabel(),
                    Toggle::make('status')->default(1)
                        ->onColor('success')
                        ->offColor('danger')
                ])->columnSpan(1),

            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                SpatieMediaLibraryImageColumn::make('image'),
                TextColumn::make('title')->searchable(['title->ar', 'title->en']),
                TextColumn::make('category.name')->label(__('forms.fields.category')),
                Tables\Columns\TextColumn::make("price"),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn(Product $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn(Model $record): bool => !auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn(Product $record) => $record->toggleStatus())
                    ),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('category_id')
                    ->options(fn() => Category::pluck('name', 'id')->toArray())
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name),
                SelectFilter::make('status')->options(ModelStatus::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('basic_information')
                ->schema([
                    TextEntry::make('id'),
                    TextEntry::make('title'),
                    TextEntry::make('description'),
                    TextEntry::make('category.name')->label(__("forms.fields.category_id")),
                    TextEntry::make('price'),
                    TextEntry::make('created_at')->dateTime(),
                    SpatieMediaLibraryImageEntry::make('image')
                        ->label(__('forms.fields.image')),
                ])->columns(3),



        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),

        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.products');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


}
