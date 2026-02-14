<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function index()
    {
        return view('tracking.index');
    }

    public function trackModule(Request $request)
    {
        $Barcode = $request->input('Barcode', '');
        
        $modules = DB::select("SELECT * FROM orderdetails WHERE Barcode = ?", [$Barcode]);
        
        return view('tracking.module', compact('modules', 'Barcode'));
    }

    public function trackOrder(Request $request, $idOrder = null)
    {
        // Handle both GET and POST
        $idOrder = $idOrder ?? $request->input('idOrder');
        
        if (empty($idOrder)) {
            return redirect()->route('tracking.index');
        }
        
        $where = "";
        $Damage = $request->input('Damage', 'ALL');
        $Model = $request->input('Model', 'ALL');
        
        // Filter logic matching original TrackOrder.php - uses LIKE not =
        if ($Damage === '' && $Model === '') {
            // No filter when both empty
            $Damage = 'ALL';
            $Model = 'ALL';
        } elseif ($Damage === "ALL" && $Model === "ALL") {
            // No filter - show all
        } elseif ($Damage === "ALL" && $Model !== "ALL") {
            $where = " AND ModuleModel LIKE " . DB::connection()->getPdo()->quote($Model);
        } elseif ($Damage === "NULL") {
            $where = " AND Damage IS NULL AND ModuleModel LIKE " . DB::connection()->getPdo()->quote($Model);
        } elseif ($Damage !== "ALL" && $Model === "ALL") {
            $where = " AND Damage LIKE " . DB::connection()->getPdo()->quote($Damage);
        } else {
            $where = " AND Damage LIKE " . DB::connection()->getPdo()->quote($Damage) . " AND ModuleModel LIKE " . DB::connection()->getPdo()->quote($Model);
        }
        
        $modules = DB::select("SELECT * FROM orderdetails WHERE idOrder = ? $where", [$idOrder]);
        $numRowsDyn = count($modules);
        
        $orderResult = DB::select("SELECT * FROM orders WHERE idOrder = ?", [$idOrder]);
        $order = !empty($orderResult) ? $orderResult[0] : abort(404);
        
        $sumResult = DB::select("SELECT count(*) as sumatoria FROM orderdetails WHERE idOrder = ? AND DateRepair IS NOT NULL", [$idOrder]);
        $repaired = $sumResult[0]->sumatoria;
        
        $qcResult = DB::select("SELECT count(*) as sumatoria FROM orderdetails WHERE idOrder = ? AND QCStatus LIKE '%Passed%'", [$idOrder]);
        $qcPassed = $qcResult[0]->sumatoria;
        
        $qcRejectedResult = DB::select("SELECT count(*) as sumatoria FROM orderdetails WHERE idOrder = ? AND QCStatus NOT LIKE '%Passed%'", [$idOrder]);
        $qcRejected = $qcRejectedResult[0]->sumatoria;
        
        $damageGroups = DB::select("SELECT Count(*) as count, Damage FROM orderdetails WHERE idOrder = ? GROUP BY Damage", [$idOrder]);
        $modelGroups = DB::select("SELECT Count(*) as count, ModuleModel FROM orderdetails WHERE idOrder = ? GROUP BY ModuleModel", [$idOrder]);
        
        return view('tracking.order', compact('order', 'modules', 'numRowsDyn', 'repaired', 'qcPassed', 'qcRejected', 'damageGroups', 'modelGroups', 'Damage', 'Model', 'idOrder'));
    }

    public function printOrder($idOrder)
    {
        $orderResult = DB::select("SELECT * FROM orders WHERE idOrder = ?", [$idOrder]);
        $order = !empty($orderResult) ? $orderResult[0] : abort(404);
        
        $sumResult = DB::select("SELECT COUNT(*) as sumatoria FROM orderdetails WHERE idOrder = ? AND DateRepair IS NOT NULL", [$idOrder]);
        $repaired = $sumResult[0]->sumatoria;
        
        $qcResult = DB::select("SELECT COUNT(*) as sumatoria FROM orderdetails WHERE idOrder = ? AND QCStatus LIKE '%Passed%'", [$idOrder]);
        $qcPassed = $qcResult[0]->sumatoria;
        
        $qcRejectedResult = DB::select("SELECT COUNT(*) as sumatoria FROM orderdetails WHERE idOrder = ? AND QCStatus NOT LIKE '%Passed%'", [$idOrder]);
        $qcRejected = $qcRejectedResult[0]->sumatoria;
        
        $modules = DB::select("SELECT * FROM orderdetails WHERE idOrder = ? ORDER BY Barcode ASC", [$idOrder]);
        $numModules = count($modules);
        
        return view('tracking.print', compact('order', 'modules', 'numModules', 'repaired', 'qcPassed', 'qcRejected', 'idOrder'));
    }
}
