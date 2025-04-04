<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Filterable;
class Subscription extends Model
{
    use HasFactory ,Filterable;


    protected $guarded = [];


    public function user() :BelongsTo{
        return $this->belongsTo(User::class,'user_id','id')->withDefault([
            'username' => '-',
        ]);
    }
    public function admin() :BelongsTo{
        return $this->belongsTo(Admin::class,'admin_id','id')->withDefault([
            'username' => '-',
        ]);
    }
    public function package() :BelongsTo{
        return $this->belongsTo(Package::class,'package_id','id')->withDefault([
            'name' => '-',
        ]);
    }

    public function oldPackage() :BelongsTo{
        return $this->belongsTo(Package::class,'old_package_id','id');
    }



    public function scopeExpired(Builder $q) :Builder{

        $currentDate = now()->toDateString();
        return $q->where(fn (Builder $query)   : Builder => 
                $query->whereNotNull('expired_at')
                    ->where('expired_at', '<', $currentDate));
    }


    public function scopeRunning(Builder $q) :Builder {
        
        return $q->where("status",SubscriptionStatus::value("RUNNING",true));
    }


  
}
