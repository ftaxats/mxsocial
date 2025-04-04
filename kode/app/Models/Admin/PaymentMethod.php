<?php

namespace App\Models\Admin;

use App\Enums\StatusEnum;
use App\Models\Core\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\PaymentLog;
use App\Models\Scopes\Global\ActiveScope;
use Illuminate\Support\Str;
use App\Traits\Filterable;
use App\Traits\ModelAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PaymentMethod extends Model
{
    use HasFactory , Filterable ,ModelAction;
    protected $guarded = [];


    protected $casts = [
        'parameters'          => 'object',
        'extra_parameters'    => 'object',
    ];

    protected static function booted()
    {

    
        static::creating(function (Model $model) {
            
            $model->uid        = Str::uuid();
            $model->name       = (request()->get("name"));
            $model->code       = t2k(request()->get("name"));
            $model->created_by = auth_user()?->id;
            $model->status     = StatusEnum::true->status();
            $model->type       = StatusEnum::false->status();
            $model->setParameters();


        });

        static::updating(function(Model $model) {
            $model->updated_by = auth_user()?->id;

        });

        
    }

    public function setParameters() :void{
        $parameter = request()->route('type') == 'manual' ? $this->parseManualParameters() : request()->input("parameter");
        $this->parameters = $parameter;
    }

    public function file() :MorphOne{
        return $this->morphOne(File::class, 'fileable');
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

    public function deposits() :HasMany{
        return $this->hasMany(PaymentLog::class,'method_id','id');
    }


    public function scopeType(Builder $q) : Builder{

        return $q->where('type',  request()->route('type') == 'manual' ? StatusEnum::false->status() : StatusEnum::true->status());
    }
  
    public function scopeAutomatic(Builder $q) : Builder{
        return $q->where('type',StatusEnum::true->status());
    }
    public function scopeManual(Builder $q) :Builder{
        return $q->where('type',StatusEnum::false->status());
    }


    public function currency() :BelongsTo{
        return $this->belongsTo(Currency::class,'currency_id','id')->withDefault([
            'name' => '-',
            'code' => '-'
        ]);
    }


    public function scopeActive(Builder $q) :Builder{
        return $q->where('status',StatusEnum::true->status());
    }



}
