<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Models\Admin\Category;
use App\Models\Core\File;
use App\Models\Scopes\Global\ActiveScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;
use App\Traits\ModelAction;
use App\Traits\Filterable;
use Illuminate\Support\Facades\Cache;

class Blog extends Model
{
    use HasFactory ,ModelAction ,Filterable;

    protected $guarded = [];

    protected $table = 'blogs';

    protected $casts = [
        'meta_keywords' => 'object',
    ];

    protected static function booted(){

        static::addGlobalScope(new ActiveScope());

        static::addGlobalScope('autoload', fn (Builder $builder): Builder => 
                                       $builder->with(['category' ,'file','createdBy']));

        static::creating(function (Model $model): void{
            $model->uid        = Str::uuid();
            $model->created_by = auth_user()?->id;
            $model->status     = StatusEnum::true->status();
            Cache::forget('feature_blogs');
        });

        static::updating(function(Model $model): void{
            $model->updated_by = auth_user()?->id;
            Cache::forget('feature_blogs');
        });

        
        static::saving(function(Model $model): void{

            if(request()->input('slug') || request()->input('title') ){
                $model->slug       = make_slug(request()->input('slug')?request()->input('slug'):request()->input('title'));
            }
            Cache::forget('feature_blogs');
            ModelAction::saveSeo($model);
        });

        static::deleted(function(Model $model) {
            Cache::forget('feature_blogs');
	    });
        
    }



    /**
     * Get blog image
     *
     * @return MorphOne
     */
    public function file(): MorphOne{
         return $this->morphOne(File::class, 'fileable');
    }


    /**
     * Get active blogs
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeActive(Builder $q): Builder{
        return $q->where("status",StatusEnum::true->status());
    }


    /**
     * Get featured blogs
     *
     * @param Builder $q
     * @return Builder
     */
    public function scopeFeature(Builder $q): Builder{
        return $q->where("is_feature",StatusEnum::true->status());
    }



    /**
     * Get blog category
     *
     * @return BelongsTo
     */
    public function category():BelongsTo {
        return $this->belongsTo(Category::class, 'category_id','id');
    }



    /**
     * Get the admin who create this record
     *
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo{
        return $this->belongsTo(Admin::class,'created_by','id')->withDefault([
            'username' => '-',
            'name' => '-'
        ]);
    }

    /**
     * Get the admin who update this record
     *
     * @return BelongsTo
     */
    public function updatedBy(): BelongsTo{
        return $this->belongsTo(Admin::class,'updated_by','id')->withDefault([
            'username' => '-',
            'name' => '-'
        ]);
    }



}
