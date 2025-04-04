<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusEnum;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Traits\Filterable;
class Language extends Model
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
    public function scopeDefault(Builder $q) : Builder{
        return $q->where('is_default',(StatusEnum::true)->status());
    }
    public function scopeActive(Builder $q) :Builder{
        return $q->where('status',(StatusEnum::true)->status());
    }

}
