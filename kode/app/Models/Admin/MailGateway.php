<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Traits\Filterable;
class MailGateway extends Model
{
    use HasFactory , Filterable;

    
    protected $guarded = [];

    protected $casts = [
        'credential' => 'object',
    ];

    
    protected static function booted(){
        static::updating(function(Model $model) {
            $model->updated_by = auth_user()?->id;
        });

        static::creating(function (Model $model) {
            $model->uid        = Str::uuid();
        });
    }

    public function updatedBy() :BelongsTo{
        return $this->belongsTo(Admin::class,'updated_by','id')->withDefault([
            'username' => '--',
            'name' => '--'
        ]);
    }


}
