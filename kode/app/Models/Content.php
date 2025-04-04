<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;
use App\Traits\ModelAction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Content extends Model
{
    use HasFactory , Filterable , ModelAction;
    protected $guarded = [];


    protected static function booted(){

        static::creating(function (Model $model) {

            $model->uid             = Str::uuid();
            $model->user_id         = request()->routeIs('user.*') ? auth_user('web')?->id : null;
            $model->admin_id        = request()->routeIs('admin.*') ? auth_user('admin')?->id : null;
            $model->status          = StatusEnum::true->status();
            
        });

        static::saving(function (Model $model) {

            if(request()->input('name')){
                $model->slug       = make_slug(request()->input('name'));
            }
          
        });
    }


    /**
     * Get the user that owns the content
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'username' => '-',
            'name' => '-'
        ]);
    }


    /**
     * Get the admin that owns the content
     *
     * @return BelongsTo
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id')->withDefault([
            'username' => '-',
            'name' => '-'
        ]);
    }


   

}
