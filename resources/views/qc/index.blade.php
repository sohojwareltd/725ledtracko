@extends('layouts.app')

@section('title', 'Quality Control')

@section('content')
<script>
function aviso(url){
    if (!confirm("WARNING!! Are you sure you want to remove it?")) {
        return false;
    } else {
        document.location = url;
        return true;
    }
}
</script>

<style>
.last-barcode-display {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: var(--radius-lg);
    padding: calc(var(--spacing-unit) * 4);
    text-align: center;
    color: white;
    box-shadow: 0 20px 60px rgba(245, 158, 11, 0.3);
    margin-bottom: calc(var(--spacing-unit) * 3);
}
.last-barcode-display h3 {
    margin: 0 0 calc(var(--spacing-unit) * 2) 0;
    font-size: 1.2rem;
    font-weight: 500;
    opacity: 0.9;
}
.barcode-value {
    font-size: 3.5rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    font-family: 'Courier New', monospace;
}
.barcode-meta {
    margin-top: calc(var(--spacing-unit) * 2);
    font-size: 0.95rem;
    opacity: 0.85;
}
.barcode-icon {
    font-size: 2rem;
    margin-bottom: calc(var(--spacing-unit) * 1);
    opacity: 0.8;
}
</style>

<header class="page-header">
    <div>
        <p class="page-header__subtitle">Validate repairs and capture QC decisions</p>
        <h1 class="page-header__title">Quality control</h1>
    </div>
    <span class="pill">Modules today: {{ $row_cnt }}</span>
</header>

<section class="section-stack">
    @if($lastBarcode)
    <div class="last-barcode-display">
        <div class="barcode-icon">
            <i class="bi bi-upc-scan"></i>
        </div>
        <h3>Last Barcode QC Scanned</h3>
        <div class="barcode-value">{{ $lastBarcode->Barcode }}</div>
        <div class="barcode-meta">
            Order #: {{ $lastBarcode->idOrder }} |
            Model: {{ $lastBarcode->ModuleModel }} |
            Status: {{ strtoupper($lastBarcode->QCStatus) }} 
        </div>
    </div>
    @endif

    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Scan QC</p>
                <h2 class="section-title">Update module status</h2>
            </div>
        </div>
        <form action="{{ route('qc.store') }}" method="POST" class="form-grid">
            @csrf
            <input id="qcStatus" maxlength="17" placeholder="Scan QC Status" type="text" name="QCStatus" required class="form-control" autofocus>
            <input id="hereQC" maxlength="7" placeholder="Scan Barcode" type="text" name="Barcode" required class="form-control">
            <button id="subHereQC" type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>

    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Recent scans</p>
                <h2 class="section-title">Modules today</h2>
            </div>
        </div>
        <div class="table-scroll">
            <table class="data-table data-table--gradient">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Barcode</th>
                        <th>Model</th>
                        <th>QC status</th>
                        <th>Date QC</th>
                        <th>Repair area</th>
                        <th>Damage</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $module)
                    <tr>
                        <td>#{{ $module->idOrder }}</td>
                        <td>{{ $module->Barcode }}</td>                        <td>{{ $module->ModuleModel }}</td>
                        <td class="status-text {{ $module->QCStatus ? \App\Helpers\StatusHelper::statusClass($module->QCStatus) : '' }}">{{ $module->QCStatus ? strtoupper($module->QCStatus) : '' }}</td>
                        <td>{{ $module->DateQC ? date('M d, Y H:i', strtotime($module->DateQC)) : "-" }}</td>
                        <td>{{ $module->RepairArea ?? '-' }}</td>
                        <td>{{ $module->Damage ?? '-' }}</td>
                        <td>
                            <a href="javascript:;" onclick="aviso('{{ route('qc.remove', [$module->Barcode, $module->idOrder]) }}'); return false;" class="btn btn-danger">Remove</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
<script>
$('#qcStatus').keyup(function(){
    if(this.value.length == 17){
        $('#hereQC').focus();
    }
});

$('#hereQC').keyup(function(){
    if(this.value.length == 7){
        $('#subHereQC').click();
    }
});
</script>
@endsection
