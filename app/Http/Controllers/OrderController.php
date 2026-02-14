<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        
        // Get companies for dropdown
        $companies = DB::select("SELECT CompanyName FROM company ORDER BY CompanyName ASC");
        
        // Build WHERE clause
        $baseWhere = "OrderStatus IN ('In Process','Created', 'Dropped off')";
        $searchWhere = $baseWhere;
        
        if (!empty($search)) {
            $searchEscaped = DB::connection()->getPdo()->quote('%' . $search . '%');
            $searchWhere .= " AND (idOrder LIKE $searchEscaped OR CompanyName LIKE $searchEscaped OR Location LIKE $searchEscaped)";
        }
        
        // Pagination
        $limit = 15;
        $page = (int)$request->input('page', 1);
        $start = ($page - 1) * $limit;
        
        // Get total count
        $countResult = DB::select("SELECT COUNT(*) AS total FROM orders WHERE $searchWhere");
        $total_rows = $countResult[0]->total;
        $total_pages = ceil($total_rows / $limit);
        
        // Get orders with pagination
        $orders = DB::select("SELECT * FROM orders WHERE $searchWhere ORDER BY idOrder DESC LIMIT $start, $limit");
        
        // Convert to pagination object for blade
        $orders = new \Illuminate\Pagination\LengthAwarePaginator(
            $orders,
            $total_rows,
            $limit,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('orders.index', compact('orders', 'companies', 'total_rows', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'CompanyName' => 'required',
            'TotModulesCaptured' => 'required|numeric',
            'Location' => 'required',
            'duedate' => 'required|date',
        ]);
        
        $rn = Auth::user()->username;
        
        DB::insert("INSERT INTO orders (CompanyName, idUser, TotModulesCaptured, OrderStatus, DateOrderCaptured, DateLastModification, Location, duedate) VALUES(?, ?, ?, 'Created', NOW(), NOW(), ?, ?)", [
            $request->CompanyName,
            $rn,
            $request->TotModulesCaptured,
            $request->Location,
            $request->duedate
        ]);
        
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), 'New Order Captured')", [$rn]);
        
        return redirect()->route('orders.index');
    }

    public function edit($idOrder)
    {
        $orderResult = DB::select("SELECT * FROM orders WHERE idOrder = ?", [$idOrder]);
        $order = !empty($orderResult) ? $orderResult[0] : abort(404);
        
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, $idOrder)
    {
        $request->validate([
            'TotModulesCaptured' => 'required|numeric',
            'TotModulesReceived' => 'required|numeric',
            'OrderStatusupd' => 'required',
        ]);
        
        $rn = Auth::user()->username;
        $OrderStatus = $request->OrderStatus;
        $OrderStatusupd = $request->OrderStatusupd;
        
        if ($OrderStatus == $OrderStatusupd) {
            DB::update("UPDATE orders SET TotModulesCaptured=?, TotModulesReceived=?, DateLastModification=NOW(), idUserLastUpdated=? WHERE idOrder=?", [
                $request->TotModulesCaptured,
                $request->TotModulesReceived,
                $rn,
                $idOrder
            ]);
        } else {
            DB::update("UPDATE orders SET TotModulesCaptured=?, TotModulesReceived=?, OrderStatus=?, DateLastModification=NOW(), idUserLastUpdated=? WHERE idOrder=?", [
                $request->TotModulesCaptured,
                $request->TotModulesReceived,
                $OrderStatusupd,
                $rn,
                $idOrder
            ]);
        }
        
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [$rn, "Order $idOrder Edited"]);
        
        return redirect()->route('orders.index');
    }

    public function destroy($idOrder)
    {
        $rn = Auth::user()->username;
        
        DB::update("UPDATE orders SET OrderStatus = 'Deleted' WHERE idOrder = ?", [$idOrder]);
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [$rn, "Order deleted: $idOrder"]);
        
        return redirect()->route('orders.index');
    }

    public function doneOrders()
    {
        $orders = DB::select("SELECT * FROM orders WHERE OrderStatus IN ('Deleted','Completed','Delayed')");
        $row_cnt = count($orders);
        $companies = DB::select("SELECT CompanyName FROM company ORDER BY CompanyName ASC");
        
        return view('orders.done', compact('orders', 'row_cnt', 'companies'));
    }
}
