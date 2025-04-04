<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelTranslation extends Model
{
    use HasFactory;


    
    public $timestamps = false; 
    
    protected $fillable = [
        'translateable_type',
        'translateable_id',
        'locale',
        'key',
        'value',
    ];

    public function translateable()
    {
        return $this->morphTo();
    }
}
