<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRACKO · @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')
</head>
<body>
<div class="app-shell">
    @include('layouts.sidebar')

    <main class="app-main">
        @if(session('error'))
            <div style="background: #fee; border: 1px solid #fcc; padding: 1rem; margin: 1rem; border-radius: 4px; color: #c33;">
                <strong>Error:</strong> {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div style="background: #efe; border: 1px solid #cfc; padding: 1rem; margin: 1rem; border-radius: 4px; color: #3c3;">
                <strong>Success:</strong> {{ session('success') }}
            </div>
        @endif
        @yield('content')
    </main>
</div>
@yield('scripts')
</body>
</html>
