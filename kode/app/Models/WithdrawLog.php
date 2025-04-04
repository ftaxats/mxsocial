<?php

namespace App\Models;

use App\Enums\WithdrawStatus;
use App\Models\Admin\Currency;
use App\Models\Admin\Withdraw;
use App\Models\Core\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use App\Traits\Filterable;
class WithdrawLog extends Model
{
    use HasFactory ,Filterable;


    protected $guarded = [];

    protected $casts = [
        'custom_data' => 'object',
    ];


    /**
     * withdraw files
     *
     * @return MorphMany
     */
    public function file() :MorphMany {

        return $this->morphMany(File::class, 'fileable');
    }


    public function user() :BelongsTo {

        return $this->belongsTo(User::class,'user_id','id')->withDefault([
            'username' => '-',
            'name'     => '-',
        ]);
    }
    public function method() :BelongsTo {
        return $this->belongsTo(Withdraw::class,'method_id','id')->withDefault([
            'name' => '-',
        ]);
    }

    public function currency() :BelongsTo {

        return $this->belongsTo(Currency::class,'currency_id','id')->withDefault([
            'name' => '-',
        ]);
    }
  

    public function scopePending(Builder $q) :Builder{

        return $q->where('status',WithdrawStatus::value("PENDING",true));
    }


    public function scopeApproved(Builder $q) :Builder{

        return $q->where('status',WithdrawStatus::value("APPROVED",true));
    }




}
