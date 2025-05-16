<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enum\GenderEnum;
use App\Enum\ModelStatus;
use App\Enum\UserStatus;
use App\Models\Location\City;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use ChristianKuri\LaravelFavorite\Traits\Favoriteability;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Theamostafa\Wallet\Traits\HasWallet;

class User extends Authenticatable implements HasMedia, FilamentUser, HasLocalePreference
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles, InteractsWithMedia;
    use HasPanelShield;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'active',
        'api_token',
        'password',
        'phone_verified_at',
        'settings',
        'data',
       

    ];

    protected static function booted()
    {
        parent::booted();

    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     *
     */

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'settings' => 'array',
        'data' => 'array',
        'location' => 'array',
        'working_days' => 'array',
    ];

    public function setPasswordAttribute($value)
    {

        if ($value && ! app()->runningInConsole()) {
            $this->attributes['password'] = bcrypt($value);

            return;
        }
        if ($value) {
            $this->attributes['password'] = $value;
        }
    }


    public function toggleActive(): bool
    {
        if ($this->active) {
            return $this->update(['active' => 0]);
        }
        return $this->update(['active' => 1]);
    }

    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class, "user_id");
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }

    

    public function isFirstTimeToLogin(): bool
    {
        return !$this->remember_token;
    }

    public function verified(): int
    {
        return !is_null($this->phone_verified_at);
    }

    // public function verificationCodes(): HasMany
    // {
    //     return $this->hasMany(VerificationCode::class, "user_id");
    // }

    public function preferredLocale()
    {
        return 'ar';
    }


    public function canAccessPanel(Panel $panel): bool
    {
        return !$this->hasRole(['customer']) && $this->active;
    }

   



    public function getWorkerOrdersCountAttribute()
    {
        return $this->workerOrders()->count();
    }


   
   
}
