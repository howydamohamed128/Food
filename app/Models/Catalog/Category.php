<?php

namespace App\Models\Catalog;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use App\Traits\Publishable;

class Category extends Model implements HasMedia {
    use HasTranslations, Publishable, InteractsWithMedia,SoftDeletes;

    protected $fillable = ['name', 'description', 'status'];
    public $translatable = ['name', 'description'];
    use HasFactory;

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(Product::class, 'category_id');
    }

   
}
