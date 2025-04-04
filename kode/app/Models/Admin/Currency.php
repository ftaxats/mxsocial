<?php

namespace App\Models\Admin;

use App\Enums\StatusEnum;
use App\Models\Admin;
use App\Models\PaymentLog;
use App\Models\Scopes\Global\ActiveScope;
use App\Models\Transaction;
use App\Models\WithdrawLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Cache;

class Currency extends Model
{
    use HasFactory ,Filterable;
    protected $guarded = [];

    protected static function booted()
    {



        static::creating(function (Model $model) {
            $model->uid        = Str::uuid();
            $model->created_by = auth_user()?->id;
            $model->status     = StatusEnum::true->status();
        });

        static::updating(function(Model $model) {
            $model->updated_by = auth_user()?->id;
        });

        static::updated(function(Model $model) {

            if(session()->has('currency') && session()->get('currency')?->code == $model->code ){
                session()->put('currency',  $model);
            }
        });

        static::saved(function (Model $model) {
            Cache::forget('site_currencies');
            Cache::forget('base_currencies');
        });

        static::deleted(function(Model $model) {
	        Cache::forget('site_currencies');
	    });
       
    }
    public function createdBy() :BelongsTo{
        return $this->belongsTo(Admin::class,'created_by','id')->withDefault([
            'username' => '-',
            'name' => '-'
        ]);
    }

    public function updatedBy() :BelongsTo{
        return $this->belongsTo(Admin::class,'updated_by','id')->withDefault([
            'username' => '-',
            'name' => '-'
        ]);
    }


    public function scopeDefault(Builder $query) :Builder{
       return $query->where("default",StatusEnum::true->status());
    }

    public function scopeRegular(Builder $query) :Builder{
        return $query->where("default",StatusEnum::false->status())->orWhere('default',null);
    }

    public function scopeActive(Builder $query) :Builder{
        return $query->where("status",StatusEnum::true->status());
    }

    public function scopeBase(Builder $query) :Currency{
        return $query->where("base",StatusEnum::true->status())->first();
    }


    public function gateway() :HasMany{
        return $this->hasMany(PaymentMethod::class,'currency_id','id');
    }


    public function withdraws() :HasMany{
        return $this->hasMany(WithdrawLog::class,'currency_id','id');
    }


    public function transactions() :HasMany{
        return $this->hasMany(Transaction::class,'currency_id','id');
    }

    public function deposits() :HasMany{
        return $this->hasMany(PaymentLog::class,'currency_id','id');
    }


    
    
}
