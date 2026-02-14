<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'idOrder';
    public $timestamps = false;

    protected $fillable = [
        'OrderName',
        'OrderDate',
        'CustomerPhone',
        'CustomerEmail',
        'TotalModules',
        'TotModulesReceived',
        'Status',
        'Notes',
        'CreatedBy',
        'DateDroppedOff',
        'DateCompleted',
    ];

    protected $casts = [
        'OrderDate' => 'datetime',
        'DateDroppedOff' => 'datetime',
        'DateCompleted' => 'datetime',
    ];

    // Get all modules in this order
    public function modules(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'idOrder');
    }

    // Get modules that have been received
    public function receivedModules()
    {
        return $this->modules()->whereNotNull('DateReceived');
    }

    // Get modules in repair
    public function modulesInRepair()
    {
        return $this->modules()->whereNotNull('DateReceived')->whereNull('DateRepair');
    }

    // Get modules that have been repaired
    public function repairedModules()
    {
        return $this->modules()->whereNotNull('DateRepair');
    }

    // Get modules passed QC
    public function qcPassedModules()
    {
        return $this->modules()->where('QCStatus', 'Passed');
    }

    // Get modules rejected in QC
    public function qcRejectedModules()
    {
        return $this->modules()->where('QCStatus', 'Rejected');
    }

    // Check if order is complete
    public function isComplete(): bool
    {
        return $this->Status === 'Done' || 
               ($this->TotalModules > 0 && 
                $this->modules()->where('QCStatus', 'Passed')->count() === $this->TotalModules);
    }

    // Get completion percentage
    public function getCompletionPercentage(): int
    {
        if ($this->TotalModules === 0) return 0;
        $completed = $this->modules()->where('QCStatus', 'Passed')->count();
        return round(($completed / $this->TotalModules) * 100);
    }
}
