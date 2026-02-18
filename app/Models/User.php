<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected static array $columnExistsCache = [];

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

    public function getKeyName()
    {
        return $this->hasTableColumn('idUser') ? 'idUser' : 'id';
    }

    public function usesTimestamps(): bool
    {
        return $this->hasTableColumn('created_at') && $this->hasTableColumn('updated_at');
    }

    protected function hasTableColumn(string $column): bool
    {
        $key = $this->getTable() . '.' . $column;

        if (!array_key_exists($key, self::$columnExistsCache)) {
            self::$columnExistsCache[$key] = Schema::hasColumn($this->getTable(), $column);
        }

        return self::$columnExistsCache[$key];
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
