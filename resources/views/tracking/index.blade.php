@extends('layouts.app')

@section('title', 'Tracking System')

@section('content')
<header class="page-header">
    <div>
        <p class="page-header__subtitle">Look up modules or entire orders</p>
        <h1 class="page-header__title">Tracking System</h1>
    </div>
</header>

<section class="stat-grid">
    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Barcode history</p>
                <h2 class="section-title">Module timeline</h2>
            </div>
        </div>
        <form action="{{ route('tracking.module') }}" method="POST" class="section-stack">
            @csrf
            <input type="text" class="form-control" name="Barcode" placeholder="Scan barcode" maxlength="20" required>
            <button type="submit" class="btn btn-primary w-100">Go</button>
        </form>
    </div>

    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Order insights</p>
                <h2 class="section-title">Order details</h2>
            </div>
        </div>
        <form action="{{ route('tracking.order') }}" method="POST" class="section-stack">
            @csrf
            <input type="text" class="form-control" name="idOrder" placeholder="Enter Order ID" maxlength="10" required>
            <input type="hidden" name="Damage" value="">
            <input type="hidden" name="Model" value="">
            <button type="submit" class="btn btn-primary w-100">Go</button>
        </form>
    </div>
</section>
@endsection
