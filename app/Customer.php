<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $table = 'lly_customer';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'role_id', 'name', 'username', 'email', 'tel', 'address', 'logo', 'lat', 'lng',
        'remark', 'isusing', 'deleted', 'deleted_at', 'deleted_user', 'isusing_user', 'update_user',
        'login_at', 'openid', 'wx_name', 'wx_logo', 'created_at', 'updated_at', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public static function findForPassport($username)
    {
        return self::where('username', $username)->first();
    }
}
