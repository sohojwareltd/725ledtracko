<?php

namespace App\Helpers;

class StatusHelper
{
    public static function statusClass(string $status): string
    {
        return match (strtolower($status)) {
            'completed', 'done', 'passed' => 'status-completed',
            'paid' => 'status-paid',
            'dropped off' => 'status-dropped',
            'in process' => 'status-inprocess',
            'created' => 'status-created',
            'deleted', 'delayed', 'inactive', 'rejected' => 'status-sandbox',
            default => 'status-sandbox',
        };
    }
}
