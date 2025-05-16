<?php

namespace App\Models\Catalog;

use App\Enum\ServiceReservationTypesEnum;
use App\Models\Add;
use App\Models\Catalog\Branch\Inventory;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderRate;
use App\Models\PlanService;
use App\Models\ProductAdd;
use App\Models\ProductOption;
use App\Models\ServiceType;
use App\Models\Worker;
use App\Notifications\Product\NewProductCreatedNotification;
use App\Traits\Publishable;
use App\Traits\Searchable;
use ChristianKuri\LaravelFavorite\Traits\Favoriteable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Notification;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia
{
    use  HasTranslations, InteractsWithMedia, Publishable;
    use  Searchable, SoftDeletes;
    

    protected $fillable = ['title', 'description',  'price', 'category_id', 'status', 'more_data'];
    public array $translatable = ['title', 'description'];
    protected $casts = [
        'more_data' => 'array',
        'type' => ServiceReservationTypesEnum::class,
        //        'price' => MoneyDecimalCast::class,
        //        'sale_price' => MoneyDecimalCast::class,
    ];

    protected static function booted()
    {
        parent::booted();
        static::created(function (Product $product) {
            // dispatch(fn() => Notification::send(Customer::whereNotificationsModeEnabled()->get(), new NewProductCreatedNotification($product)))->afterResponse();

        });
    }

    public function scopeCategoryEnabled($builder)
    {
        return $builder->whereHas('category', fn($query) => $query->where('status', 1));
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withoutGlobalScopes();
    }

   
    

   

  


}
