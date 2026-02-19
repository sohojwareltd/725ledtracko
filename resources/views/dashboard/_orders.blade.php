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
