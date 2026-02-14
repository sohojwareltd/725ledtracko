<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAudit extends Model
{
    protected $table = 'user_audits';
    protected $primaryKey = 'idAudit';
    public $timestamps = false;

    protected $fillable = [
        'User',
        'Date',
        'AuditDescription',
        'IPAddress',
        'ActionType',
    ];

    protected $casts = [
        'Date' => 'datetime',
    ];

    // Scope: Get audits for a specific user
    public function scopeForUser($query, $username)
    {
        return $query->where('User', $username);
    }

    // Scope: Get audits by action type
    public function scopeByAction($query, $actionType)
    {
        return $query->where('ActionType', $actionType);
    }

    // Scope: Get recent audits
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('Date', '>=', now()->subDays($days));
    }
}
