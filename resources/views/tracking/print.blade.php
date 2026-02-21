<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #{{ $idOrder }}</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #000; color: #fff; font-weight: bold; }
        .no-print { margin-bottom: 20px; text-align: right; }
        .no-print button, .no-print a { padding: 10px 20px; margin-left: 10px; cursor: pointer; border: none; text-decoration: none; display: inline-block; }
        .no-print button { background: #fff; color: #000; border: 1px solid #000; }
        .no-print a { background: #000; color: #fff; }
        @media print { .no-print { display: none !important; } }
        h1 { margin: 0 0 20px 0; }
        h2 { margin: 20px 0 10px 0; font-size: 16px; }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Print</button>
        <a href="{{ route('tracking.order', ['idOrder' => $idOrder]) }}">Back</a>
    </div>

    <h1>Order #{{ $idOrder }}</h1>

    <table>
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
                <td>{{ $order->Location }}</td>
                <td>{{ $order->idUser }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Modules ({{ $numModules }})</h2>
    <table>
        <thead>
            <tr>
                <th>Barcode</th>
                <th>Model</th>
                <th>Damage</th>
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
                <td>{{ $module->Barcode }}</td>
                <td>{{ $module->ModuleModel }}</td>
                <td>{{ $module->Damage }}</td>
                <td>{{ $module->DateReceived ? date('M d, Y', strtotime($module->DateReceived)) : '-' }}</td>
                <td>{{ $module->DateRepair ? date('M d, Y', strtotime($module->DateRepair)) : '-' }}</td>
                <td>{{ $module->DateQC ? date('M d, Y', strtotime($module->DateQC)) : '-' }}</td>
                <td>{{ $module->repairer ?? '-' }}</td>
                <td>{{ $module->RepairArea ?? '-' }}</td>
                <td>{{ $module->QCStatus ? strtoupper($module->QCStatus) : '-' }}</td>
                <td>{{ $module->QCAgent }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
