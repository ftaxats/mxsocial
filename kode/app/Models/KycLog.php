<?php

namespace App\Models;

use App\Enums\KYCStatus;
use App\Enums\WithdrawStatus;
use App\Models\Core\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycLog extends Model
{
    use HasFactory , Filterable;
    protected $guarded = [];

    protected $casts = [
        'kyc_data' => 'object',
    ];


    /**
     * Get KYC files
     *
     * @return MorphMany
     */
    public function file(): MorphMany{
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * Get pending log
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopePending(Builder $q): Builder{
        return $q->where('status',KYCStatus::value("REQUESTED",true));
    }

    /**
     * Get approved log
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeApproved(Builder $q): Builder{
        return $q->where('status',KYCStatus::value("APPROVED",true));
    }



    /**
     * Get hold log
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeHold(Builder $q): Builder{
        return $q->where('status',KYCStatus::value("HOLD",true));
    }


    /**
     * Get rejected log
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeRejected(Builder $q): Builder{
        return $q->where('status',KYCStatus::value("REJECTED",true));
    }


    /**
     * Get the user of the log
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class,'user_id');
    }
}
