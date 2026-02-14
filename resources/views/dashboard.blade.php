@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<header class="page-header">
    <div>
        <p class="page-header__subtitle">Live snapshot · auto update 10s (no reload)</p>
        <h1 class="page-header__title">Operations Dashboard</h1>
    </div>
    <span class="pill" id="last-update"><i class="bi bi-lightning-charge"></i> Initializing…</span>
</header>

<section class="section-stack">
    <div class="stat-grid" id="stats-grid">
        <div class="surface stat-card stat-card--primary">
            <div class="section-header">
                <h2 class="section-title">Technician Output</h2>
                <span class="muted">Today</span>
            </div>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr><th>Technician</th><th>Qty</th></tr>
                    </thead>
                    <tbody>
                        @foreach($techOutput as $row)
                        <tr>
                            <td>{{ $row->repairer }}</td>
                            <td>{{ $row->qty }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td class="status-text">Total</td>
                            <td><strong>{{ $totalRepairs }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="surface stat-card stat-card--success">
            <div class="section-header">
                <h2 class="section-title">Technician Orders</h2>
                <span class="muted">Module throughput</span>
            </div>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr><th>Technician</th><th>Order</th><th>Qty</th></tr>
                    </thead>
                    <tbody>
                        @foreach($techOrders as $row)
                        <tr>
                            <td>{{ $row->repairer }}</td>
                            <td>#{{ $row->idOrder }}</td>
                            <td>{{ $row->qty }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="status-text">Total</td>
                            <td><strong>{{ $totalRepairs }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="surface stat-card stat-card--warning">
            <div class="section-header">
                <h2 class="section-title">Quality Control</h2>
                <span class="muted">Modules checked</span>
            </div>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr><th>QC agent</th><th>Qty</th></tr>
                    </thead>
                    <tbody>
                        @foreach($qcStats as $row)
                        <tr>
                            <td>{{ $row->QCAgent }}</td>
                            <td>{{ $row->qty }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="surface stat-card stat-card--info">
            <div class="section-header">
                <h2 class="section-title">Notes</h2>
                <span class="muted">Latest update</span>
            </div>
            <div class="section-stack">
                @if($message)
                    <p>{{ $message->Message }}</p>
                    <p>{{ $message->Message2 }}</p>
                    <p>{{ $message->Message3 }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">In process</p>
                <h2 class="section-title">Orders progress</h2>
            </div>
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <div class="table-scroll" id="orders-progress">
            <table class="data-table data-table--gradient">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Company</th>
                        <th>Received</th>
                        <th>Repaired</th>
                        <th>Repair %</th>
                        <th>QC</th>
                        <th>QC %</th>
                        <th>Due date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ordersProgress as $row)
                    <tr>
                        <td>#{{ $row->idOrder }}</td>
                        <td>{{ $row->CompanyName }}</td>
                        <td>{{ $row->TotModulesReceived }}</td>
                        <td>{{ $row->repaired }}</td>
                        <td><span class="badge badge-neutral">{{ $row->Perprogress }}%</span></td>
                        <td>{{ $row->QC }}</td>
                        <td><span class="badge badge-neutral">{{ $row->PerprogressQC }}%</span></td>
                        <td>{{ $row->duedate ? date('M d, Y', strtotime($row->duedate)) : '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
(function(){
    const intervalMs = 10000;
    const statsEl = document.getElementById('stats-grid');
    const ordersEl = document.getElementById('orders-progress');
    const badgeEl = document.getElementById('last-update');

    function updateBadge(ts){ if(badgeEl){ badgeEl.innerHTML = '<i class="bi bi-lightning-charge"></i> Updated '+ts; } }

    function safeReplace(target, html){
        if(!target) return;
        target.innerHTML = html;
    }

    async function refresh(){
        try {
            const r = await fetch('{{ route("dashboard.refresh") }}?ts=' + Date.now(), { cache:'no-store' });
            if(!r.ok){ console.warn('Refresh HTTP status', r.status); return; }
            const data = await r.json();
            if(data.stats && statsEl){ safeReplace(statsEl, data.stats); }
            if(data.orders && ordersEl){ safeReplace(ordersEl, data.orders); }
            if(data.timestamp){ updateBadge(data.timestamp); }
        } catch(e){ console.warn('Dashboard refresh failed', e); }
    }

    setTimeout(refresh, 600);
    setInterval(refresh, intervalMs);
})();
</script>
@endsection
