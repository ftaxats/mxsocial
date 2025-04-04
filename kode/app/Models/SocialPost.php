<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Models\Core\File;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
class SocialPost extends Model
{
    use HasFactory;

    use HasFactory , Filterable;

    protected $casts = [
        'platform_response' => 'object',
    ];


    protected $guarded = [];


    protected static function booted(){
        static::creating(function (Model $model) {
            $model->uid        = Str::uuid();
        });
    }



    /**
     * Get file
     *
     * @return MorphMany
     */
    public function file(): MorphMany{
        return $this->morphMany(File::class, 'fileable');
    }


    /**
     * Get user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class, 'user_id');
    }



    /**
     * Get admin
     *
     * @return BelongsTo
     */
    public function admin(): BelongsTo{
        return $this->belongsTo(Admin::class, 'admin_id');
    }




    /**
     * Get the account of the post
     *
     * @return BelongsTo
     */
    public function account() :BelongsTo{
        return $this->belongsTo(SocialAccount::class, 'account_id');
    }


    /**
     * Get the platform of the post
     *
     * @return BelongsTo
     */
    public function platform() :BelongsTo{
        return $this->belongsTo(MediaPlatform::class, 'platform_id');
    }

    


    /**
     * Pending post
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopePending(Builder $q):Builder {
        return $q->where('status',strval(PostStatus::PENDING->value));
    }


    



    /**
     * Success post
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeSuccess(Builder $q):Builder {
        return $q->where('status',strval(PostStatus::SUCCESS->value));
    }



    /**
     * Scheule post
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeSchedule(Builder $q):Builder {
        return $q->where('status',strval(PostStatus::SCHEDULE->value));
    }


    

    /**
     * Failed post
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeFailed(Builder $q):Builder {
        return $q->where('status',strval(PostStatus::FAILED->value));
    }



    /**
     * Postable post
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopePostable(Builder $q):Builder {
        return $q->whereIn('status',[strval(PostStatus::value('PENDING',true)) ,strval(PostStatus::value('SCHEDULE',true))]);
    }


}
