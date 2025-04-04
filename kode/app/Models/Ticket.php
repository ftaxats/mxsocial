<?php

namespace App\Models;

use App\Enums\TicketStatus;
use App\Models\Core\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use App\Traits\Filterable;
class Ticket extends Model
{
    use HasFactory , Filterable;

    protected $guarded = [];

    protected $casts = [
        'ticket_data' => 'object',
    ];

    protected static function booted(){
        
        static::creating(function (Model $model) {
            $model->uid        = Str::uuid();
        });

    }

    public function user(){

        return $this->belongsTo(User::class,'user_id','id')->withDefault([
            'username' => '-',
            'name' => '-',
        ]);
    }
 

    public function messages() :HasMany{

        return $this->hasMany(Message::class,'ticket_id','id')->latest();
    }

    public function scopePending(Builder $q) :Builder{

        return $q->where("status",TicketStatus::PENDING->value);
    }


    public function scopeSolved(Builder $q) :Builder{

        return $q->where("status",TicketStatus::SOLVED->value);
    }

    public function scopeClosed(Builder $q) :Builder{

        return $q->where("status",TicketStatus::CLOSED->value);
    }

    public function scopeHold(Builder $q) :Builder{

        return $q->where("status",TicketStatus::HOLD->value);
    }

    public function file() :MorphMany{

        return $this->morphMany(File::class, 'fileable');
    }

 
}
