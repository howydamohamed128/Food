<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use App\Traits\Publishable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model implements HasMedia {
    use  Publishable, HasTranslations,InteractsWithMedia, HasFactory,SoftDeletes;

    protected $appends = ['ar','en'];
    protected array $translatable = ['name','image'];
    protected $fillable = [
        'status',
        'site_status',
        'link',
        'name',
    ];
    protected $casts = [
        'site_status' => 'boolean',
    ];


    public function getArAttribute()
    {
        return $this->getFirstMediaUrl('ar');
    }

    public function getEnAttribute()
    {
        return $this->getFirstMediaUrl('en');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('ar');
        $this->addMediaCollection('en');
    }


}
