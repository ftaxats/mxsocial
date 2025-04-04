<?php

namespace App\Models;

use App\Enums\DepositStatus;
use App\Enums\StatusEnum;
use App\Models\Admin\Currency;
use App\Models\Admin\PaymentMethod;
use App\Models\Core\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use App\Traits\ModelAction;
use App\Traits\Filterable;
class PaymentLog extends Model
{
    use HasFactory ,Filterable;


    protected $guarded = [];

    protected $casts = [
        'custom_data'      => 'object',
        'gateway_response' => 'object',
    ];



    /**
     * Get the user of the  payment log
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class,'user_id','id')->withDefault(['username' => '-','name'     => '-']);
    }


    /**
     * Get payment method
     *
     * @return BelongsTo
     */
    public function method(): BelongsTo{
        return $this->belongsTo(PaymentMethod::class,'method_id','id')
                                 ->withDefault(['name' => '-']);
    }



    /**
     * Get currency of the log
     *
     * @return BelongsTo
     */
    public function currency(): BelongsTo{
        return $this->belongsTo(Currency::class,'currency_id','id')->withDefault(['name' => '-']);
    }
  

    /**
     * Get files of a speficic payment log
     *
     * @return MorphMany
     */
    public function file(): MorphMany{
        return $this->morphMany(File::class, 'fileable');
    }


    /**
     * Get pending payment log
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopePending(Builder $q): Builder{
        return $q->where('status',DepositStatus::value("PENDING",true));
    }


    /**
     * Get initiate payment log
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeInitiate(Builder $q): Builder{
        return $q->where('status',DepositStatus::value("INITIATE",true));
    }


    /**
     * Get  paid  payment log
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopePaid(Builder $q): Builder{
        return $q->where('status',DepositStatus::value("PAID",true));
    }


}
