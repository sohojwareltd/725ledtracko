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
        $digitsBarcode = preg_replace('/\D+/', '', $rawBarcode) ?? '';

        // Keep legacy behavior: if full value does not match, try numeric and last-4 candidates.
        $barcodeCandidates = array_values(array_unique(array_filter([
            $rawBarcode,
            $digitsBarcode,
            $digitsBarcode !== '' ? ltrim($digitsBarcode, '0') : '',
            strlen($digitsBarcode) >= 4 ? substr($digitsBarcode, -4) : '',
        ], static fn ($v) => $v !== '')));
        
        if (empty($Damage) || empty($DamageArea) || empty($barcodeCandidates)) {
            return redirect()->route('repair.index')->with('error', 'Please scan the damage, damage area, and barcode.');
        }
        
        $orderResult = [];
        $matchedBarcode = null;

        foreach ($barcodeCandidates as $candidate) {
            $orderResult = DB::select(
                "SELECT idOrder, Barcode FROM orderdetails WHERE Barcode = ? ORDER BY idOrder DESC LIMIT 1",
                [$candidate]
            );

            if (!empty($orderResult)) {
                $matchedBarcode = (string) $orderResult[0]->Barcode;
                break;
            }
        }
        
        if (empty($orderResult)) {
            return redirect()->route('repair.index')->with('error', 'Barcode not found. Please verify the scan.');
        }
        
        $idOrder = $orderResult[0]->idOrder;
        
        $updatedRows = DB::update("UPDATE orderdetails SET Damage = ?, DateRepair = NOW(), repairer = ?, RepairArea = ? WHERE idOrder = ? AND Barcode = ?", [
            $Damage, $rn, $DamageArea, $idOrder, $matchedBarcode
        ]);

        if ($updatedRows === 0) {
            // MySQL can return 0 when data is already the same (or double-submit happens quickly).
            // If the matched row still exists, treat this as a successful capture to avoid false errors.
            $exists = DB::select(
                "SELECT 1 FROM orderdetails WHERE idOrder = ? AND Barcode = ? LIMIT 1",
                [$idOrder, $matchedBarcode]
            );

            if (empty($exists)) {
                return redirect()->route('repair.index')->with('error', 'Could not add module. Please scan again.');
            }
        }
        
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [$rn, "Repair module:$matchedBarcode"]);
        
        return redirect()->route('repair.index')->with('success', "Module $matchedBarcode added to repair.");
    }

    public function remove($Barcode, $idOrder)
    {
        $rn = Auth::user()->username;
        
        DB::update("UPDATE orderdetails SET repairer = NULL, DateRepair = NULL WHERE Barcode = ?", [$Barcode]);
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [$rn, "Un repair module:$Barcode"]);
        
        return redirect()->route('repair.index');
    }
}
