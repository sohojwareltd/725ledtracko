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
        'CompanyName',
        'idUser',
        'idUserLastUpdated',
        'TotModulesCaptured',
        'TotModulesReceived',
        'OrderStatus',
        'DateOrderCaptured',
        'DateLastModification',
        'DateOrderReceived',
        'Location',
        'duedate',
        'DateDroppedOff',
    ];

    protected $casts = [
        'DateOrderCaptured' => 'datetime',
        'DateLastModification' => 'datetime',
        'DateOrderReceived' => 'datetime',
        'duedate' => 'datetime',
        'DateDroppedOff' => 'datetime',
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
        return in_array(strtolower((string) $this->OrderStatus), ['completed', 'done'], true);
    }

    // Get completion percentage
    public function getCompletionPercentage(): int
    {
        if ((int) $this->TotModulesReceived === 0) {
            return 0;
        }

        $completed = $this->modules()->where('QCStatus', 'Passed')->count();
        return (int) round(($completed / (int) $this->TotModulesReceived) * 100);
    }
}
