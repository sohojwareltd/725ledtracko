<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerTrackingController extends Controller
{
    public function index()
    {
        return view('customer.track');
    }

    public function track(Request $request)
    {
        $idOrderRaw = trim($request->input('idOrder', ''));

        if ($idOrderRaw === '' || !ctype_digit($idOrderRaw)) {
            return view('customer.track', [
                'error' => 'Please enter a valid numeric order number.',
            ]);
        }

        $idOrder = (int) $idOrderRaw;

        $orderResult = DB::select("SELECT * FROM orders WHERE idOrder = ?", [$idOrder]);

        if (empty($orderResult)) {
            return view('customer.track', [
                'error' => "We couldn't find an order with that number. Please try again.",
            ]);
        }

        $order = $orderResult[0];

        $repairedResult = DB::select(
            "SELECT COUNT(*) as sumatoria FROM orderdetails WHERE idOrder = ? AND DateRepair IS NOT NULL",
            [$idOrder]
        );
        $repaired = $repairedResult[0]->sumatoria ?? 0;

        $qcResult = DB::select(
            "SELECT COUNT(*) as sumatoria FROM orderdetails WHERE idOrder = ? AND QCStatus LIKE '%Passed%'",
            [$idOrder]
        );
        $qcPassed = $qcResult[0]->sumatoria ?? 0;

        $progress = 0;
        if (($order->TotModulesReceived ?? 0) > 0) {
            $progress = round(($repaired / $order->TotModulesReceived) * 100, 1);
        }

        return view('customer.track', compact('order', 'repaired', 'qcPassed', 'progress', 'idOrder'));
    }
}
