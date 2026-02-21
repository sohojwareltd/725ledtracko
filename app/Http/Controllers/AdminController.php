<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = strtolower(trim((string) ($user->role ?? '')));
        
        if ($role !== 'admin') {
            return redirect('/')->with('error', "Access denied. Administrator access only.");
        }
        
        $messageResult = DB::select("SELECT * FROM messages ORDER BY Id_Message DESC LIMIT 1");
        $message = !empty($messageResult) ? $messageResult[0] : null;
        
        return view('admin.index', compact('message'));
    }

    public function updateMessage(Request $request)
    {
        $role = strtolower(trim((string) (Auth::user()->role ?? '')));
        
        if ($role !== 'admin') {
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
        $role = strtolower(trim((string) (Auth::user()->role ?? '')));
        
        if ($role !== 'admin') {
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

    // ---------------------------------------------------------------
    // Companies & Modules Management
    // ---------------------------------------------------------------

    public function companies()
    {
        $role = strtolower(trim((string) (Auth::user()->role ?? '')));
        if ($role !== 'admin') {
            return redirect('/')->with('error', 'Access denied.');
        }

        // All companies with their linked modules
        $companies = DB::select("SELECT * FROM company ORDER BY CompanyName ASC");

        $companyModules = [];
        foreach ($companies as $company) {
            $companyModules[$company->idCompany] = DB::select(
                "SELECT m.idModule, m.ModuleName
                 FROM modules m
                 INNER JOIN companymodules cm ON cm.idModules = m.idModule
                 WHERE cm.idCompany = ?
                 ORDER BY m.ModuleName ASC",
                [$company->idCompany]
            );
        }

        return view('admin.companies', compact('companies', 'companyModules'));
    }

    public function storeCompany(Request $request)
    {
        $role = strtolower(trim((string) (Auth::user()->role ?? '')));
        if ($role !== 'admin') {
            return redirect('/')->with('error', 'Access denied.');
        }

        $name = trim($request->input('CompanyName', ''));
        if (empty($name)) {
            return redirect()->route('admin.companies')->with('error', 'Company name is required.');
        }

        // Check duplicate
        $exists = DB::select("SELECT 1 FROM company WHERE CompanyName = ? LIMIT 1", [$name]);
        if (!empty($exists)) {
            return redirect()->route('admin.companies')->with('error', "Company \"$name\" already exists.");
        }

        DB::insert("INSERT INTO company (CompanyName) VALUES (?)", [$name]);
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [Auth::user()->username, "Created company: $name"]);

        return redirect()->route('admin.companies')->with('success', "Company \"$name\" created.");
    }

    public function deleteCompany(Request $request, $id)
    {
        $role = strtolower(trim((string) (Auth::user()->role ?? '')));
        if ($role !== 'admin') {
            return redirect('/')->with('error', 'Access denied.');
        }

        // Delete linked modules first (from companymodules, then orphaned modules)
        $linkedModules = DB::select("SELECT idModules FROM companymodules WHERE idCompany = ?", [$id]);
        DB::delete("DELETE FROM companymodules WHERE idCompany = ?", [$id]);

        // Delete modules that are no longer linked to any company
        foreach ($linkedModules as $lm) {
            $stillLinked = DB::select("SELECT 1 FROM companymodules WHERE idModules = ? LIMIT 1", [$lm->idModules]);
            if (empty($stillLinked)) {
                DB::delete("DELETE FROM modules WHERE idModule = ?", [$lm->idModules]);
            }
        }

        DB::delete("DELETE FROM company WHERE idCompany = ?", [$id]);
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [Auth::user()->username, "Deleted company ID: $id"]);

        return redirect()->route('admin.companies')->with('success', 'Company deleted.');
    }

    public function storeCompanyModule(Request $request, $id)
    {
        $role = strtolower(trim((string) (Auth::user()->role ?? '')));
        if ($role !== 'admin') {
            return redirect('/')->with('error', 'Access denied.');
        }

        $moduleName = trim($request->input('ModuleName', ''));
        if (empty($moduleName)) {
            return redirect()->route('admin.companies')->with('error', 'Module name is required.');
        }

        // Verify company exists
        $company = DB::select("SELECT * FROM company WHERE idCompany = ? LIMIT 1", [$id]);
        if (empty($company)) {
            return redirect()->route('admin.companies')->with('error', 'Company not found.');
        }

        // Check if this module name already exists (reuse or create new)
        $existing = DB::select("SELECT idModule FROM modules WHERE ModuleName = ? LIMIT 1", [$moduleName]);
        if (!empty($existing)) {
            $moduleId = $existing[0]->idModule;
        } else {
            DB::insert("INSERT INTO modules (ModuleName) VALUES (?)", [$moduleName]);
            $moduleId = DB::getPdo()->lastInsertId();
        }

        // Check if link already exists
        $linkExists = DB::select(
            "SELECT 1 FROM companymodules WHERE idCompany = ? AND idModules = ? LIMIT 1",
            [$id, $moduleId]
        );
        if (!empty($linkExists)) {
            return redirect()->route('admin.companies')->with('error', "Module \"$moduleName\" is already linked to this company.");
        }

        DB::insert("INSERT INTO companymodules (idModules, idCompany) VALUES (?, ?)", [$moduleId, $id]);
        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [Auth::user()->username, "Added module \"$moduleName\" to company ID $id"]);

        return redirect()->route('admin.companies')
            ->with('success', "Module \"$moduleName\" added.")
            ->with('open_company', $id);
    }

    public function deleteCompanyModule(Request $request, $companyId, $moduleId)
    {
        $role = strtolower(trim((string) (Auth::user()->role ?? '')));
        if ($role !== 'admin') {
            return redirect('/')->with('error', 'Access denied.');
        }

        // Remove the link
        DB::delete("DELETE FROM companymodules WHERE idCompany = ? AND idModules = ?", [$companyId, $moduleId]);

        // If this module is no longer linked to any company, remove the module record too
        $stillLinked = DB::select("SELECT 1 FROM companymodules WHERE idModules = ? LIMIT 1", [$moduleId]);
        if (empty($stillLinked)) {
            DB::delete("DELETE FROM modules WHERE idModule = ?", [$moduleId]);
        }

        DB::insert("INSERT INTO useraudit (User, Date, AuditDescription) VALUES(?, NOW(), ?)", [Auth::user()->username, "Removed module ID $moduleId from company ID $companyId"]);

        return redirect()->route('admin.companies')
            ->with('success', 'Module removed.')
            ->with('open_company', $companyId);
    }
}
