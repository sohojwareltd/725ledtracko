<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    protected $table = 'order_details';
    public $timestamps = false;

    protected $fillable = [
        'idOrder',
        'Barcode',
        'ModuleModel',
        'Damage',
        'DateReceived',
        'ReceivedBy',
        'DateRepair',
        'RepairedBy',
        'RepairNotes',
        'QCStatus',
        'QCDate',
        'QCAgent',
        'QCNotes',
        'RepairTime',
    ];

    protected $casts = [
        'DateReceived' => 'datetime',
        'DateRepair' => 'datetime',
        'QCDate' => 'datetime',
    ];

    // Belongs to Order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'idOrder');
    }

    // Get the technician who repaired this module
    public function technician()
    {
        return User::where('UserName', $this->RepairedBy)->first();
    }

    // Get the QC agent who inspected this module
    public function qcAgent()
    {
        return User::where('UserName', $this->QCAgent)->first();
    }

    // Get the reception staff who received this module
    public function receptionist()
    {
        return User::where('UserName', $this->ReceivedBy)->first();
    }

    // Get module status
    public function getStatus(): string
    {
        if (is_null($this->DateReceived)) {
            return 'Not Received';
        }
        if (is_null($this->DateRepair)) {
            return 'Awaiting Repair';
        }
        if (is_null($this->QCDate)) {
            return 'Awaiting QC';
        }
        if ($this->QCStatus === 'Passed') {
            return 'Ready for Delivery';
        }
        if ($this->QCStatus === 'Rejected') {
            return 'Rejected - Back to Repair';
        }
        return 'Pending';
    }

    // Check if module can be repaired (received but not yet repaired)
    public function canBeRepaired(): bool
    {
        return !is_null($this->DateReceived) && is_null($this->DateRepair);
    }

    // Check if module can undergo QC (repaired but not yet inspected)
    public function canUndergoQC(): bool
    {
        return !is_null($this->DateRepair) && is_null($this->QCDate);
    }

    // Get repair duration in hours
    public function getRepairDurationHours(): float
    {
        if (is_null($this->RepairTime)) return 0;
        return $this->RepairTime / 60;
    }
}
