<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        
        $modules = DB::select(
            "SELECT ModuleName FROM modules WHERE idModule IN (
                SELECT idModules FROM companymodules WHERE idCompany = (
                    SELECT idCompany FROM company WHERE CompanyName = (
                        SELECT CompanyName FROM orders WHERE idOrder = ?
                    )
                )
            )",
            [$idOrder]
        );
        
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
        $rawBarcode = trim((string) $request->input('Barcode', ''));
        $Barcode = preg_replace('/\D+/', '', $rawBarcode) ?? '';
        
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
        $countmod = (int) $request->input('countmod', 0);

        $updatePayload = [
            'OrderStatus' => 'Dropped off',
            'TotModulesReceived' => $countmod,
        ];

        // Legacy parity: historical PHP flow stores reception completion in DateOrderReceived.
        if (Schema::hasColumn('orders', 'DateOrderReceived')) {
            $updatePayload['DateOrderReceived'] = now();
        }

        // Some converted schemas introduced DateDroppedOff. Keep both when available.
        if (Schema::hasColumn('orders', 'DateDroppedOff')) {
            $updatePayload['DateDroppedOff'] = now();
        }

        DB::table('orders')->where('idOrder', $idOrder)->update($updatePayload);
        
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
