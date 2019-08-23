<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function post() {
        return $this->hasOne('App\Post'); //hasOne here has a second arg which is default to user_id, if it isn't the column you created, you can change it to yours.
    }

    public function posts() {
        return $this->hasMany('App\Post');
    }

    public function roles() {
        //if you follow the convention, the format is below
        return $this->belongsToMany('App\Role')->withPivot('updated_at');
        
        //To customize tables name and columns, follow the format below
        //return $this->belongsToMany('App\Role', 'user_roles', 'user_id', 'role_id');
        
    }

    public function photos() {
        return $this->morphMany('App\Photo', 'imageable');
    }
}
