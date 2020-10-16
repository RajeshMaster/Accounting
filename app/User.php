<?php

namespace App;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = '';
    protected $table = "dev_mstuser";
    protected $fillable = [
        'userid', 'username', 'Password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function __construct($conn=null, $request=null)
    {
        
        $this->connection = 'mysql';
    }
    /**
    * Overrides the method to ignore the remember token.
    */
    public function setAttribute($key, $value)
    {
        $isRememberTokenAttribute = $key == $this->getRememberTokenName();
        if (!$isRememberTokenAttribute)
        {
          parent::setAttribute($key, $value);
        }
    }
    public static function fnfetchscreenname() {
        $certificateName = DB::TABLE('dev_ourdetails')
                                ->SELECT('systemname')
                                ->get();
        return $certificateName;
    }

}