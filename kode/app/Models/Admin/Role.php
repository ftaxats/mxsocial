<?php

namespace App\Models\Admin;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Role extends Model
{
    use HasFactory , Filterable;

    protected $guarded = [];

    protected $casts = [
        'permissions' => 'object',
    ];


    protected static function booted() :void{
        parent::boot();
        static::creating(function (Model $model) {
            $model->uid        = Str::uuid();
            $model->created_by = auth_user()?->id;
            $model->status     = StatusEnum::true->status();
        });

        static::updating(function(Model $model) {
            $model->updated_by = auth_user()?->id;
        });
    }

    public function createdBy() :BelongsTo {
        return $this->belongsTo(Admin::class,'created_by','id')->withDefault([

            'username' =>  '-',
            'name' =>  '-'
        ]);
    }

    public function updatedBy()  :BelongsTo{
        return $this->belongsTo(Admin::class,'updated_by','id')->withDefault([

            'username' => '-',
            'name' =>  '-'
        ]);
    }

    public function scopeActive(Builder $q) :Builder{
        return $q->where('status',StatusEnum::true->status());
    }

    public function staff() :HasMany{
        return $this->hasMany(Admin::class,'role_id','id')->latest();
    }

    public function scopeFilter(Builder $q) :Builder{
        
        return $q->when(request()->name,function($query) {
            return $query->where("name","like","%".request()->name."%");
        });
    }
}
