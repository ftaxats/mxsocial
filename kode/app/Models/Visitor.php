<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Foundation\Mix;
use Illuminate\Support\Str;
class Visitor extends Model
{
    use HasFactory,Filterable;





    protected $guarded = [];
    
    protected $casts = [
        'agent_info' => 'object',
    ];


    protected static function booted(){

        static::creating(function (Model $model) {
            $model->uid        = Str::uuid();
        });

      
    }


    public function country() :BelongsTo{
        return $this->belongsTo(Country::class,'country_id','id')->withDefault([
            'name' => "-"
        ]);
    }

    public function updatedBy() :BelongsTo{
        return $this->belongsTo(Admin::class,'updated_by','id')->withDefault([
            'username' => '-',
            'name' => '-',
        ]);
    }

 

    public static function insertOrupdtae(string  $ip_address,array $ipInfo , mixed $country ,? bool $blocked = false) :mixed{

        
    
        $ip = Visitor::where('ip_address', $ip_address)->first();
        if (!$ip) {
            $ip = new Visitor();
            $ip->ip_address = $ip_address;
            $ip->country_id = $country?->id;
            $ip->is_blocked = $blocked ? StatusEnum::true->status() : StatusEnum::false->status();
        }

        $ip->agent_info = $ipInfo;
        $ip->updated_at =  Carbon::now();
        $ip->save();
        return $ip;
    }


}
