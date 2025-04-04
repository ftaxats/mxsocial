<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PostWebhookLog extends Model
{
    use HasFactory ,Filterable;
    
    protected $guarded = [];

    protected $casts = [
        'webhook_response'  => 'object',
    ];



}
