<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceptionController extends Controller
{
    public function index()
    {
        $orders = DB::select("SELECT * FROM orders WHERE OrderStatus = 'Created'");
        $pending_orders = count($orders);
        
        return view('reception.index', compact('orders', 'pending_orders'));
    }

    public function receive($idOrder)
    {
        $orderResult = DB::select("SELECT * FROM orders WHERE idOrder = ?", [$idOrder]);
        $order = !empty($orderResult) ? $orderResult[0] : abort(404);
        
        $countResult = DB::select("SELECT COUNT(*) as countmod FROM orderdetails WHERE idOrder = ?", [$idOrder]);
        $countmod = $countResult[0]->countmod;
        
        $detailsResult = DB::select("SELECT idOrder, ModuleModel, COUNT(*) as countable FROM orderdetails WHERE idOrder = ? GROUP BY idOrder, ModuleModel ORDER BY ModuleModel", [$idOrder]);
        $details = $detailsResult;
        
        $modules = []; // TODO: modules, companymodules, company tables not in migration
        
        $topResult = DB::select("SELECT ModuleModel from orderdetails where idOrder = ? order by DateReceived desc LIMIT 1", [$idOrder]);
        $topModel = !empty($topResult) ? $topResult[0]->ModuleModel : null;
        
        $lastBarcodeResult = DB::select("SELECT Barcode, ModuleModel, DateReceived FROM orderdetails WHERE idOrder = ? ORDER BY DateReceived DESC LIMIT 1", [$idOrder]);
        $lastBarcode = !empty($lastBarcodeResult) ? $lastBarcodeResult[0] : null;
        
        return view('reception.receive', compact('order', 'countmod', 'details', 'modules', 'topModel', 'lastBarcode'));
    }

    public function addModule(Request $request, $idOrder)
    {
        $rn = Auth::user()->username;
        $ModelModule = trim($request->input('ModelModule', ''));
        $Barcode = trim($request->input('Barcode', ''));
        
        if (empty($ModelModule) || empty($Barcode)) {
            return redirect()->route('reception.receive', $idOrder)->with('error', 'required');
        }
        
        if (!preg_match('/^[0-9]+$/', $Barcode)) {
            return redirect()->route('reception.receive', $idOrder)->with('error', 'invalid');
        }
        
        // Check duplicate
        $dupResult = DB::select("SELECT 1 FROM orderdetails WHERE idOrder = ? AND Barcode = ? LIMIT 1", [$idOrder, $Barcode]);
        if (!empty($dupResult)) {
            return redirect()->route('reception.receive', $idOrder)->with('error', 'duplicate');
        }
        
        DB::insert("INSERT INTO orderdetails (idOrder, ModuleModel, Barcode, DateReceived) VALUES (?, ?, ?, NOW())", [$idOrder, $ModelModule, $Barcode]);
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), 'New Order detail Captured')", [$rn]);
        
        return redirect()->route('reception.receive', $idOrder)->with('success', '1');
    }

    public function complete(Request $request, $idOrder)
    {
        $countmod = $request->input('countmod', 0);
        
        DB::update("UPDATE orders SET OrderStatus = 'Dropped off', TotModulesReceived = ?, DateDroppedOff = NOW() WHERE idOrder = ?", [$countmod, $idOrder]);
        
        return redirect()->route('reception.index');
    }

    public function details($idOrder)
    {
        $orderResult = DB::select("SELECT * FROM orders WHERE idOrder = ?", [$idOrder]);
        $order = !empty($orderResult) ? $orderResult[0] : abort(404);
        
        $details = DB::select("SELECT * FROM orderdetails WHERE idOrder = ? ORDER BY ModuleModel, Barcode", [$idOrder]);
        
        $countResult = DB::select("SELECT COUNT(*) as countmod FROM orderdetails WHERE idOrder = ?", [$idOrder]);
        $countmod = $countResult[0]->countmod;
        
        return view('reception.details', compact('order', 'details', 'countmod', 'idOrder'));
    }

    public function deleteModule($idOrder, $Barcode)
    {
        DB::delete("DELETE FROM orderdetails WHERE Barcode = ?", [$Barcode]);
        
        return redirect()->route('reception.details', $idOrder);
    }
}
