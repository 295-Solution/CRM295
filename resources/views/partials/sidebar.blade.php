@php
    $workspaceActive = request()->routeIs('clients.*', 'quotations.*', 'reports.*');
@endphp

<aside class="app-sidebar">
    <div class="app-brand-row">
        <h2 class="app-brand"><span class="app-brand-text">CRM 295</span></h2>
        <button id="sidebar-toggle" class="app-sidebar-toggle" type="button" aria-label="Toggle sidebar" aria-expanded="true">&laquo;</button>
    </div>

    <nav class="app-nav">
        <a class="app-nav-link {{ request()->routeIs('dashboard.page') ? 'active' : '' }}" href="{{ route('dashboard.page') }}">Dashboard</a>

        <div class="app-nav-group {{ $workspaceActive ? 'active' : '' }}">
            <p class="app-nav-title">Workspace</p>
            <div class="app-subnav">
                    <a class="{{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">Clients</a>
                    <a class="{{ request()->routeIs('quotations.*') ? 'active' : '' }}" href="{{ route('quotations.index') }}">Quotations</a>
                    <a class="{{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">Reports</a>
            </div>
        </div>

        @can('manage-users')
            <a class="app-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">User</a>
        @endcan
    </nav>

    <div class="app-sidebar-footer">
        <div class="app-user-chip">{{ auth()->user()->name }}</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="app-logout-btn" type="submit">Logout</button>
        </form>
    </div>
</aside>

<script>
    (() => {
        const shell = document.querySelector('.app-shell');
        const toggleButton = document.getElementById('sidebar-toggle');

        if (!shell || !toggleButton) {
            return;
        }

        const desktopQuery = window.matchMedia('(min-width: 961px)');
        const storageKey = 'crm295_sidebar_collapsed';

        const applyState = (collapsed) => {
            shell.classList.toggle('sidebar-collapsed', collapsed);
            toggleButton.setAttribute('aria-expanded', (!collapsed).toString());
        };

        const syncByViewport = () => {
            if (desktopQuery.matches) {
                applyState(localStorage.getItem(storageKey) === '1');
            } else {
                applyState(false);
            }
        };

        syncByViewport();

        toggleButton.addEventListener('click', () => {
            const collapsed = !shell.classList.contains('sidebar-collapsed');
            applyState(collapsed);
            localStorage.setItem(storageKey, collapsed ? '1' : '0');
        });

        desktopQuery.addEventListener('change', syncByViewport);
    })();
</script>
