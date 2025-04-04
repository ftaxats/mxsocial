<?php

namespace App\Models\Admin;

use App\Enums\StatusEnum;
use App\Models\Admin;
use App\Models\Core\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
class Frontend extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'value' => 'object',
    ];
    protected static function booted(){

        static::addGlobalScope('autoload', function (Builder $builder) {
            $builder->with(['childrens','childrens.file']);
        });
        static::creating(function (Model $model) {
            $model->uid        = Str::uuid();
            $model->status     = StatusEnum::true->status();
        });
        static::saved(function(Model $model) {
            $model->updated_by = auth_user()?->id;
            Cache::forget('frontend_content');
        });

        static::deleted(function(Model $model) {
	        Cache::forget('frontend_content');
	    });
    }
    public function file() :MorphMany{
        return $this->morphMany(File::class, 'fileable');
    }

    public function updatedBy() :BelongsTo{
        return $this->belongsTo(Admin::class,'updated_by','id')->withDefault([
            'username' => '-',
            'name' => '-'
        ]);
    }


    public function childrens() :HasMany{
        return $this->hasMany(self::class,'parent_id','id');
    }

    public function scopeActive( Builder $q) :Builder {
         return $q->where('status',StatusEnum::true->status());
    }
}
