<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $techOutput = DB::select("SELECT COUNT(*) as qty, repairer FROM orderdetails WHERE DateRepair > timestamp(CURRENT_DATE) AND repairer IS NOT NULL GROUP BY repairer");
        $techOrders = DB::select("SELECT repairer, idOrder, COUNT(*) as qty FROM orderdetails WHERE DateRepair > timestamp(CURRENT_DATE) AND repairer IS NOT NULL GROUP BY repairer, idOrder");
        $qcStats = DB::select("SELECT COUNT(*) as qty, QCAgent FROM orderdetails WHERE DateQC > timestamp(CURRENT_DATE) AND QCAgent IS NOT NULL GROUP BY QCAgent");
        $totalResult = DB::select("SELECT COUNT(*) as Total FROM orderdetails WHERE DateRepair > timestamp(CURRENT_DATE)");
        $totalRepairs = $totalResult[0]->Total ?? 0;
        
        // Messages table is optional - gracefully handle if it doesn't exist
        try {
            $messageResult = DB::select("SELECT * FROM messages ORDER BY Id_Message DESC LIMIT 1");
            $message = !empty($messageResult) ? $messageResult[0] : null;
        } catch (\Exception $e) {
            $message = null;
        }
        
        $ordersProgress = DB::select("
            SELECT orders.idOrder, orders.CompanyName, orders.TotModulesReceived, orders.duedate,
            COUNT(orderdetails.DateRepair) as repaired,
            TRUNCATE((COUNT(orderdetails.DateRepair) / orders.TotModulesReceived) * 100, 1) as Perprogress,
            COUNT(orderdetails.DateQC) as QC,
            TRUNCATE((COUNT(orderdetails.DateQC) / orders.TotModulesReceived) * 100, 1) as PerprogressQC
            FROM orders
            INNER JOIN orderdetails ON orders.idOrder = orderdetails.idOrder
            WHERE orders.OrderStatus = 'In Process'
            GROUP BY orderdetails.idOrder, orders.CompanyName, orders.TotModulesReceived, orders.duedate
        ");

        return view('dashboard', compact('techOutput', 'techOrders', 'qcStats', 'totalRepairs', 'message', 'ordersProgress'));
    }

    public function refresh()
    {
        // For AJAX refresh - return same queries
        $techOutput = DB::select("SELECT COUNT(*) as qty, repairer FROM orderdetails WHERE DateRepair > timestamp(CURRENT_DATE) AND repairer IS NOT NULL GROUP BY repairer");
        $techOrders = DB::select("SELECT repairer, idOrder, COUNT(*) as qty FROM orderdetails WHERE DateRepair > timestamp(CURRENT_DATE) AND repairer IS NOT NULL GROUP BY repairer, idOrder");
        $qcStats = DB::select("SELECT COUNT(*) as qty, QCAgent FROM orderdetails WHERE DateQC > timestamp(CURRENT_DATE) AND QCAgent IS NOT NULL GROUP BY QCAgent");
        $totalResult = DB::select("SELECT COUNT(*) as Total FROM orderdetails WHERE DateRepair > timestamp(CURRENT_DATE)");
        $totalRepairs = $totalResult[0]->Total ?? 0;
        
        try {
            $messageResult = DB::select("SELECT * FROM messages ORDER BY Id_Message DESC LIMIT 1");
            $message = !empty($messageResult) ? $messageResult[0] : null;
        } catch (\Exception $e) {
            $message = null;
        }
        
        $ordersProgress = DB::select("
            SELECT orders.idOrder, orders.CompanyName, orders.TotModulesReceived, orders.duedate,
            COUNT(orderdetails.DateRepair) as repaired,
            TRUNCATE((COUNT(orderdetails.DateRepair) / orders.TotModulesReceived) * 100, 1) as Perprogress,
            COUNT(orderdetails.DateQC) as QC,
            TRUNCATE((COUNT(orderdetails.DateQC) / orders.TotModulesReceived) * 100, 1) as PerprogressQC
            FROM orders
            INNER JOIN orderdetails ON orders.idOrder = orderdetails.idOrder
            WHERE orders.OrderStatus = 'In Process'
            GROUP BY orderdetails.idOrder, orders.CompanyName, orders.TotModulesReceived, orders.duedate
        ");

        return response()->json([
            'stats' => view('partials.dashboard_stats', compact('techOutput', 'techOrders', 'qcStats', 'totalRepairs', 'message'))->render(),
            'orders' => view('partials.dashboard_orders', compact('ordersProgress'))->render(),
            'timestamp' => date('H:i:s'),
        ]);
    }
}
