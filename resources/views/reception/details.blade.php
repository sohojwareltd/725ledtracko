@extends('layouts.app')

@section('title', 'Reception Details')

@section('content')
<script>
function aviso(url){
    if (!confirm("WARNING!! Are you sure you want to delete it?")) {
        return false;
    } else {
        document.location = url;
        return true;
    }
}
</script>

<style>
.summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: calc(var(--spacing-unit) * 2); }
.summary-card { border-radius: var(--radius-lg); padding: calc(var(--spacing-unit) * 3); background: var(--surface-strong); box-shadow: var(--shadow-md); }
.summary-card__label { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: calc(var(--spacing-unit)); }
.summary-card__value { font-size: 1.75rem; font-weight: 600; color: var(--text-main); }
.summary-card__meta { font-size: 0.85rem; color: var(--text-muted); }
.meta-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: calc(var(--spacing-unit) * 2); margin-top: calc(var(--spacing-unit) * 3); }
.meta-item label { font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 4px; }
.meta-item span { font-weight: 600; color: var(--text-main); }
</style>

<header class="page-header">
    <div>
        <p class="page-header__subtitle">Review all scanned modules for this order</p>
        <h1 class="page-header__title">Reception details</h1>
    </div>
    <div class="top-actions">
        <a href="{{ route('reception.receive', $idOrder) }}" class="btn btn-ghost">
            <i class="bi bi-arrow-left"></i> Back to order
        </a>
    </div>
</header>

<section class="section-stack">
    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Order overview</p>
                <h2 class="section-title">Order #{{ $order->idOrder }}</h2>
            </div>
            <span class="pill">{{ $order->CompanyName }}</span>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <p class="summary-card__label">Captured modules</p>
                <p class="summary-card__value">{{ $order->TotModulesCaptured ?? 0 }}</p>
                <p class="summary-card__meta">Original request</p>
            </div>
            <div class="summary-card">
                <p class="summary-card__label">Received modules</p>
                <p class="summary-card__value">{{ $countmod }}</p>
                <p class="summary-card__meta">{{ $order->TotModulesReceived ?? 0 }} recorded in header</p>
            </div>
            <div class="summary-card">
                <p class="summary-card__label">Order status</p>
                <p class="summary-card__value">{{ strtoupper($order->OrderStatus) }}</p>
                <p class="summary-card__meta">{{ $order->DateOrderCaptured ? date('M d, Y', strtotime($order->DateOrderCaptured)) : '-' }}</p>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-item">
                <label>Location</label>
                <span>{{ $order->Location ?? '-' }}</span>
            </div>
            <div class="meta-item">
                <label>Date received</label>
                <span>{{ $order->DateOrderReceived ? date('M d, Y', strtotime($order->DateOrderReceived)) : '-' }}</span>
            </div>
            <div class="meta-item">
                <label>Due date</label>
                <span>{{ $order->duedate ? date('M d, Y', strtotime($order->duedate)) : '-' }}</span>
            </div>
        </div>
    </div>

    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Modules scanned</p>
                <h2 class="section-title">Module details</h2>
            </div>
            <span class="pill">{{ $countmod }} items</span>
        </div>

        <div class="table-scroll">
            <table class="data-table data-table--gradient">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Module model</th>
                        <th>Barcode</th>
                        <th>Date received</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @foreach($details as $detail)
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td>{{ $detail->ModuleModel ?? '' }}</td>
                        <td><code>{{ $detail->Barcode ?? '' }}</code></td>
                        <td>{{ $detail->DateReceived ? date('M d, Y H:i', strtotime($detail->DateReceived)) : '-' }}</td>
                        <td>
                            <button class="btn btn-danger" onclick="aviso('{{ route('reception.deleteModule', [$idOrder, $detail->Barcode]) }}');">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    @endforeach
                    @if(empty($details) || count($details) == 0)
                    <tr>
                        <td colspan="5" class="text-center">No modules received for this order yet.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
