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
        $rawBarcode = trim((string) $request->input('Barcode', ''));
        $Barcode = preg_replace('/\D+/', '', $rawBarcode) ?? '';

        $lookupBarcodes = array_values(array_unique(array_filter([
            $Barcode,
            ltrim($Barcode, '0'),
        ], static fn (string $value): bool => $value !== '')));

        if (empty($QCStatus) || empty($lookupBarcodes)) {
            return redirect()->route('qc.index')->with('error', 'QC status and barcode are required.');
        }

        $placeholders = implode(', ', array_fill(0, count($lookupBarcodes), '?'));

        $orderResult = DB::select(
            "SELECT idOrderDetail, idOrder, Barcode
             FROM orderdetails
             WHERE Barcode IN ($placeholders) AND DateRepair IS NOT NULL
             ORDER BY idOrder DESC, idOrderDetail DESC
             LIMIT 1",
            $lookupBarcodes
        );
        
        if (empty($orderResult)) {
            return redirect()->route('qc.index')->with('error', 'Barcode not ready for QC (missing repair data).');
        }
        
        $idOrderDetail = $orderResult[0]->idOrderDetail;
        $idOrder = $orderResult[0]->idOrder;
        $matchedBarcode = (string) $orderResult[0]->Barcode;

        if (
            str_starts_with($Barcode, '0')
            && $Barcode !== $matchedBarcode
            && ltrim($Barcode, '0') === ltrim($matchedBarcode, '0')
        ) {
            DB::update("UPDATE orderdetails SET Barcode = ? WHERE idOrderDetail = ?", [$Barcode, $idOrderDetail]);
            $matchedBarcode = $Barcode;
        }
        
        $updatedRows = DB::update("UPDATE orderdetails SET QCStatus = ?, DateQC = NOW(), QCAgent = ? WHERE idOrderDetail = ?", [
            $QCStatus, $rn, $idOrderDetail
        ]);

        if ($updatedRows === 0) {
            $exists = DB::select(
                "SELECT 1 FROM orderdetails WHERE idOrderDetail = ? AND DateRepair IS NOT NULL LIMIT 1",
                [$idOrderDetail]
            );

            if (empty($exists)) {
                return redirect()->route('qc.index')->with('error', 'Could not add module to QC. Please scan again.');
            }
        }
        
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [$rn, "QC module:$matchedBarcode"]);
        
        return redirect()->route('qc.index')->with('success', "Module $matchedBarcode added to QC.");
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
