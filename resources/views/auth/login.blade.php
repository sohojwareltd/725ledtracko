<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRACKO · Sign in</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="login-page">
    <div class="login-bg-orb"></div>
    <div class="login-bg-orb"></div>
    
    <div class="login-card">
        <div class="login-logo-wrapper">
            <img src="{{ asset('img/725led_repair_png3.png') }}" alt="725co LED Repair" class="login-logo">
        </div>
        <span class="brand-pill">TRACKO</span>

        <div>
            <h1>Sign in</h1>
            <p>Access the LED repair operations console.</p>
        </div>

        <form action="{{ route('login.store') }}" method="post" class="section-stack">
            @csrf
            
            <div>
                <label class="muted" for="username">User name</label>
                <input class="form-control @error('username') error @enderror" 
                       type="text" id="username" name="username" 
                      placeholder="Enter user name" 
                       value="{{ old('username') }}" 
                       required autofocus>
            </div>

            <div>
                <label class="muted" for="password">Password</label>
                <input class="form-control @error('password') error @enderror" 
                       type="password" id="password" name="password" 
                       placeholder="Enter password" 
                       required>
                <div style="min-height: 56px; display: flex; align-items: flex-start; margin-top: 8px;">
                    @if ($errors->any())
                    <div style="padding: 10px 12px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius-sm); color: var(--danger); font-size: 0.85rem; display: flex; align-items: center; gap: 8px; width: 100%;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>
                            @foreach ($errors->all() as $error)
                                {{ $error }}@if (!$loop->last)<br>@endif
                            @endforeach
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <button class="btn btn-primary w-100" type="submit" style="margin-top: -8px;">
                <i class="bi bi-box-arrow-in-right"></i>
                Continue
            </button>
        </form>
    </div>
</body>
</html>
