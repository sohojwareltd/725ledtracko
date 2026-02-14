@extends('layouts.app')

@section('title', 'QC Rejected')

@section('content')
<header class="page-header">
    <div>
        <p class="page-header__subtitle">Modules that failed quality control</p>
        <h1 class="page-header__title">QC Rejected Today</h1>
    </div>
    <a href="{{ route('qc.index') }}" class="btn btn-primary">
        <i class="bi bi-arrow-left"></i> Back to QC
    </a>
</header>

<section class="section-stack">
    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Rejected modules summary</p>
                <h2 class="section-title">Qty. Modules Rejected by Repairer</h2>
            </div>
        </div>
        <div class="table-scroll">
            <table class="data-table data-table--gradient">
                <thead>
                    <tr>
                        <th>Repairer</th>
                        <th>Qty. Modules Rejected</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grouped = [];
                        foreach ($modules as $module) {
                            if (!isset($grouped[$module->repairer])) {
                                $grouped[$module->repairer] = 0;
                            }
                            $grouped[$module->repairer]++;
                        }
                    @endphp
                    @foreach($grouped as $repairer => $qty)
                    <tr>
                        <td>{{ $repairer ?? '-' }}</td>
                        <td>{{ $qty }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Rejected modules details</p>
                <h2 class="section-title">Details</h2>
            </div>
        </div>
        <div class="table-scroll">
            <table class="data-table data-table--gradient">
                <thead>
                    <tr>
                        <th>Repairer</th>
                        <th>Barcode</th>
                        <th>Order ID</th>
                        <th>QC Status</th>
                        <th>QC Rejected Area</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $row)
                    <tr>
                        <td>{{ $row->repairer ?? '-' }}</td>
                        <td>{{ $row->Barcode }}</td>
                        <td>#{{ $row->idOrder }}</td>
                        <td>{{ $row->QCStatus ?? '-' }}</td>
                        <td>{{ $row->QCRejectedArea ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
