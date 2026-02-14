@extends('layouts.app')

@section('title', 'Inactive Orders')

@section('content')
<script>
function aviso(url){
    if (!confirm("WARNING!! Are you sure you want to delete it? Click OK to confirm, Cancel to abort.")) {
        return false;
    } else {
        document.location = url;
        return true;
    }
}
</script>

<header class="page-header">
    <div>
        <p class="page-header__subtitle">Archived and completed orders</p>
        <h1 class="page-header__title">Inactive Orders</h1>
    </div>
    <a href="{{ route('orders.index') }}" class="btn btn-primary">
        <i class="bi bi-arrow-left"></i> View Active Orders
    </a>
</header>

<section class="section-stack">
    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Order archive</p>
                <h2 class="section-title">Inactive Orders ({{ $row_cnt }})</h2>
            </div>
        </div>
        <div class="table-scroll">
            <table class="data-table data-table--gradient">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Company</th>
                        <th>Date created</th>
                        <th>Qty captured</th>
                        <th>Date received</th>
                        <th>Qty received</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $row)
                    <tr>
                        <td><strong>#{{ $row->idOrder }}</strong></td>
                        <td>{{ $row->CompanyName }}</td>
                        <td>{{ $row->DateOrderCaptured ? date('M d, Y', strtotime($row->DateOrderCaptured)) : '-' }}</td>
                        <td>{{ $row->TotModulesCaptured ?? '0' }}</td>
                        <td>{{ $row->DateOrderReceived ? date('M d, Y', strtotime($row->DateOrderReceived)) : '-' }}</td>
                        <td>{{ $row->TotModulesReceived ?? '0' }}</td>
                        <td><span class="badge badge-neutral">{{ $row->Location ?? '' }}</span></td>
                        <td><span class="status-text {{ \App\Helpers\StatusHelper::statusClass($row->OrderStatus) }}">{{ strtoupper($row->OrderStatus) }}</span></td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('orders.edit', $row->idOrder) }}" class="btn btn-ghost">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="javascript:;" onclick="aviso('{{ route('orders.delete', $row->idOrder) }}');" class="btn btn-danger">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
