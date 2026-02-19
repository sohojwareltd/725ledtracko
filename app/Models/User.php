<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string|null $username
 * @property string|null $password
 * @property string|null $role
 * @property string|null $UserName
 * @property string|null $Password
 * @property string|null $Role
 * @property string|null $FullName
 * @property int $id
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'UserName',
        'Password',
        'Role',
        'FullName',
        'Active',
        'CreatedDate',
        'LastLogin',
        'username',
        'password',
        'role',
        'remember_token',
    ];

    protected $hidden = [
        'Password',
        'password',
        'remember_token',
    ];

    // DO NOT override getAuthIdentifierName - defaults to 'id'
    // The old override returning 'username' caused login failure
    // because Auth stored numeric ID but queried WHERE username = 1

    public function getUsernameAttribute(): ?string
    {
        return $this->attributes['UserName'] ?? $this->attributes['username'] ?? null;
    }

    public function getPasswordAttribute(): ?string
    {
        return $this->attributes['Password'] ?? $this->attributes['password'] ?? null;
    }

    public function getRoleAttribute(): ?string
    {
        return $this->attributes['Role'] ?? $this->attributes['role'] ?? null;
    }

    public function getAuthPassword(): string
    {
        return (string) ($this->Password ?? $this->password ?? '');
    }

    public function hasRole($roleCheck)
    {
        return $this->role === $roleCheck;
    }

    public function isAdmin()
    {
        return $this->role === 'Admin';
    }
}
