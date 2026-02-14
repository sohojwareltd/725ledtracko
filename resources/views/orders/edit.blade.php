@extends('layouts.app')

@section('title', 'Update Order')

@section('content')
<header class="page-header">
    <div>
        <p class="page-header__subtitle">Modify order details and status</p>
        <h1 class="page-header__title">Update order</h1>
    </div>
    <span class="pill"><i class="bi bi-tag"></i> Order #{{ $order->idOrder }}</span>
</header>

<section class="surface">
    <form action="{{ route('orders.update', $order->idOrder) }}" method="POST" class="section-stack">
        @csrf
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 600; margin: 0 0 calc(var(--spacing-unit) * 3) 0; color: var(--text-main);">Order information</h2>
        </div>

        <div class="form-grid">
            <div>
                <label class="muted" for="idOrder">Order ID</label>
                <input type="text" class="form-control" id="idOrder" value="{{ $order->idOrder }}" disabled>
            </div>
            <div>
                <label class="muted" for="companyName">Company name</label>
                <input type="text" class="form-control" id="companyName" value="{{ $order->CompanyName }}" disabled>
            </div>
            <div>
                <label class="muted" for="dateCreated">Date created</label>
                <input type="text" class="form-control" id="dateCreated" value="{{ $order->DateOrderCaptured ? date('M d, Y', strtotime($order->DateOrderCaptured)) : '-' }}" disabled>
            </div>
            <div>
                <label class="muted" for="dateReceived">Date received</label>
                <input type="text" class="form-control" id="dateReceived" value="{{ $order->DateOrderReceived ? date('M d, Y', strtotime($order->DateOrderReceived)) : '-' }}" disabled>
            </div>
        </div>

        <div class="form-grid">
            <div>
                <label class="muted" for="totModulesCaptured">Qty. modules captured</label>
                <input type="number" class="form-control" id="totModulesCaptured" name="TotModulesCaptured" value="{{ $order->TotModulesCaptured }}" required>
            </div>
            <div>
                <label class="muted" for="totModulesReceived">Qty. modules received</label>
                <input type="number" class="form-control" id="totModulesReceived" name="TotModulesReceived" value="{{ $order->TotModulesReceived }}" required>
            </div>
            <div>
                <label class="muted" for="duedate">Due date</label>
                <input type="text" class="form-control" id="duedate" value="{{ $order->duedate ? date('M d, Y', strtotime($order->duedate)) : '-' }}" disabled>
            </div>
            <div>
                <label class="muted" for="orderStatus">Order status</label>
                <select id="orderStatus" name="OrderStatusupd" class="form-control" required>
                    <option value="{{ $order->OrderStatus }}" selected>Current -{{ $order->OrderStatus }}</option>
                    <option value="Created">Created</option>
                    <option value="Dropped off">Documented</option>
                    <option value="In Process">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
        </div>

        <input type="hidden" name="idOrder" value="{{ $order->idOrder }}">
        <input type="hidden" name="OrderStatus" value="{{ $order->OrderStatus }}">
        <input type="hidden" name="CompanyName" value="{{ $order->CompanyName }}">

        <div style="display: flex; gap: calc(var(--spacing-unit) * 2); margin-top: calc(var(--spacing-unit) * 2);">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i>
                Update order
            </button>
            <a href="{{ route('orders.index') }}" class="btn btn-ghost">
                <i class="bi bi-x-lg"></i>
                Cancel
            </a>
        </div>
    </form>
</section>
@endsection
