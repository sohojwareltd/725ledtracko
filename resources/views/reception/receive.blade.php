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

/* Toast Notification Styles */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}
.toast {
    background: white;
    border-radius: var(--radius-md);
    padding: calc(var(--spacing-unit) * 3);
    margin-bottom: calc(var(--spacing-unit) * 2);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: calc(var(--spacing-unit) * 2);
    min-width: 300px;
    animation: slideInRight 0.3s ease-out;
}
.toast.success { border-left: 4px solid #22c55e; }
.toast.error   { border-left: 4px solid #ef4444; }
.toast-icon { font-size: 1.5rem; flex-shrink: 0; }
.toast.success .toast-icon { color: #22c55e; }
.toast.error   .toast-icon { color: #ef4444; }
.toast-content { flex: 1; }
.toast-title { font-weight: 600; margin-bottom: 4px; }
.toast-message { font-size: 0.9rem; opacity: 0.8; }
.toast-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    opacity: 0.5;
    transition: opacity 0.2s;
}
.toast-close:hover { opacity: 1; }
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}
@keyframes slideOutRight {
    from { transform: translateX(0);    opacity: 1; }
    to   { transform: translateX(100%); opacity: 0; }
}
.toast.hiding { animation: slideOutRight 0.3s ease-out forwards; }

/* Barcode Error Styles */
.form-control.error {
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
}
.error-message {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    display: none;
}
.error-message.show { display: block; }
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
                    <div id="barcodeError" class="error-message">Barcode must be 1000 or higher</div>
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

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
<script>
let barcodeValid = true;

$('#here').on('input keyup', function(){
    const value = parseInt(this.value);
    const inputField = $(this);
    const errorMessage = $('#barcodeError');
    const submitButton = $('#subHere');

    // Barcode 0-999 invalid, 1000+ valid
    if (this.value && value >= 0 && value <= 999) {
        inputField.addClass('error');
        errorMessage.addClass('show');
        submitButton.prop('disabled', true);
        barcodeValid = false;
    } else {
        inputField.removeClass('error');
        errorMessage.removeClass('show');
        submitButton.prop('disabled', false);
        barcodeValid = true;
    }

    // Auto-submit when 7 digits AND valid
    if (this.value.length == 7 && barcodeValid) {
        $('#subHere').click();
    }
});

// Prevent form submission if barcode is invalid
$('form').on('submit', function(e) {
    const barcodeValue = parseInt($('#here').val());
    if ($('#here').val() && barcodeValue >= 0 && barcodeValue <= 999) {
        e.preventDefault();
        showToast('error', 'Invalid Barcode', 'Barcode must be 1000 or higher');
        return false;
    }
});

function showToast(type, title, message) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';
    toast.innerHTML = `
        <div class="toast-icon"><i class="bi ${icon}"></i></div>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="closeToast(this)"><i class="bi bi-x"></i></button>
    `;
    container.appendChild(toast);
    setTimeout(() => { closeToast(toast.querySelector('.toast-close')); }, 5000);
}

function closeToast(button) {
    const toast = button.closest('.toast');
    toast.classList.add('hiding');
    setTimeout(() => { toast.remove(); }, 300);
}

// Show toast based on Laravel session flash
@if(session('success'))
    showToast('success', 'Success!', 'Module added successfully.');
@endif
@if(session('error'))
    @php
        $errMap = [
            'duplicate' => 'This barcode is already registered for the order.',
            'required'  => 'Module model, barcode, and order are required.',
            'invalid'   => 'Barcode must contain digits only.',
            'failed'    => 'The insertion was not complete. Please try again.',
        ];
        $errMsg = $errMap[session('error')] ?? 'An error occurred. Please try again.';
    @endphp
    showToast('error', 'Error!', '{{ $errMsg }}');
@endif
</script>
@endsection
