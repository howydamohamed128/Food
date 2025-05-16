<?php

namespace App\Filament\Widgets;

use DB;
use Carbon\Carbon;
use App\Models\Add;
use App\Models\Gift;
use App\Models\Plan;
use App\Models\User;
use App\Models\Zone;
use App\Models\Order;
use App\Models\Salary;
use App\Models\Worker;
use Cknow\Money\Money;
use App\Models\Customer;
use App\Models\Content\Banner;
use App\Models\Catalog\Product;
use App\Models\Catalog\Service;
use App\Models\Catalog\Category;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class GlobalOrderStats extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 2;

    protected function getStats(): array
    {

        return [

            Stat::make(__('panel.stats.banners_count'), value: Banner::count())->icon('heroicon-o-photo'),
            Stat::make(__('panel.stats.categories_count'), Category::count())->icon('heroicon-o-queue-list'),
            Stat::make(__('panel.stats.products_count'), Product::count())->icon('heroicon-o-briefcase'),
      
        ];
    }
    public function getHeading(): ?string
    {
        return __('panel.stats.general_stats');
    }

}
