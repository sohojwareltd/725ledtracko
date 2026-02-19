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
