<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function ticket(){
        return $this->belongsTo(Ticket::class,'ticket_id','id')->latest();
    }

    public function admin(){
        return $this->belongsTo(Admin::class,'admin_id','id')->latest();
    }
}
