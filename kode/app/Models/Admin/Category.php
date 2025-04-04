<?php

namespace App\Models\Admin;

use App\Enums\CategoryDisplay;
use App\Enums\StatusEnum;
use App\Models\Admin;
use App\Models\AiTemplate;
use App\Models\Blog;
use App\Models\ModelTranslation;
use App\Models\Scopes\Global\ActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Traits\ModelAction;
class Category extends Model
{
    use HasFactory , Filterable ,ModelAction ;

    protected $guarded = [];

    protected $casts = [
        'meta_keywords' => 'object',
    ];

    protected static function booted(){


        static::addGlobalScope('autoload', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                return $query->where('locale', app()->getLocale());
            }]);
        });

        static::creating(function (Model $model) {
            $model->uid        = Str::uuid();
            $model->created_by = auth_user()?->id;
            $model->status     = StatusEnum::true->status();
        });

        static::saving(function (Model $model) {
            if(request()->input('slug') || request()->input('title') ){
                $model->slug       = make_slug(request()->input('slug')? request()->input('slug') :Arr::get(request()->input('title'),'default',''));
            }

            ModelAction::saveSeo($model);
        });

        static::updating(function(Model $model) {
            $model->updated_by = auth_user()?->id;
        });
        static::deleting(function(Model $model) {
            $model->translations()->delete();
        });
    }


    public function scopeActive(Builder $q) :Builder{
        return $q->where("status",StatusEnum::true->status());
    }


    public function scopeFeature(Builder $q) :Builder{
        return $q->where("is_feature",StatusEnum::true->status());
    }


    public function scopeArticle(Builder $q) :Builder{
        return $q->whereIn("display_in",CategoryDisplay::values(['BOTH','BLOG'],true));
    }

    public function scopeTemplate(Builder $q) :Builder{
        return $q->whereIn("display_in",CategoryDisplay::values(['BOTH','TEMPLATE'],true));
    }


    public function templates() :HasMany{

        return $this->hasMany(AiTemplate::class, 'category_id','id');
    }


    public function articles() :HasMany{
        return $this->hasMany(Blog::class, 'category_id','id');
    }


    public function parent() :BelongsTo{
        return $this->belongsTo(Category::class, 'parent_id','id');
    }


    public function childrens() :HasMany{
        return $this->hasMany(Category::class, 'parent_id','id');
    }



    public function createdBy() :BelongsTo{
        return $this->belongsTo(Admin::class,'created_by','id')->withDefault([
            'username' => '-',
            'name' => '-'
        ]);
    }

    public function updatedBy() :BelongsTo{
        return $this->belongsTo(Admin::class,'updated_by','id')->withDefault([
            'username' => '-',
            'name' => '-'
        ]);
    }


    public function translations():MorphMany{
        return $this->morphMany(ModelTranslation::class, 'translateable');

    }


    public function getTitleAttribute(mixed $value) :string{

        if(count($this->translations) !=0 ) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'title') {
                    return $translation['value'];
                }
            }
        }
        return $value;
    }








}
