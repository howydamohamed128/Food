<?php

namespace App\Filament\Pages\Settings;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\Catalog\BranchResource;
use App\Forms\Components\SelectFontAwesomeIcon;
use App\Models\Content\Page;
use App\Settings\GeneralSettings;
use App\Traits\Filament\HasTranslationLabel;

class ManageGeneral extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $settings = GeneralSettings::class;
    protected static ?string $slug = 'settings/general';
    protected static ?int $navigationSort = 1;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make("General")->schema([
                    FileUpload::make('app_logo'),
                    FileUpload::make('fav_icon')->label(__('forms.fields.favicon')),

                    TextInput::make('app_name')
                        ->required(),
                    TextInput::make('app_email')
                        ->email()
                        ->required(),
                    TextInput::make('app_phone')
                        ->type('number')
                        ->numeric()
                        ->required(),
                    TextInput::make('app_mobile')
                        ->type('number')
                        ->numeric()
                        ->required(),
                    TextInput::make('app_whatsapp')
                        ->type('number')
                        ->numeric()
                        ->required(),

                ]),
              

                Forms\Components\Section::make("social_links")->schema([

                    Repeater::make("social_links")
                        ->label('')
                        ->schema([
                            SelectFontAwesomeIcon::make('icon')
                                ->searchable()
                                ->allowHtml(),

                            TextInput::make('link')
                                ->url()
                            //                                ->required()
                            ,
                        ])

                ])
                    ->collapsible()
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return __("menu.general");
    }

    public function getHeading(): string|Htmlable
    {
        return __('sections.global_settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.settings');
    }

    public function getTitle(): string|Htmlable
    {
        return __('sections.global_settings');
    }

    public function workingDaysSchema(): Repeater
    {
        $schema = [];
        foreach (['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day) {

            $schema[] = Group::make([
                Checkbox::make("$day.status")->label(__("forms.fields.weekdays.$day")),
                Hidden::make("$day.day_name")->default($day),
                TextInput::make("$day.from")->type('time')->label('')->default("00:00"),
                TextInput::make("$day.to")->type('time')->label('')->default("23:59"),
            ])->columns(3);
        }
        //        dd($schema);
        return Repeater::make('working_days')
            ->label('')
            ->deletable(false)
            //            ->addable(false)
            ->schema($schema)
            ->defaultItems(1)
            ->minItems(1)
            ->maxItems(1);
    }

    public function getBreadcrumbs(): array
    {
        return [
            null => static::getNavigationGroup(),
            static::getUrl() => static::getNavigationLabel(),
        ];
    }
}
