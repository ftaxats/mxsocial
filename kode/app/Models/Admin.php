<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Models\Admin\Role;
use App\Models\Core\File;
use App\Models\Core\Otp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

use App\Traits\Notifyable;
class Admin extends Authenticatable
{
    
    use  HasFactory ,Filterable ,SoftDeletes ,Notifyable;


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'role_id',
        'created_by',
        'phone',
        'email',
        'status',
        'password',
        'muted_user',
        'blocked_user',
        'last_login'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        "muted_user" => "array",
        "blocked_user" => "array",
        "notification_settings" =>"object"
    ];

    protected static function booted(){
        static::creating(function (Model $model) {
            $model->uid = Str::uuid();
            $model->created_by = auth_user()?->id;
            $model->status     = StatusEnum::true->status();
        });

        static::updating(function(Model $model) {
            $model->updated_by = auth_user()?auth_user()->id :null;
        });

        
    }

    public function createdBy() :BelongsTo{
        return $this->belongsTo(Admin::class,'created_by','id')->withDefault([
            'username' => '-',
        ]);
    }
    public function updatedBy() :BelongsTo{
        return $this->belongsTo(Admin::class,'updated_by','id')->withDefault([
             'username' => '-',
        ]);
    }

    public function scopeStaff(Builder $q) :Builder{
        return $q->where('super_admin',StatusEnum::false->status());
    }


    public function scopeActive(Builder $q) :Builder{
        return $q->where('status',StatusEnum::true->status());
    }

    public function role() :BelongsTo{
        return $this->belongsTo(Role::class , "role_id","id")->withDefault([
            'name' => '-',
            "permissions" => json_encode([])
       ]);
    }
    
    public function file() :MorphOne{
        return $this->morphOne(File::class, 'fileable');
    }


    public function otp() : MorphMany{
        return $this->MorphMany(Otp::class, 'otpable');
    }


    public function notifications() :MorphMany{
        return $this->MorphMany(Notification::class, 'notificationable');
    }



    




}
