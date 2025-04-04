<?php

namespace App\Models\Admin;

use App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Filterable;
class Template extends Model
{
    use HasFactory ,Filterable;
    protected $guarded = [];

    protected $casts = [
        'template_key' => 'object',
    ];


    protected static function booted(){
        static::updating(function(Model $model) {
            $model->updated_by = auth_user()?->id;
        });

    }


    public function updatedBy() : BelongsTo{
        return $this->belongsTo(Admin::class,'updated_by','id')->withDefault([
            'name' =>  '-',
            'username' =>  '-',
        ]);
    }
}
