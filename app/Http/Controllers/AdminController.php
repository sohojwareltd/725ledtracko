<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // Admin users only (matching original Admin.php check)
        $user = Auth::user();
        $rn = strtolower(trim($user->username ?? ''));
        
        // Check if user is admin (case-insensitive)
        if (!in_array($rn, ['pepe', 'ale', 'luis'], true)) {
            return redirect('/')->with('error', "Access denied. Administrator access only.");
        }
        
        $messageResult = DB::select("SELECT * FROM messages ORDER BY Id_Message DESC LIMIT 1");
        $message = !empty($messageResult) ? $messageResult[0] : null;
        
        return view('admin.index', compact('message'));
    }

    public function updateMessage(Request $request)
    {
        $rn = strtolower(trim(Auth::user()->username ?? ''));
        
        if (!in_array($rn, ['pepe', 'ale', 'luis'], true)) {
            return redirect('/')->with('error', 'Access denied.');
        }
        
        $Message = $request->input('Message', '');
        $Message2 = $request->input('Message2', '');
        $Message3 = $request->input('Message3', '');
        
        if (!empty($Message)) {
            DB::update("UPDATE messages SET Message = ?, Message2 = ?, Message3 = ?", [$Message, $Message2, $Message3]);
        }
        
        return redirect()->route('admin.index');
    }

    public function setRepaired(Request $request)
    {
        $rn = strtolower(trim(Auth::user()->username ?? ''));
        
        if (!in_array($rn, ['pepe', 'ale', 'luis'], true)) {
            return redirect('/')->with('error', 'Access denied.');
        }
        
        $idOrder = $request->input('idOrder', '');
        
        if (empty($idOrder)) {
            return back()->with('error', 'Order ID is required.');
        }
        
        // Set all modules of this order as repaired by current user
        DB::update("UPDATE orderdetails SET repairer = ?, DateRepair = NOW() WHERE idOrder = ? AND DateRepair IS NULL", [Auth::user()->username, $idOrder]);
        
        // Log audit
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [Auth::user()->username, "Set all modules repaired for order $idOrder"]);
        
        return redirect()->route('admin.index')->with('success', "All modules in Order #$idOrder marked as repaired.");
    }
}
