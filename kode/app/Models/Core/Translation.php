<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Translation extends Model
{
    use HasFactory;
    protected static function booted()
    {
        static::creating(function (Model $model) {
            $model->uid        = Str::uuid();

        });
    }
    protected $guarded = [];
}
