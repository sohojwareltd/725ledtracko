<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RepairController extends Controller
{
    public function index()
    {
        $rn = Auth::user()->username;
        
        // Get today's repairs for this user
        $modules = DB::select("SELECT * FROM orderdetails WHERE repairer = ? AND DateRepair > timestamp(CURRENT_DATE) ORDER BY DateRepair DESC", [$rn]);
        $row_cnt = count($modules);
        
        // Get last repaired barcode
        $lastResult = DB::select("SELECT Barcode, ModuleModel, Damage, DateRepair, idOrder FROM orderdetails WHERE repairer = ? AND DateRepair > timestamp(CURRENT_DATE) ORDER BY DateRepair DESC LIMIT 1", [$rn]);
        $lastBarcode = !empty($lastResult) ? $lastResult[0] : null;
        
        return view('repair.index', compact('modules', 'row_cnt', 'lastBarcode'));
    }

    public function store(Request $request)
    {
        $rn = Auth::user()->username;
        $Damage = trim($request->input('Damage', ''));
        $DamageArea = trim($request->input('DamageArea', ''));
        $Barcode = trim($request->input('Barcode', ''));
        
        if (empty($Damage) || empty($DamageArea) || empty($Barcode)) {
            return back()->withErrors(['error' => 'Please scan the damage, damage area, and barcode.']);
        }
        
        // Find latest order containing this barcode
        $orderResult = DB::select("SELECT idOrder FROM orderdetails WHERE Barcode = ? ORDER BY idOrder DESC LIMIT 1", [$Barcode]);
        
        if (empty($orderResult)) {
            return back()->withErrors(['error' => 'Barcode not found. Please verify the scan.']);
        }
        
        $idOrder = $orderResult[0]->idOrder;
        
        DB::update("UPDATE orderdetails SET Damage = ?, DateRepair = NOW(), repairer = ?, RepairArea = ? WHERE idOrder = ? AND Barcode = ?", [
            $Damage, $rn, $DamageArea, $idOrder, $Barcode
        ]);
        
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [$rn, "Repair module:$Barcode"]);
        
        return redirect()->route('repair.index');
    }

    public function remove($Barcode, $idOrder)
    {
        $rn = Auth::user()->username;
        
        DB::update("UPDATE orderdetails SET repairer = NULL, DateRepair = NULL WHERE Barcode = ?", [$Barcode]);
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [$rn, "Un repair module:$Barcode"]);
        
        return redirect()->route('repair.index');
    }
}
