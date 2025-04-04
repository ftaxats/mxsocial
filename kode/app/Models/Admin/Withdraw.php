<?php

namespace App\Models\Admin;

use App\Enums\StatusEnum;
use App\Models\Core\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\Scopes\Global\ActiveScope;
use App\Models\WithdrawLog;
use Illuminate\Support\Str;
use App\Traits\Filterable;
use App\Traits\ModelAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;


class Withdraw extends Model
{
    use HasFactory ,Filterable ,  ModelAction;
    protected $guarded = [];
    protected $casts = [
        'parameters' => 'object',
    ];
    protected static function booted()
    {

 
        static::creating(function (Model $model) {
            
            $model->uid        = Str::uuid();
            $model->created_by = auth_user()->id;
            $model->status     = StatusEnum::true->status();
            $model->setParameters();

        });

        static::updating(function(Model $model) {
            $model->updated_by = auth_user()?->id;
        });
    }


    public function setParameters() :void{

        $this->parameters = $this->parseManualParameters();
    }

    public function scopeActive(Builder $q) : Builder{
        return $q->where('status',StatusEnum::true->status());
    }


    public function createdBy() :BelongsTo{
        return $this->belongsTo(Admin::class,'created_by','id')->withDefault([
            'user_name' =>  '-',
            'name' =>  '-'
        ]);
    }
    public function updatedBy() :BelongsTo{
        return $this->belongsTo(Admin::class,'updated_by','id')->withDefault([
            'username' =>  '-',
            'name' => '-'
        ]);
    }
 
    public function file() :MorphOne{
        return $this->morphOne(File::class, 'fileable');
    }



    public function log() :HasMany{
        return $this->hasMany(WithdrawLog::class, 'method_id');
    }


    
  
}
