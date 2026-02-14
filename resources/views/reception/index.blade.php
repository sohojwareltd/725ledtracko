@extends('layouts.app')

@section('title', 'Reception')

@section('content')
<script>
function aviso(url){
    if (!confirm("ALERT!! You are about to delete this record. Click OK to continue.")) {
        return false;
    } else {
        document.location = url;
        return true;
    }
}
</script>

<header class="page-header">
    <div>
        <p class="page-header__subtitle">Convert captured orders into received inventory</p>
        <h1 class="page-header__title">Reception queue</h1>
    </div>
    <span class="pill">Pending: {{ $pending_orders }}</span>
</header>

<section class="surface">
    <div class="table-scroll">
        <table class="data-table data-table--gradient">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Company</th>
                    <th>Date created</th>
                    <th>Qty modules</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>To do</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if($pending_orders === 0)
                <tr>
                    <td colspan="8" class="text-center">No orders need reception right now.</td>
                </tr>
                @else
                    @foreach($orders as $row)
                    <tr>
                        <td>#{{ $row->idOrder }}</td>
                        <td>{{ $row->CompanyName }}</td>
                        <td>{{ $row->DateOrderCaptured ? date('M d, Y H:i', strtotime($row->DateOrderCaptured)) : "-" }}</td>
                        <td>{{ $row->TotModulesCaptured }}</td>
                        <td><span class="badge badge-neutral">{{ $row->Location ?? '' }}</span></td>
                        <td class="status-text {{ \App\Helpers\StatusHelper::statusClass($row->OrderStatus) }}">{{ strtoupper($row->OrderStatus) }}</td>
                        <td>
                            <a href="{{ route('reception.receive', $row->idOrder) }}" class="btn btn-primary">Receive</a>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('orders.edit', $row->idOrder) }}" class="btn btn-primary">Edit</a>
                                <a href="javascript:;" onclick="aviso('{{ route('orders.delete', $row->idOrder) }}'); return false;" class="btn btn-danger">Delete</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</section>
@endsection
