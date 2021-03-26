<?php

namespace App\Entities;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Users extends \Moloquent implements AuthenticatableContract,AuthorizableContract,JWTSubject
{
    use Authenticatable, Authorizable;

    protected $collection = 'users';
    protected $fillable = ["empCode", "firstName", "lastName", "dob", "department", "role", "mobile", "email", "officeEmail", "password", "doj", "branch", "skills", "education", "active"];

    protected static $unguarded = true;

    protected $hidden = [
        'password',
    ];

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'empCode' => $this->empCode,
            'userId' => $this->_id
        ];
    }
}
