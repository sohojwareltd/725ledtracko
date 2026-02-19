<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    private function getDashboardData()
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

        // Check if duedate column exists in orders table
        $hasDuedate = false;
        try {
            $columns = DB::select("SHOW COLUMNS FROM orders LIKE 'duedate'");
            $hasDuedate = !empty($columns);
        } catch (\Exception $e) {
            $hasDuedate = false;
        }

        $duedateSelect = $hasDuedate ? ', orders.duedate' : ', NULL as duedate';
        $duedateGroup = $hasDuedate ? ', orders.duedate' : '';

        $ordersProgress = DB::select("
            SELECT orders.idOrder, orders.CompanyName, orders.TotModulesReceived{$duedateSelect},
            COUNT(orderdetails.DateRepair) as repaired,
            CASE WHEN orders.TotModulesReceived > 0
                THEN TRUNCATE((COUNT(orderdetails.DateRepair) / orders.TotModulesReceived) * 100, 1)
                ELSE 0 END as Perprogress,
            COUNT(orderdetails.DateQC) as QC,
            CASE WHEN orders.TotModulesReceived > 0
                THEN TRUNCATE((COUNT(orderdetails.DateQC) / orders.TotModulesReceived) * 100, 1)
                ELSE 0 END as PerprogressQC
            FROM orders
            INNER JOIN orderdetails ON orders.idOrder = orderdetails.idOrder
            WHERE orders.OrderStatus = 'In Process'
            GROUP BY orders.idOrder, orders.CompanyName, orders.TotModulesReceived{$duedateGroup}
        ");

        return compact('techOutput', 'techOrders', 'qcStats', 'totalRepairs', 'message', 'ordersProgress');
    }

    public function index()
    {
        try {
            $data = $this->getDashboardData();
            return view('dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            return view('dashboard', [
                'techOutput' => [],
                'techOrders' => [],
                'qcStats' => [],
                'totalRepairs' => 0,
                'message' => null,
                'ordersProgress' => [],
            ])->with('error', 'Could not load dashboard data: ' . $e->getMessage());
        }
    }

    public function refresh()
    {
        try {
            $data = $this->getDashboardData();

            return response()->json([
                'stats' => view('dashboard._stats', $data)->render(),
                'orders' => view('dashboard._orders', $data)->render(),
                'timestamp' => date('H:i:s'),
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard refresh error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
