<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Filterable;
class CreditLog extends Model
{
    use HasFactory , Filterable;

    protected $guarded = [];


    public function subscription() :BelongsTo{
        return $this->belongsTo(Subscription::class,'subscription_id');
    }


    public function user() :BelongsTo{
        return $this->belongsTo(User::class,'user_id');
    }
}
