<?php

namespace App\Filament\Resources\Catalog\ProductResource\Pages;

use App\Filament\Resources\Catalog\PlanResource;
use App\Filament\Resources\Catalog\ProductResource;
use App\Filament\Resources\Users\CustomerResource;
use Filament\Actions;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListProductActivities extends ListActivities
{
    protected static string $resource = ProductResource::class;

}
