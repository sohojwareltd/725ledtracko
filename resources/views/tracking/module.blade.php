@extends('layouts.app')

@section('title', 'Module History')

@section('content')
<header class="page-header">
    <div>
        <p class="page-header__subtitle">Module timeline and repair history</p>
        <h1 class="page-header__title">Module history</h1>
    </div>
    <span class="pill"><i class="bi bi-upc-scan"></i> Barcode: {{ $Barcode }}</span>
</header>

<section class="surface">
    @if(count($modules) > 0)
    <div class="table-scroll">
        <table class="data-table data-table--gradient">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Module</th>
                    <th>Date received</th>
                    <th>Date repaired</th>
                    <th>Date QC</th>
                    <th>Damage</th>
                    <th>Repair area</th>
                    <th>Repairer</th>
                    <th>QC agent</th>
                </tr>
            </thead>
            <tbody>
                @foreach($modules as $row)
                <tr>
                    <td>#{{ $row->idOrder }}</td>
                    <td>{{ $row->ModuleModel ?? '-' }}</td>
                    <td>{{ $row->DateReceived ? date('M d, Y', strtotime($row->DateReceived)) : '-' }}</td>
                    <td>{{ $row->DateRepair ? date('M d, Y', strtotime($row->DateRepair)) : '-' }}</td>
                    <td>{{ $row->DateQC ? date('M d, Y', strtotime($row->DateQC)) : '-' }}</td>
                    <td>{{ $row->Damage ?? '-' }}</td>
                    <td>{{ $row->RepairArea ?? '-' }}</td>
                    <td>{{ $row->repairer ?? '-' }}</td>
                    <td>{{ $row->QCAgent ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align: center; padding: calc(var(--spacing-unit) * 4); color: var(--text-subtle);">
        <i class="bi bi-inbox" style="font-size: 3rem; margin-bottom: calc(var(--spacing-unit) * 2); opacity: 0.5;"></i>
        <p style="font-size: 1.1rem; margin: 0;">No modules found for this barcode.</p>
        <p style="margin-top: calc(var(--spacing-unit) * 1);">Please check the barcode and try again.</p>
    </div>
    @endif
</section>
@endsection
