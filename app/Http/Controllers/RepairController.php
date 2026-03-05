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
        $rawBarcode = trim((string) $request->input('Barcode', ''));
        $Barcode = preg_replace('/\D+/', '', $rawBarcode) ?? '';

        $lookupBarcodes = $this->barcodeLookupCandidates($Barcode);

        if (empty($Damage) || empty($DamageArea) || empty($lookupBarcodes)) {
            return redirect()->route('repair.index')->with('error', 'Please scan the damage, damage area, and barcode.');
        }

        $placeholders = implode(', ', array_fill(0, count($lookupBarcodes), '?'));
        $orderResult = DB::select(
            "SELECT idOrderDetail, idOrder, Barcode FROM orderdetails WHERE Barcode IN ($placeholders) ORDER BY idOrder DESC, idOrderDetail DESC LIMIT 1",
            $lookupBarcodes
        );
        
        if (empty($orderResult)) {
            return redirect()->route('repair.index')->with('error', 'Barcode not found. Please verify the scan.');
        }
        
        $idOrderDetail = $orderResult[0]->idOrderDetail;
        $idOrder = $orderResult[0]->idOrder;
        $matchedBarcode = (string) $orderResult[0]->Barcode;

        // If the scanner provides a zero-padded barcode that maps to legacy numeric data,
        // persist the padded value so operators consistently see what they scanned.
        if (
            str_starts_with($Barcode, '0')
            && $Barcode !== $matchedBarcode
            && ltrim($Barcode, '0') === ltrim($matchedBarcode, '0')
        ) {
            DB::update("UPDATE orderdetails SET Barcode = ? WHERE idOrderDetail = ?", [$Barcode, $idOrderDetail]);
            $matchedBarcode = $Barcode;
        }
        
        $updatedRows = DB::update("UPDATE orderdetails SET Damage = ?, DateRepair = NOW(), repairer = ?, RepairArea = ? WHERE idOrderDetail = ?", [
            $Damage, $rn, $DamageArea, $idOrderDetail
        ]);

        if ($updatedRows === 0) {
            // MySQL can return 0 when data is already the same (or double-submit happens quickly).
            // If the matched row still exists, treat this as a successful capture to avoid false errors.
            $exists = DB::select(
                "SELECT 1 FROM orderdetails WHERE idOrderDetail = ? LIMIT 1",
                [$idOrderDetail]
            );

            if (empty($exists)) {
                return redirect()->route('repair.index')->with('error', 'Could not add module. Please scan again.');
            }
        }
        
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [$rn, "Repair module:$matchedBarcode"]);
        
        return redirect()->route('repair.index')->with('success', "Module $matchedBarcode added to repair.");
    }

    private function barcodeLookupCandidates(string $barcode): array
    {
        if ($barcode === '') {
            return [];
        }

        $candidates = [$barcode];

        $trimmed = $barcode;
        while (str_starts_with($trimmed, '0') && strlen($trimmed) > 1) {
            $trimmed = substr($trimmed, 1);
            $candidates[] = $trimmed;
        }

        return array_values(array_unique(array_filter($candidates, static fn (string $value): bool => $value !== '')));
    }

    public function remove($Barcode, $idOrder)
    {
        $rn = Auth::user()->username;
        
        DB::update("UPDATE orderdetails SET repairer = NULL, DateRepair = NULL WHERE Barcode = ?", [$Barcode]);
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [$rn, "Un repair module:$Barcode"]);
        
        return redirect()->route('repair.index');
    }
}
