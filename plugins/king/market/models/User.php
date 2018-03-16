<?php

namespace King\Market\Models;

use Model;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use Notifiable;
    public $table = 'users';
    protected $connection = 'mysqlapp';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','mobile'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $attachOne = [
        'icon' => \System\Models\File::class
    ];

    public $attachMany = [
        'featured_images' => ['System\Models\File']
    ];

    
    public function findForPassport($username)
    {
        $user = $this->orWhere('email', $username)->orWhere('mobile', $username)->first();
        return $user;
    }

    /*第一方应用不再通过密码认证*/
    public function validateForPassportPasswordGrant($password)
    {
        return true;
    }
    public function symbols()
    {
       return $this->belongsToMany('King\Market\Models\SymbolApp', 'symbols_users' ,'user_id','symbol_id');
    }
}
