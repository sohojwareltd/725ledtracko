<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

/**
 * @property string|null $username
 * @property string|null $password
 * @property string|null $role
 * @property string|null $UserName
 * @property string|null $Password
 * @property string|null $Role
 * @property string|null $FullName
 * @property int|null $id
 * @property int|null $idUser
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'idUser';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

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

    public function getAuthIdentifierName(): string
    {
        // Support both legacy (`idUser`) and Laravel-default (`id`) user tables.
        try {
            return Schema::hasColumn($this->getTable(), 'idUser') ? 'idUser' : 'id';
        } catch (\Throwable $e) {
            return $this->primaryKey;
        }
    }

    public function getKeyName(): string
    {
        return $this->getAuthIdentifierName();
    }

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
        return strtolower((string) $this->role) === 'admin';
    }
}
