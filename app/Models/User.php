<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use App\Models\Permission;
use App\Notifications\CustomResetPasswordNotification;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // Table name (optional, if not following Laravel naming conventions)
    protected $table = 'users';

    // Use UUIDs instead of auto-incrementing IDs
    public $incrementing = false;
    protected $keyType = 'string';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'userid';

    // Boot method to auto-generate UUIDs
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'userid',
        'name',
        'email',
        'pfno',
        'password',
        'role'
    ];

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }
    //functions
    public function permissions()
    {
        if ($this->isadmin) {
            return Permission::orderBy('priorityno');
        } else {
            return $this->belongsToMany(Permission::class, 'userpermissions', 'useridfk', 'permissionidfk')->orderBy('priorityno');
        }
    }

    public function defaultpermissions()
    {
        $defaultp = Permission::where('targetrole', Auth::user()->role)->orderBy('priorityno');
        return $defaultp;
    }

    public function haspermission($shortname)
    {
        if ($this->issuperadmin()) {
            return true;
        } else {
            return $this->permissions()->where('shortname', $shortname)->exists();
        }
    }
    public function hasselfpermission($shortname)
    {
        return $this->permissions()->where('shortname', $shortname)->exists();
    }
    public function issuperadmin()
    {
        if ($this->isadmin) {
            return true;
        } else {
            return false;
        }
    }
}
