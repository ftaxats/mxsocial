<?php

namespace App\Models\Admin;

use App\Enums\StatusEnum;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Traits\Filterable;
use Illuminate\Support\Facades\Cache;
class Menu extends Model
{
    use HasFactory ,Filterable;


    protected $guarded = [];
    protected $casts = [
        'section' => 'array',
        'meta_keywords' => 'object',
    ];
 
    protected static function booted()
    {
        static::creating(function (Model $model) {
            $model->uid        = Str::uuid();
            $model->created_by = auth_user()?->id;
            $model->status     = StatusEnum::true->status();
            Cache::forget('menus');
        });

        static::updating(function(Model $model) {
            $model->updated_by = auth_user()?->id;
            Cache::forget('menus');
        });

        static::saving(function(Model $model) {
            if(request()->input("section"))  $model->section =  request()->input("section");
            Cache::forget('menus');
        });
        static::deleted(function(Model $model) {
            Cache::forget('menus');
	    });
    }

    public function scopeActive(Builder $q) :Builder{
        return $q->where("status",StatusEnum::true->status());
    }



    public function scopeDefault(Builder $q) :Builder{
        return $q->where("is_default",StatusEnum::true->status());
    }
   
   

    public function createdBy() :BelongsTo {
        return $this->belongsTo(Admin::class,'created_by','id')->withDefault([
            'username' => '-',
            'name' => '-'
        ]);
    }

    public function updatedBy() :BelongsTo {
        return $this->belongsTo(Admin::class,'updated_by','id')->withDefault([
            'username' => '-',
            'name' => '-'
        ]);
    }
   

}
