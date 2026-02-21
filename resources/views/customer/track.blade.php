<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRACKO · Customer Order Tracking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body {
            background: radial-gradient(circle at top, rgba(34, 179, 193, 0.08), transparent 55%), var(--page-bg);
        }

        .tracking-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .tracking-card {
            width: min(500px, 100%);
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            padding: calc(var(--spacing-unit) * 4);
            text-align: center;
        }

        .tracking-logo {
            max-width: 110px;
            height: auto;
            margin: 0 auto calc(var(--spacing-unit) * 3);
        }

        .tracking-card h1 {
            margin: 0 0 calc(var(--spacing-unit) * 1);
            font-size: 1.8rem;
            color: var(--text-main);
        }

        .tracking-card p {
            margin: 0 0 calc(var(--spacing-unit) * 3);
            color: var(--text-subtle);
        }

        .tracking-form {
            display: flex;
            flex-direction: column;
            gap: calc(var(--spacing-unit) * 2);
        }

        .tracking-error {
            border-radius: var(--radius-md);
            padding: calc(var(--spacing-unit) * 2);
            background: rgba(231, 76, 60, 0.1);
            color: #c0392b;
            font-weight: 600;
            margin-bottom: calc(var(--spacing-unit) * 2);
            border: 1px solid rgba(192, 57, 43, 0.3);
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
        }

        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 999px;
            background: var(--surface-muted);
            color: var(--brand);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: calc(var(--spacing-unit) * 3);
        }

        /* Result page styles */
        .customer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px;
            background: linear-gradient(135deg, #22b3c1, #007f80);
            border-radius: var(--radius-lg);
            color: white;
            margin-bottom: calc(var(--spacing-unit) * 3);
            box-shadow: var(--shadow-sm);
        }

        .customer-header h1 { margin: 0; font-size: 1.5rem; }
        .customer-header p { margin: 0; opacity: 0.9; font-size: 0.9rem; }

        .customer-logo {
            width: 60px;
            height: auto;
            filter: brightness(0) invert(1);
        }

        .result-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .progress-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
            background: var(--surface-muted);
            color: var(--brand);
        }
    </style>
</head>
<body>

@if(isset($order))
{{-- ===== RESULT PAGE ===== --}}
<div style="min-height: 100vh; padding: 24px;">
    <div class="result-wrapper">

        <div class="customer-header">
            <div>
                <p>Welcome to the 725co TRACKO system</p>
                <h1>Order Details</h1>
            </div>
            <img src="{{ asset('img/725led_repair_png3.png') }}" alt="725co Logo" class="customer-logo">
        </div>

        <section class="section-stack">
            <div class="surface">
                <div class="section-header">
                    <div>
                        <p class="muted">Your order information</p>
                        <h2 class="section-title">Order Summary</h2>
                    </div>
                    <a href="{{ route('customer.track') }}" class="btn btn-ghost">
                        <i class="bi bi-arrow-left"></i> Back to Search
                    </a>
                </div>

                <div class="table-scroll">
                    <table class="data-table data-table--gradient">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Company</th>
                                <th>Modules received</th>
                                <th>Modules repaired</th>
                                <th>QC passed</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>#{{ $order->idOrder }}</strong></td>
                                <td>{{ $order->CompanyName }}</td>
                                <td>{{ $order->TotModulesReceived ?? 0 }}</td>
                                <td>{{ $repaired }}</td>
                                <td>{{ $qcPassed }}</td>
                                <td><span class="badge badge-neutral">{{ $order->Location ?? '-' }}</span></td>
                                <td>
                                    <span class="status-text {{ \App\Helpers\StatusHelper::statusClass($order->OrderStatus) }}">
                                        {{ strtoupper($order->OrderStatus) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="progress-badge">
                                        <i class="bi bi-graph-up-arrow" style="margin-right: 6px;"></i>
                                        {{ $progress }}%
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </div>
</div>

@else
{{-- ===== SEARCH FORM ===== --}}
<div class="tracking-container">
    <div class="tracking-card">
        <img src="{{ asset('img/725led_repair_png3.png') }}" alt="725co Logo" class="tracking-logo">

        <div class="welcome-badge">
            <i class="bi bi-shield-check"></i>
            Welcome to 725co TRACKO
        </div>

        <h1>Track Your Order</h1>
        <p>Access your repair progress. Enter your order number for full details.</p>

        @if(isset($error))
        <div class="tracking-error">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ $error }}
        </div>
        @endif

        <form action="{{ route('customer.track.post') }}" method="POST" class="tracking-form">
            @csrf
            <input
                type="text"
                name="idOrder"
                class="form-control"
                placeholder="Enter order number"
                onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"
                required
                autofocus>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Track Order
            </button>
        </form>
    </div>
</div>
@endif

</body>
</html>
