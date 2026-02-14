<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // EXACT original SQL with correct DB column names: repairer, DateRepair, DateQC, QCAgent
        $techOutput = DB::select("SELECT COUNT(*) as qty, repairer FROM orderdetails WHERE DateRepair > timestamp(CURRENT_DATE) GROUP BY repairer");
        $techOrders = DB::select("SELECT repairer, idOrder, COUNT(*) as qty FROM orderdetails WHERE DateRepair > timestamp(CURRENT_DATE) GROUP BY repairer, idOrder");
        $qcStats = DB::select("SELECT COUNT(*) as qty, QCAgent FROM orderdetails WHERE DateQC > timestamp(CURRENT_DATE) GROUP BY QCAgent");
        $totalResult = DB::select("SELECT COUNT(*) as Total FROM orderdetails WHERE DateRepair > timestamp(CURRENT_DATE)");
        $totalRepairs = $totalResult[0]->Total ?? 0;
        $messageResult = DB::select("SELECT * FROM messages ORDER BY Id_Message DESC LIMIT 1");
        $message = !empty($messageResult) ? $messageResult[0] : null;
        $ordersProgress = DB::select("
            SELECT orders.duedate, orders.idOrder, orders.CompanyName, orders.TotModulesReceived, 
            COUNT(orderdetails.DateRepair) as repaired,
            TRUNCATE((COUNT(orderdetails.DateRepair) / orders.TotModulesReceived) * 100, 1) as Perprogress,
            COUNT(orderdetails.DateQC) as QC,
            TRUNCATE((COUNT(orderdetails.DateQC) / orders.TotModulesReceived) * 100, 1) as PerprogressQC
            FROM orders, orderdetails 
            WHERE orders.OrderStatus = 'In Process' AND orders.idOrder = orderdetails.idOrder
            GROUP BY orderdetails.idOrder
        ");

        return view('dashboard', compact('techOutput', 'techOrders', 'qcStats', 'totalRepairs', 'message', 'ordersProgress'));
    }

    public function refresh()
    {
        // For AJAX refresh - return same queries
        $techOutput = DB::select("SELECT COUNT(*) as qty, repairer FROM orderdetails WHERE DateRepair > timestamp(CURRENT_DATE) GROUP BY repairer");
        $techOrders = DB::select("SELECT repairer, idOrder, COUNT(*) as qty FROM orderdetails WHERE DateRepair > timestamp(CURRENT_DATE) GROUP BY repairer, idOrder");
        $qcStats = DB::select("SELECT COUNT(*) as qty, QCAgent FROM orderdetails WHERE DateQC > timestamp(CURRENT_DATE) GROUP BY QCAgent");
        $totalResult = DB::select("SELECT COUNT(*) as Total FROM orderdetails WHERE DateRepair > timestamp(CURRENT_DATE)");
        $totalRepairs = $totalResult[0]->Total ?? 0;
        $messageResult = DB::select("SELECT * FROM messages ORDER BY Id_Message DESC LIMIT 1");
        $message = !empty($messageResult) ? $messageResult[0] : null;
        $ordersProgress = DB::select("
            SELECT orders.duedate, orders.idOrder, orders.CompanyName, orders.TotModulesReceived, 
            COUNT(orderdetails.DateRepair) as repaired,
            TRUNCATE((COUNT(orderdetails.DateRepair) / orders.TotModulesReceived) * 100, 1) as Perprogress,
            COUNT(orderdetails.DateQC) as QC,
            TRUNCATE((COUNT(orderdetails.DateQC) / orders.TotModulesReceived) * 100, 1) as PerprogressQC
            FROM orders, orderdetails 
            WHERE orders.OrderStatus = 'In Process' AND orders.idOrder = orderdetails.idOrder
            GROUP BY orderdetails.idOrder
        ");

        return response()->json([
            'stats' => view('partials.dashboard_stats', compact('techOutput', 'techOrders', 'qcStats', 'totalRepairs', 'message'))->render(),
            'orders' => view('partials.dashboard_orders', compact('ordersProgress'))->render(),
            'timestamp' => date('H:i:s'),
        ]);
    }
}
