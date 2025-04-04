<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Models\Admin\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Filterable;
use App\Traits\ModelAction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class AiTemplate extends Model
{
    use HasFactory , Filterable , ModelAction;
    protected $guarded = [];

    protected $casts = [
        'prompt_fields'    => 'object',
    ];
    protected static function booted(){

        static::creating(function (Model $model) {
            
            $model->uid             = Str::uuid();
            $model->user_id         = request()->routeIs('user.*') ? auth_user('web')?->id : null;
            $model->admin_id        = request()->routeIs('admin.*') ? auth_user('admin')?->id : null;
            $model->status          = StatusEnum::true->status();
            $model->setParameters();
        });

        static::saving(function (Model $model) {
            
            if(request()->input('slug') || request()->input('name') ){
                $model->slug       = make_slug(request()->input('slug')? request()->input('slug') : request()->input('name'));
            }
            Cache::forget('feature_templates');

        });


        static::deleted(function(Model $model) {
            Cache::forget('feature_templates');
	    });

    }


    public function setParameters() :void {

        $this->prompt_fields =  $this->parseManualParameters();
    }


    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', StatusEnum::true->status());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', StatusEnum::true->status());
    }

    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('is_default', StatusEnum::false->status());
    }


    /**
     * Get the user that owns the AiTemplate
     * 
     * 
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault([
            'username' => '-',
            'name' =>  '-'
        ]);;
    }

    /**
     * Get the category that owns the AiTemplate
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }



    /**
     * Get the sub category that owns the AiTemplate
     *
     * @return BelongsTo
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'sub_category_id', 'id');
    }
    /**
     * Get the admin that owns the AiTemplate
     *
     * @return BelongsTo
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id')->withDefault([
            'username' => '-',
            'name' =>  '-'
        ]);
    }


    /**
     * Get all of the template usages for the AiTemplate
     *
     * @return HasMany
     */
    public function templateUsages(): HasMany
    {
        return $this->hasMany(TemplateUsage::class, 'template_id');
    }

}
