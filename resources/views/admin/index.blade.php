@extends('layouts.app')

@section('title', 'Administrator')

@section('content')
<header class="page-header">
    <div>
        <p class="page-header__subtitle">Manage system settings and messages</p>
        <h1 class="page-header__title">Administrator</h1>
    </div>
    <span class="pill"><i class="bi bi-shield-check"></i> Admin Panel</span>
</header>

<section class="section-stack">
    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">Module management</p>
                <h2 class="section-title">Set "Repaired" to all modules</h2>
            </div>
        </div>
        <form action="{{ route('admin.setRepaired') }}" method="POST" class="section-stack">
            @csrf
            <div class="form-grid">
                <div>
                    <label class="muted" for="idOrder">Order ID</label>
                    <input type="text" 
                           class="form-control" 
                           id="idOrder" 
                           name="idOrder" 
                           placeholder="Enter Order ID"
                           onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"
                           required>
                </div>
            </div>
            <div style="display: flex; gap: calc(var(--spacing-unit) * 2);">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    Set All Repaired
                </button>
            </div>
        </form>
    </div>

    <div class="surface">
        <div class="section-header">
            <div>
                <p class="muted">System messages</p>
                <h2 class="section-title">Input messages</h2>
            </div>
        </div>
        <form action="{{ route('admin.message') }}" method="POST" class="section-stack">
            @csrf
            <div class="form-grid">
                <div>
                    <label class="muted" for="Message">Message 1</label>
                    <input type="text" class="form-control" id="Message" name="Message" value="{{ $message->Message ?? '' }}">
                </div>
            </div>
            <div class="form-grid">
                <div>
                    <label class="muted" for="Message2">Message 2</label>
                    <input type="text" class="form-control" id="Message2" name="Message2" value="{{ $message->Message2 ?? '' }}">
                </div>
            </div>
            <div class="form-grid">
                <div>
                    <label class="muted" for="Message3">Message 3</label>
                    <input type="text" class="form-control" id="Message3" name="Message3" value="{{ $message->Message3 ?? '' }}">
                </div>
            </div>
            <div style="margin-top: calc(var(--spacing-unit) * 2);">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    Set Messages
                </button>
            </div>
        </form>
    </div>
</section>
@endsection
