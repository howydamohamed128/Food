<?php

namespace App\Filament\Resources\Catalog;

use App\Enum\ModelStatus;
use App\Filament\Resources\Catalog;
use App\Models\Catalog\Category;
use App\Traits\Filament\HasTranslationLabel;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class CategoryResource extends Resource implements HasShieldPermissions {
    use Translatable;
    use HasTranslationLabel;

    protected static ?string $model = Category::class;
//    protected static string $view = 'filament.pages.listing.categories';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make("basic_information")
                    ->schema([
                        TextInput::make('name')->required(),
                    //    Forms\Components\Textarea::make('description')->required(),
                    //     RichEditor::make('description'),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->required(),



                        Toggle::make('status')
                            ->default(1)
                            ->onColor('success')
                            ->offColor('danger')
                    ])
            ])->columns(1);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                TextColumn::make('created_at')
                ->date('d/m/Y H:i:s'),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn(Category $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                           ->disabled(fn(Model $record): bool => !auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn(Category $record) => $record->toggleStatus())

                    ),

                // TextColumn::make('products_count')->state(fn(Category $record) => $record->products()->count()),
                // TextColumn::make('orders_count')->state(fn(Category $record) => $record->orders()->count()),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options(ModelStatus::class)
            ])
            ->actions([
                Tables\Actions\RestoreAction::make(),
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    static public function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('description'),
                        TextEntry::make('status')
                            ->formatStateUsing(fn(string $state): string => $state ? 'Yes' : 'No')
                            ->color(fn(string $state): string => match ($state) {
                                '1' => 'success',
                                '0' => 'danger',
                            })
                    ]),

            ]);
    }

    public static function getRelations(): array {
        return [
//            ChildrenRelationManager::class
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Catalog\CategoryResource\Pages\ListCategories::route('/'),
            'create' => Catalog\CategoryResource\Pages\CreateCategory::route('/create'),
            'edit' => Catalog\CategoryResource\Pages\EditCategory::route('/{record}/edit'),
            // 'view' => Catalog\CategoryResource\Pages\ViewCategory::route('/{record}'),
        ];
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.categories');
    }

    public static function getPermissionPrefixes(): array {
        return [
            'view_any',
            // 'view',
            'create',
            'update',
            'delete',
            'restore',
            'delete_any',
        ];
    }
}
