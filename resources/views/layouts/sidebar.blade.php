@php
    $currentRoute = Route::currentRouteName();
    $username = Auth::user()->username ?? '';
    $role = strtolower(trim((string) (Auth::user()->role ?? '')));
    $isAdmin = $role === 'admin';
    
    $navItems = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'speedometer2', 'match' => ['dashboard']],
        ['route' => 'orders.index', 'label' => 'Orders', 'icon' => 'basket2', 'match' => ['orders.index', 'orders.edit', 'orders.done']],
        ['route' => 'reception.index', 'label' => 'Reception', 'icon' => 'box-arrow-in-down', 'match' => ['reception.index', 'reception.receive', 'reception.details']],
        ['route' => 'repair.index', 'label' => 'Repair', 'icon' => 'tools', 'match' => ['repair.index']],
        ['route' => 'qc.index', 'label' => 'QC', 'icon' => 'check2-circle', 'match' => ['qc.index']],
        ['route' => 'tracking.index', 'label' => 'Tracking', 'icon' => 'geo-alt', 'match' => ['tracking.index', 'tracking.module', 'tracking.order']],
    ];
@endphp

<aside class="app-sidebar" id="appSidebar">
    <button class="sidebar-close" id="sidebarClose" aria-label="Close navigation">
        <i class="bi bi-x"></i>
    </button>
    <div class="brand-block">
        <div class="sidebar-logo-wrapper">
            <img src="{{ asset('img/725logo.png') }}" alt="725co Logo" class="sidebar-logo">
        </div>
        <span class="brand-pill">725Co.</span>
        <p class="brand-user">Welcome, {{ $username }}</p>
    </div>

    <nav class="app-nav">
        @foreach($navItems as $item)
            <a class="app-nav__link {{ in_array($currentRoute, $item['match']) ? 'is-active' : '' }}" 
               href="{{ route($item['route']) }}" 
               title="{{ $item['label'] }}">
                <i class="bi bi-{{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
        
        @if($isAdmin)
            <a class="app-nav__link {{ $currentRoute === 'admin.index' ? 'is-active' : '' }}" 
               href="{{ route('admin.index') }}"
               title="Administrator">
                <i class="bi bi-shield-check"></i>
                <span>Administrator</span>
            </a>
        @endif
    </nav>

    <div class="app-sidebar__footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-ghost w-100">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </button>
        </form>
    </div>
</aside>
<button class="sidebar-trigger" id="sidebarTrigger" aria-label="Toggle navigation">
    <i class="bi bi-list"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<script>
(function () {
    const sidebar = document.getElementById('appSidebar');
    const trigger = document.getElementById('sidebarTrigger');
    const overlay = document.getElementById('sidebarOverlay');
    const closeBtn = document.getElementById('sidebarClose');
    const navLinks = document.querySelectorAll('.app-nav__link');
    if (!sidebar || !trigger || !overlay || !closeBtn) return;

    const isMobile = () => window.innerWidth <= 1100;

    const closeSidebar = () => {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-active');
        document.body.classList.remove('sidebar-open');
        if (isMobile()) {
            sidebar.style.transform = 'translateX(-110%)';
        } else {
            sidebar.style.transform = '';
        }
    };

    const openSidebar = () => {
        sidebar.classList.add('is-open');
        overlay.classList.add('is-active');
        document.body.classList.add('sidebar-open');
        if (isMobile()) {
            sidebar.style.transform = 'translateX(0)';
        }
    };

    const syncSidebarState = () => {
        if (isMobile()) {
            closeSidebar();
        } else {
            sidebar.classList.remove('is-open');
            overlay.classList.remove('is-active');
            document.body.classList.remove('sidebar-open');
            sidebar.style.transform = '';
        }
    };

    trigger.addEventListener('click', () => {
        const isOpen = sidebar.classList.contains('is-open');
        if (isOpen) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    overlay.addEventListener('click', closeSidebar);
    closeBtn.addEventListener('click', closeSidebar);
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 1100) {
                closeSidebar();
            }
        });
    });

    window.addEventListener('resize', syncSidebarState);
    syncSidebarState();
})();
</script>
