<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function notificationable(){
        return $this->morphTo();
    }

    public function scopeUnread(Builder $q) :Builder {

        return $q->where("is_read",StatusEnum::false->status());
    }
}
