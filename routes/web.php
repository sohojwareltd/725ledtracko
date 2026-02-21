<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\QCController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerTrackingController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Customer Tracking (no login required)
Route::get('/track', [CustomerTrackingController::class, 'index'])->name('customer.track');
Route::post('/track', [CustomerTrackingController::class, 'track'])->name('customer.track.post');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.alt');
    Route::get('/dashboard/refresh', [DashboardController::class, 'refresh'])->name('dashboard.refresh');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{idOrder}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::post('/orders/{idOrder}/update', [OrderController::class, 'update'])->name('orders.update');
    Route::get('/orders/{idOrder}/delete', [OrderController::class, 'destroy'])->name('orders.delete');
    Route::get('/doneorders', [OrderController::class, 'doneOrders'])->name('orders.done');

    // Reception
    Route::get('/receive', [ReceptionController::class, 'index'])->name('reception.index');
    Route::get('/receive/{idOrder}', [ReceptionController::class, 'receive'])->name('reception.receive');
    Route::post('/receive/{idOrder}/add-module', [ReceptionController::class, 'addModule'])->name('reception.addModule');
    Route::post('/receive/{idOrder}/complete', [ReceptionController::class, 'complete'])->name('reception.complete');
    Route::get('/receive/{idOrder}/details', [ReceptionController::class, 'details'])->name('reception.details');
    Route::get('/receive/{idOrder}/delete-module/{Barcode}', [ReceptionController::class, 'deleteModule'])->name('reception.deleteModule');

    // Repair
    Route::get('/repair', [RepairController::class, 'index'])->name('repair.index');
    Route::post('/repair', [RepairController::class, 'store'])->name('repair.store');
    Route::get('/repair/remove/{Barcode}/{idOrder}', [RepairController::class, 'remove'])->name('repair.remove');

    // QC
    Route::get('/qc', [QCController::class, 'index'])->name('qc.index');
    Route::post('/qc', [QCController::class, 'store'])->name('qc.store');
    Route::get('/qc/remove/{Barcode}/{idOrder}', [QCController::class, 'remove'])->name('qc.remove');
    Route::get('/qc/rejected', [QCController::class, 'rejected'])->name('qc.rejected');

    // Tracking
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::post('/tracking/module', [TrackingController::class, 'trackModule'])->name('tracking.module');
    Route::match(['get', 'post'], '/tracking/order', [TrackingController::class, 'trackOrder'])->name('tracking.order');
    Route::get('/tracking/order/{idOrder}/print', [TrackingController::class, 'printOrder'])->name('tracking.print');

    // Admin
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/message', [AdminController::class, 'updateMessage'])->name('admin.message');
    Route::post('/admin/set-repaired', [AdminController::class, 'setRepaired'])->name('admin.setRepaired');

    // Admin — Companies & Modules
    Route::get('/admin/companies', [AdminController::class, 'companies'])->name('admin.companies');
    Route::post('/admin/companies', [AdminController::class, 'storeCompany'])->name('admin.companies.store');
    Route::post('/admin/companies/{id}/delete', [AdminController::class, 'deleteCompany'])->name('admin.companies.delete');
    Route::post('/admin/companies/{id}/add-module', [AdminController::class, 'storeCompanyModule'])->name('admin.companies.addModule');
    Route::post('/admin/companies/{companyId}/delete-module/{moduleId}', [AdminController::class, 'deleteCompanyModule'])->name('admin.companies.deleteModule');
});

// Remove everything below - old debug routes
// END OF ROUTES
