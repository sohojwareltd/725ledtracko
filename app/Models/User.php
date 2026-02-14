<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'username',
        'password',
        'role',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // DO NOT override getAuthIdentifierName - defaults to 'id'
    // The old override returning 'username' caused login failure
    // because Auth stored numeric ID but queried WHERE username = 1

    public function hasRole($roleCheck)
    {
        return $this->role === $roleCheck;
    }

    public function isAdmin()
    {
        return $this->role === 'Admin';
    }
}
