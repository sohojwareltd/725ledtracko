@extends('layouts.app')

@section('title', 'Order Reception')

@section('content')
<style>
.last-barcode-display {
    background: linear-gradient(135deg, #03c0c1 0%, #009c9d 100%);
    border-radius: var(--radius-lg);
    padding: calc(var(--spacing-unit) * 4);
    text-align: center;
    color: white;
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
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
        <p class="page-header__subtitle">Scan and register received modules</p>
        <h1 class="page-header__title">Order Reception</h1>
    </div>
    <a href="{{ route('reception.index') }}" class="btn btn-ghost"><i class="bi bi-arrow-left"></i> Back to Queue</a>
</header>

<section class="section-stack">
    @if($lastBarcode)
    <div class="last-barcode-display">
        <div class="barcode-icon">
            <i class="bi bi-upc-scan"></i>
        </div>
        <h3>Last Barcode Scanned</h3>
        <div class="barcode-value">{{ $lastBarcode->Barcode }}</div>
        <div class="barcode-meta">
            Order #: {{ $order->idOrder }} |
            Company Name: {{ $order->CompanyName }} |
            Model: {{ $lastBarcode->ModuleModel }} | 
            Time: {{ date('h:i:s A', strtotime($lastBarcode->DateReceived)) }}
        </div>
    </div>
    @endif

    <div class="surface">
        <div class="section-header">
            <h2 class="section-title">Order Information</h2>
        </div>
        <div class="form-grid">
            <div>
                <label class="muted">Order ID</label>
                <input type="text" class="form-control" value="#{{ $order->idOrder }}" disabled>
            </div>
            <div>
                <label class="muted">Company Name</label>
                <input type="text" class="form-control" value="{{ $order->CompanyName }}" disabled>
            </div>
            <div>
                <label class="muted">Date Created</label>
                <input type="text" class="form-control" value="{{ $order->DateOrderCaptured ? date('M d, Y', strtotime($order->DateOrderCaptured)) : '' }}" disabled>
            </div>
            <div>
                <label class="muted">Qty. Captured</label>
                <input type="text" class="form-control" value="{{ $order->TotModulesCaptured ?? '0' }}" disabled>
            </div>
            <div>
                <label class="muted">Qty. Received</label>
                <input type="text" class="form-control" value="{{ $countmod }}" disabled>
            </div>
        </div>
    </div>

    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Add modules</p>
                <h2 class="section-title">Scan Barcode</h2>
            </div>
        </div>
        <form action="{{ route('reception.addModule', $order->idOrder) }}" method="POST" class="section-stack">
            @csrf
            <div class="form-grid">
                <div>
                    <label class="muted">Module Model</label>
                    <select name="ModelModule" class="form-control" required>
                        <option value="" disabled selected hidden>Select a Model</option>
                        @if($topModel)
                            <option value="{{ $topModel }}" selected>Current - {{ $topModel }}</option>
                        @endif
                        @foreach($modules as $module)
                            <option value="{{ $module->ModuleName }}">{{ $module->ModuleName }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="muted">Barcode</label>
                    <input id="here" minlength="4" maxlength="7" placeholder="Scan barcode..." type="text" 
                           name="Barcode" tabindex="1" required autofocus class="form-control">
                    <input type="hidden" name="idOrder" value="{{ $order->idOrder }}">
                </div>
            </div>
            <button id="subHere" type="submit" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Module
            </button>
        </form>
        
        <form action="{{ route('reception.complete', $order->idOrder) }}" method="POST" style="margin-top: calc(var(--spacing-unit) * 2);">
            @csrf
            <input type="hidden" name="idOrder" value="{{ $order->idOrder }}">
            <input type="hidden" name="countmod" value="{{ $countmod }}">
            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-check-circle"></i> Complete Reception
            </button>
        </form>
    </div>

    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Received by model</p>
                <h2 class="section-title">Module Summary</h2>
            </div>
        </div>
        <div class="table-scroll">
            <table class="data-table data-table--gradient">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Module Model</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $detail)
                    <tr>
                        <td>#{{ $detail->idOrder }}</td>
                        <td>{{ $detail->ModuleModel }}</td>
                        <td><span class="badge badge-neutral">{{ $detail->countable }}</span></td>
                        <td>
                            <a href="{{ route('reception.details', $order->idOrder) }}" class="btn btn-primary">
                                <i class="bi bi-list-ul"></i> View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    @if(empty($details) || count($details) == 0)
                    <tr>
                        <td colspan="4" class="text-center">No modules received yet</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
<script>
$('#here').keyup(function(){
    if(this.value.length == 7){ 
        $('#subHere').click(); 
    }
});
</script>
@endsection
