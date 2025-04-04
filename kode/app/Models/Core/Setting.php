<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
class Setting extends Model
{
    use HasFactory;


    protected $guarded = [];
    

    public function file() :MorphOne{
        return $this->morphOne(File::class, 'fileable');
    }


   


}
