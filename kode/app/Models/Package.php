<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{

    use HasFactory , Filterable;


    
    protected $casts = [

        'social_access'        => 'object',
        'ai_configuration'     => 'object',
        'template_access'      => 'array',
    ];


    protected $guarded = [];

    protected static function booted(){

        static::creating(function (Model $model) {

            $model->uid        = Str::uuid();
            $model->created_by = auth_user()? auth_user()->id : null;
            $model->status     = StatusEnum::true->status();
        });

        static::updating(function(Model $model) {

            $model->updated_by = auth_user()?->id;
        });

        static::saving(function (Model $model) {

            if(request()->input('title')){
                $model->slug       = make_slug(request()->input('title'));
            }
    
        });

        
    }

    public function createdBy() :BelongsTo{

        return $this->belongsTo(Admin::class,'created_by','id')->withDefault([
            'username' => '-',
        ]);
    }

    public function updatedBy() :BelongsTo{

        return $this->belongsTo(Admin::class,'updated_by','id')->withDefault([
             'username' => '-',
        ]);
    }
    
    public function scopeActive(Builder $q) :Builder{

        return $q->where("status",StatusEnum::true->status());
    }

    public function scopeFeature(Builder $q) :Builder{
        return $q->where("is_feature",StatusEnum::true->status());
    }


    public function scopeRecommended(Builder $q) :Package{

        return $q->where("is_recommended",StatusEnum::true->status())->first();
    }

    

    public function subscriptions() :HasMany{

        return $this->hasMany(Subscription::class,'package_id','id');
    }



    
}
