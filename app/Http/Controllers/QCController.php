<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QCController extends Controller
{
    public function index()
    {
        $rn = Auth::user()->username;
        
        // Get today's QC for this user
        $modules = DB::select("SELECT * FROM orderdetails WHERE QCAgent = ? AND DateQC > timestamp(CURRENT_DATE) ORDER BY DateQC DESC", [$rn]);
        $row_cnt = count($modules);
        
        // Get last QC scanned barcode
        $lastResult = DB::select("SELECT Barcode, ModuleModel, QCStatus, DateQC, idOrder FROM orderdetails WHERE QCAgent = ? AND DateQC > timestamp(CURRENT_DATE) ORDER BY DateQC DESC LIMIT 1", [$rn]);
        $lastBarcode = !empty($lastResult) ? $lastResult[0] : null;
        
        return view('qc.index', compact('modules', 'row_cnt', 'lastBarcode'));
    }

    public function store(Request $request)
    {
        $rn = Auth::user()->username;
        $QCStatus = trim($request->input('QCStatus', ''));
        $Barcode = trim($request->input('Barcode', ''));
        
        if (empty($QCStatus) || empty($Barcode)) {
            return back()->withErrors(['error' => 'QC status and barcode are required.']);
        }
        
        // Find latest order for barcode that already has repair info
        $orderResult = DB::select("SELECT idOrder FROM orderdetails WHERE Barcode = ? AND DateRepair IS NOT NULL ORDER BY idOrder DESC LIMIT 1", [$Barcode]);
        
        if (empty($orderResult)) {
            return back()->withErrors(['error' => 'Barcode not ready for QC (missing repair data).']);
        }
        
        $idOrder = $orderResult[0]->idOrder;
        
        DB::update("UPDATE orderdetails SET QCStatus = ?, DateQC = NOW(), QCAgent = ? WHERE idOrder = ? AND Barcode = ?", [
            $QCStatus, $rn, $idOrder, $Barcode
        ]);
        
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [$rn, "QC module:$Barcode"]);
        
        return redirect()->route('qc.index');
    }

    public function remove($Barcode, $idOrder)
    {
        $rn = Auth::user()->username;
        
        DB::update("UPDATE orderdetails SET QCStatus = NULL, QCAgent = NULL, DateQC = NULL WHERE idOrder = ? AND Barcode = ?", [$idOrder, $Barcode]);
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [$rn, "Remove module from QC:$Barcode"]);
        
        return redirect()->route('qc.index');
    }

    public function rejected()
    {
        // Admin access only
        $user = Auth::user();
        $role = strtolower(trim((string) ($user->role ?? '')));
        
        if ($role !== 'admin') {
            return redirect('/')->with('error', 'Access denied.');
        }
        
        // Get today's rejected modules (QC Status IS NOT NULL and doesn't contain "Passed")
        $modules = DB::select("SELECT repairer as repairer, Barcode, idOrder, QCStatus FROM orderdetails WHERE DateQC > CURRENT_DATE AND QCStatus IS NOT NULL ORDER BY repairer ASC");
        
        return view('qc.rejected', compact('modules'));
    }
}
