<?php

namespace App\Providers;

use App\Lib\Cart;
use App\Notifications\Notification;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification as BaseNotification;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use MyFatoorah\Library\API\MyFatoorahRefund;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        // $this->cart();;
        // $this->app->bind(MyFatoorahPayment::class, function () {
        //     return new MyFatoorahPayment([
        //             'apiKey' => config('myfatoorah.api_key'),
        //             'isTest' => config('myfatoorah.test_mode'),
        //             'countryCode' => config('myfatoorah.country_iso'),
        //         ]
        //     );
        // });
        // $this->app->bind(MyFatoorahPaymentStatus::class, function () {
        //     return new MyFatoorahPaymentStatus([
        //             'apiKey' => config('myfatoorah.api_key'),
        //             'isTest' => config('myfatoorah.test_mode'),
        //             'countryCode' => config('myfatoorah.country_iso'),
        //         ]
        //     );
        // });
        // $this->app->bind(MyFatoorahRefund::class, function () {
        //     return new MyFatoorahRefund([
        //             'apiKey' => config('myfatoorah.api_key'),
        //             'isTest' => config('myfatoorah.test_mode'),
        //             'countryCode' => config('myfatoorah.country_iso'),
        //         ]
        //     );
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        RateLimiter::for('otp', function () {
            return Limit::perMinutes("3", 1);
        });

        $this->app->bind(BaseNotification::class, Notification::class);
        $this->translateLabels();
        FilamentView::registerRenderHook(
            'panels::scripts.after',
            fn(): string => Blade::render('filament.firebase-initialization'),
        );
        FilamentView::registerRenderHook(
            'panels::head.start',
            fn(): string => Blade::render('filament.hooks.body-start'),
        );
        // LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
        //     $switch->locales(['ar', 'en']);
        // });


        FilamentAsset::register([
            Js::make('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js'),
            Js::make('jstree', 'https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.15/jstree.min.js'),
            Css::make('jstree', asset('css/custom/jstree/jstree.min.css')),
            Css::make('jstree-ext', asset('css/custom/jstree/ext-component-tree.min.css')),
            Css::make('fontawesome', asset('https://pro.fontawesome.com/releases/v5.10.0/css/all.css')),
        ]);


        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });
    }


    private function translateLabels(): void {
        $translateLabelsComponents = [
            Field::class,
            Filter::class,
            SelectFilter::class,
        ];
        foreach ($translateLabelsComponents as $component) {
            $component::configureUsing(function ($c): void {
                $c->label(__('forms.fields.' . $c->getName()));
            });
        }
        Field::macro('translatable', function () {
            return $this->hint(__('forms.fields.translatable'))
                ->hintIcon('heroicon-m-language');
        });

        Table::configureUsing(function (Table $table): void {
            $table->modifyQueryUsing(function (Builder $query): void {

                if ($query->getColumns()->getModel()->getCreatedAtColumn() && !in_array(get_class($query->getColumns()->getModel()),[\App\Models\Notification::class])) {
                    $query->latest($query->getColumns()->getModel()->getTable() . ".id");
                }
            });
        });

        TextEntry::configureUsing(function (TextEntry $field): void {
            $field->label(__('forms.fields.' . Str::replace('.', '_', $field->getName())));
        });

        Section::configureUsing(function (Section $section): void {
            $section
                ->collapsible()
                ->heading(__('sections.' . Str::lower($section->getHeading())))
                ->label(__('sections.' . Str::lower($section->getHeading())));

        });
        Column::configureUsing(function ($c): void {
            $c->label(fn($column): string => __("forms.fields." . Str::replace('.', '_', $column->getName())))
                ->translateLabel()
                ->toggleable();
        });

        \Filament\Infolists\Components\Section::configureUsing(function (\Filament\Infolists\Components\Section $section): void {
            $section->collapsible()->heading(__('sections.' . Str::lower($section->getHeading())));
        });
    }

    // private function cart() {
    //     $this->app->singleton('cart', function ($app) {
    //         $storageClass = config('shopping_cart.storage');
    //         $eventsClass = config('shopping_cart.events');
    //         $storage = $storageClass ? new $storageClass() : $app['session'];
    //         $events = $eventsClass ? new $eventsClass() : $app['events'];
    //         $instanceName = 'cart';
    //         if (!session()->has('cart_id')) {
    //             session(['cart_id' => uniqid()]);
    //         }
    //         $session_key = session('cart_id');
    //         return new Cart(
    //             $storage,
    //             $events,
    //             $instanceName,
    //             $session_key,
    //             config('shopping_cart')
    //         );
    //     });
    //     app('events')->listen('cart.cleared', function ($cart) {
    //         /** @var Cart $coreCart */
    //         $coreCart = $this->app['cart'];
    //         session(['cart_id' => uniqid()]);
    //         $session_key = session('cart_id');
    //         $coreCart->session($session_key);
    //     });
    // }
}
