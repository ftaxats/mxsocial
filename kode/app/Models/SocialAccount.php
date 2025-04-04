<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class SocialAccount extends Model
{
    use HasFactory , Filterable;

    protected $guarded = [];

    protected $casts = [
        'account_information' => 'object',
    ];


    protected static function booted(){
        static::creating(function (Model $model) {
            $model->uid        = Str::uuid();
        });
    }


    /**
     * Admin where account belongs
     *
     * @return BelongsTo
     */
    public function admin(): BelongsTo{
        return $this->belongsTo(Admin::class,"admin_id");
    }



    /**
     * User where account belongs
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class,"user_id");
    }


    /**
     * Platform where account belongs
     *
     * @return BelongsTo
     */
    public function platform(): BelongsTo{
        return $this->belongsTo(MediaPlatform::class,"platform_id");
    }


    /**
     * subscription where account belongs
     *
     * @return BelongsTo
     */
    public function subscription(): BelongsTo{  
        return $this->belongsTo(Subscription::class,"subscription_id");
    }


    /**
     * Get social post
     *
     * @return HasMany
     */
    public function posts(): HasMany{
        return $this->hasMany(SocialPost::class,"account_id");
    }
    

    public function scopeActive(Builder $q) :Builder{
        return $q->where('status',StatusEnum::true->status());
    }
    public function scopeInactive(Builder $q) :Builder{
        return $q->where('status',StatusEnum::false->status());
    }


    public function scopeConnected(Builder $q) :Builder{
        return $q->where('is_connected',StatusEnum::true->status());
    }

    
}
