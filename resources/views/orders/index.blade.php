@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<script>
function aviso(url){
    if (!confirm("WARNING!! Are you sure you want to delete it?, if so, please click on OK \n if not, please click on Cancel.")) {
        return false;
    } else {
        document.location = url;
        return true;
    }
}
</script>

<header class="page-header">
    <div>
        <p class="page-header__subtitle">Create and monitor repair orders</p>
        <h1 class="page-header__title">Orders workspace</h1>
    </div>
    <div class="top-actions">
        <span class="pill">Active orders: {{ $total_rows }}</span>
        <a class="btn btn-ghost" href="{{ route('orders.done') }}"><i class="bi bi-archive"></i> Inactive</a>
    </div>
</header>

<section class="section-stack">
    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">New entry</p>
                <h2 class="section-title">Capture an order</h2>
            </div>
        </div>
        <form class="section-stack" action="{{ route('orders.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div>
                    <label class="muted">Company</label>
                    <select name="CompanyName" class="form-control" required>
                        <option value="" disabled selected hidden>Select a Company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->CompanyName }}">{{ $company->CompanyName }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="muted">Qty modules</label>
                    <input type="text" class="form-control" name="TotModulesCaptured" placeholder="Quantity" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" required>
                </div>
                <div>
                    <label class="muted">Location</label>
                    <select name="Location" class="form-control" required>
                        <option value="" disabled selected hidden>Select a Location</option>
                        <option value="Atlanta">Atlanta</option>
                        <option value="Chicago">Chicago</option>
                        <option value="Florida">Florida</option>
                        <option value="Garden Grove">Garden Grove</option>
                        <option value="Kansas City">Kansas City</option>
                        <option value="Las Vegas">Las Vegas</option>
                        <option value="Nashville">Nashville</option>
                        <option value="New Jersey">New Jersey</option>
                        <option value="Texas">Texas</option>
                    </select>
                </div>
                <div>
                    <label class="muted">Due date</label>
                    <input type="date" id="duedate" name="duedate" class="form-control" required>
                </div>
            </div>
            <div class="top-actions">
                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add order</button>
            </div>
        </form>
    </div>

    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Currently active</p>
                <h2 class="section-title">Orders overview</h2>
            </div>
        </div>
        <form method="GET" action="{{ route('orders.index') }}" class="section-stack" style="margin-bottom: 20px;">
            <div style="display: flex; gap: 12px; align-items: flex-end;">
                <div style="flex: 1;">
                    <label class="muted">Search orders</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by Order ID, Company, or Location..." value="{{ $search }}">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                    @if(!empty($search))
                        <a href="{{ route('orders.index') }}" class="btn btn-ghost"><i class="bi bi-x"></i> Clear</a>
                    @endif
                </div>
            </div>
        </form>
        @if(!empty($search))
            <div class="muted" style="margin-bottom: 12px;">
                Found <strong>{{ $total_rows }}</strong> result(s) for "<strong>{{ $search }}</strong>"
            </div>
        @endif
        <div class="table-scroll">
            <table class="data-table data-table--gradient">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Company</th>
                        <th>Date created</th>
                        <th>Qty modules</th>
                        <th>Date received</th>
                        <th>Qty received</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Due date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $row)
                    <tr>
                        <td>#{{ $row->idOrder }}</td>
                        <td>{{ $row->CompanyName }}</td>
                        <td>{{ $row->DateOrderCaptured ? date('M d, Y', strtotime($row->DateOrderCaptured)) : '' }}</td>
                        <td>{{ $row->TotModulesCaptured ?? '0' }}</td>
                        <td>{{ $row->DateOrderReceived ? date('M d, Y', strtotime($row->DateOrderReceived)) : '' }}</td>
                        <td>{{ $row->TotModulesReceived ?? '0' }}</td>
                        <td><span class="badge badge-neutral">{{ $row->Location ?? '' }}</span></td>
                        <td class="{{ $row->OrderStatus ? \App\Helpers\StatusHelper::statusClass($row->OrderStatus) : '' }} status-text">{{ $row->OrderStatus ? strtoupper($row->OrderStatus) : '' }}</td>
                        <td>{{ $row->duedate ? date('M d, Y', strtotime($row->duedate)) : '' }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('orders.edit', $row->idOrder) }}" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                                <a href="javascript:;" onclick="aviso('{{ route('orders.delete', $row->idOrder) }}'); return false;" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-box">
            <div class="muted">
                Showing <strong>{{ $orders->firstItem() ?? 0 }}</strong> to <strong>{{ $orders->lastItem() ?? 0 }}</strong> of <strong>{{ $total_rows }}</strong> results
            </div>
            <div class="pagination-controls">
                @if($orders->currentPage() > 1)
                    <a class="pg-btn" href="{{ $orders->previousPageUrl() . (!empty($search) ? '&search=' . urlencode($search) : '') }}">Previous</a>
                @else
                    <button class="pg-btn" disabled>Previous</button>
                @endif

                <span class="pg-count">{{ $orders->currentPage() }} / {{ $orders->lastPage() }}</span>

                @if($orders->hasMorePages())
                    <a class="pg-btn" href="{{ $orders->nextPageUrl() . (!empty($search) ? '&search=' . urlencode($search) : '') }}">Next</a>
                @else
                    <button class="pg-btn" disabled>Next</button>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
