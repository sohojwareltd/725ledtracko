@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<header class="page-header">
    <div>
        <p class="page-header__subtitle">Order details and statistics</p>
        <h1 class="page-header__title">Order #{{ $idOrder }}</h1>
    </div>
    <a href="{{ route('tracking.print', $idOrder) }}" class="btn btn-primary">
        <i class="bi bi-printer"></i> Print
    </a>
</header>

<section class="section-stack">
    <div class="surface">
        <div class="table-scroll">
            <table class="data-table data-table--gradient">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Date captured</th>
                        <th>Date received</th>
                        <th>Company</th>
                        <th>Qty captured</th>
                        <th>Qty received</th>
                        <th>Qty repaired</th>
                        <th>QC passed</th>
                        <th>QC rejected</th>
                        <th>Location</th>
                        <th>Created by</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#{{ $order->idOrder }}</td>
                        <td>{{ $order->DateOrderCaptured ? date('M d, Y', strtotime($order->DateOrderCaptured)) : '-' }}</td>
                        <td>{{ $order->DateOrderReceived ? date('M d, Y', strtotime($order->DateOrderReceived)) : '-' }}</td>
                        <td>{{ $order->CompanyName }}</td>
                        <td>{{ $order->TotModulesCaptured }}</td>
                        <td>{{ $order->TotModulesReceived }}</td>
                        <td>{{ $repaired }}</td>
                        <td>{{ $qcPassed }}</td>
                        <td>{{ $qcRejected }}</td>
                        <td><span class="badge badge-neutral">{{ $order->Location }}</span></td>
                        <td>{{ $order->idUser }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Module details</p>
                <h2 class="section-title">Modules ({{ $numRowsDyn }})</h2>
            </div>
        </div>
        <div class="table-scroll">
            <table class="data-table data-table--gradient">
                <thead>
                    <tr>
                        <th>Barcode</th>
                        <th>
                            <form method="post" action="{{ route('tracking.order') }}" style="display: inline;" id="filterForm">
                                @csrf
                                <input type="hidden" name="idOrder" value="{{ $idOrder }}">
                                <select name="Model" onchange="document.getElementById('filterForm').submit()" style="border: none; background: transparent; font-weight: bold; cursor: pointer;">
                                    <option value="ALL" {{ empty($Model) || $Model == 'ALL' ? 'selected' : '' }}>Model-ALL</option>
                                    @foreach($modelGroups as $modelGroup)
                                        <option value="{{ $modelGroup->ModuleModel }}" {{ $Model == $modelGroup->ModuleModel ? 'selected' : '' }}>{{ $modelGroup->ModuleModel }}</option>
                                    @endforeach
                                </select>
                            
                        </th>
                        <th colspan="2">
                            
                                <select name="Damage" onchange="document.getElementById('filterForm').submit()" style="border: none; background: transparent; font-weight: bold; cursor: pointer;">
                                    <option value="ALL" {{ empty($Damage) || $Damage == 'ALL' ? 'selected' : '' }}>Damage-ALL</option>
                                    @foreach($damageGroups as $damageGroup)
                                        @if($damageGroup->Damage)
                                            <option value="{{ $damageGroup->Damage }}" {{ $Damage == $damageGroup->Damage ? 'selected' : '' }}>{{ $damageGroup->Damage }}</option>
                                        @endif
                                    @endforeach
                                    <option value="NULL" {{ $Damage == 'NULL' ? 'selected' : '' }}>-empty-</option>
                                </select>
                            </form>
                        </th>
                        <th>Date received</th>
                        <th>Date repaired</th>
                        <th>Date QC</th>
                        <th>Engineer</th>
                        <th>Repair area</th>
                        <th>QC status</th>
                        <th>QC agent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $module)
                    <tr>
                        <th>{{ $module->Barcode }}</th>
                        <th>{{ $module->ModuleModel }}</th>
                        <th>{{ $module->Damage }}</th>
                        <th></th>
                        <th>{{ $module->DateReceived ? date('m-d-Y', strtotime($module->DateReceived)) : '' }}</th>
                        <th>{{ $module->DateRepair ? date('m-d-Y', strtotime($module->DateRepair)) : '' }}</th>
                        <th>{{ $module->DateQC ? date('m-d-Y', strtotime($module->DateQC)) : '' }}</th>
                        <th>{{ $module->repairer }}</th>
                        <th>{{ $module->RepairArea }}</th>
                        <th>{{ $module->QCStatus }}</th>
                        <th>{{ $module->QCAgent }}</th>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
