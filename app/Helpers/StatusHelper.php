<?php

namespace App\Helpers;

class StatusHelper
{
    public static function statusClass(string $status): string
    {
        return match (strtolower($status)) {
            'completed' => 'status-completed',
            'paid' => 'status-paid',
            'dropped off' => 'status-dropped',
            'in process' => 'status-inprocess',
            'created' => 'status-created',
            default => 'status-sandbox',
        };
    }
}
