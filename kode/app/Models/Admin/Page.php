<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusEnum;
use App\Models\Admin;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Traits\ModelAction;
class Page extends Model
{
    use HasFactory ,Filterable , ModelAction;


    protected $guarded = [];

    protected $casts = [
        'meta_keywords' => 'object',
    ];
    protected static function booted(){

        static::creating(function (Model $model) {
            $model->uid        = Str::uuid();
            $model->created_by = auth_user()?->id;
            $model->status     = StatusEnum::true->status();

        });

        static::updating(function(Model $model) {
            $model->updated_by = auth_user()?->id;
        });


        static::saving(function (Model $model) {
            if(request()->input('slug') || request()->input('title') ){
                $model->slug       = make_slug(request()->input('slug')?request()->input('slug') :request()->input('title'));
            }
            ModelAction::saveSeo($model);
        });

    }

    public function scopeActive(EloquentBuilder $q){
        return $q->where("status",StatusEnum::true->status());
    }

    public function scopeHeader(EloquentBuilder $q){
        return $q->where("show_in_header",StatusEnum::true->status());
    }
    public function scopeFooter(EloquentBuilder $q){
      return $q->where("show_in_footer",StatusEnum::true->status());
    }
    public function createdBy(){
        return $this->belongsTo(Admin::class,'created_by','id')->withDefault([
            'username' => '-',
            'name' => '-'
        ]);
    }
    public function updatedBy(){
        return $this->belongsTo(Admin::class,'updated_by','id')->withDefault([
            'username' => '-',
            'name' => '-'
        ]);
    }


}
