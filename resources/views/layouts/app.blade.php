<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LibraryCRM') | {{ $library->name ?? 'LibraryCRM' }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: {{ $library->theme_color ?? '#0d6efd' }};
            --accent-1: #667eea;
            --accent-2: #764ba2;
            --accent-teal: #14b8a6;
            --ink: #12141a;
            --sidebar-width: 264px;
            --shadow-sm: 0 2px 10px rgba(17,24,39,.05);
            --shadow-md: 0 10px 30px rgba(17,24,39,.08);
            --shadow-lg: 0 20px 50px rgba(17,24,39,.14);
        }
    </style>
    <link href="{{ asset('assets/css/app-layout.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="page-fade-init">
    <!-- Toast stack (rendered from session flash messages) -->
    <div id="toast-stack">
        @if(session('success'))
            <div class="toast-item success" data-autohide>
                <i class="bi bi-check-circle-fill icon"></i>
                <div class="msg">{{ session('success') }}</div>
                <button class="close-t" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                <div class="bar"></div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast-item error" data-autohide>
                <i class="bi bi-exclamation-circle-fill icon"></i>
                <div class="msg">{{ session('error') }}</div>
                <button class="close-t" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                <div class="bar"></div>
            </div>
        @endif
        @if(session('warning'))
            <div class="toast-item warning" data-autohide>
                <i class="bi bi-exclamation-triangle-fill icon"></i>
                <div class="msg">{{ session('warning') }}</div>
                <button class="close-t" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                <div class="bar"></div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand d-flex align-items-center gap-3">
            @if(isset($library) && $library->logo)
                <img src="{{ $library->logo_url }}" alt="Logo">
            @else
                <div class="icon-box bg-primary text-white rounded-2 d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:linear-gradient(135deg, var(--accent-1), var(--accent-2)) !important;box-shadow:0 6px 16px rgba(102,126,234,.4);">
                    <i class="bi bi-book-fill"></i>
                </div>
            @endif
            <div>
                <h6>{{ $library->name ?? 'LibraryCRM' }}</h6>
                <small>{{ auth()->user()->role ?? '' }}</small>
            </div>
        </div>

        @yield('sidebar-menu')
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Trial Banner -->
        @if(isset($library) && $library->isOnTrial())
        <div class="trial-banner">
            <span><i class="bi bi-clock me-2"></i>Trial period: <strong>{{ $library->daysLeft() }} days left</strong></span>
            <a href="/owner/subscription/plans" class="btn btn-sm btn-light">Upgrade Now</a>
        </div>
        @endif

        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn-icon-soft d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5>@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small d-none d-md-block">{{ now()->format('d M Y') }}</span>
                <div class="dropdown">
                    <button class="user-chip border-0" data-bs-toggle="dropdown">
                        <span class="user-avatar-sm">{{ substr(auth()->user()->name ?? '?', 0, 1) }}</span>
                        <span class="small fw-600">{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down small text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius:14px;">
                        <li><a class="dropdown-item text-danger rounded-3 mx-1" style="width:auto" href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit()">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="bottom-nav d-md-none">
        <a href="/owner/dashboard" class="bn-item {{ request()->is('owner/dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i><span>Home</span>
        </a>
        <a href="/owner/members" class="bn-item {{ request()->is('owner/members*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i><span>Members</span>
        </a>
        <a href="/owner/fees/collect" class="bn-item bn-item-fab">
            <i class="bi bi-plus-lg"></i>
        </a>
        <a href="/owner/attendance/qr" class="bn-item {{ request()->is('owner/attendance*') ? 'active' : '' }}">
            <i class="bi bi-qr-code-scan"></i><span>QR</span>
        </a>
        <button type="button" class="bn-item" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="bi bi-list"></i><span>More</span>
        </button>
    </nav>

    <form id="logout-form" action="/logout" method="POST" class="d-none">@csrf</form>

    <!-- Shared confirmation modal (replaces native confirm() dialogs) -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold mb-0">Please confirm</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmModalBody">Are you sure you want to proceed?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmModalBtn">Yes, continue</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/app-layout.js') }}"></script>
    @stack('scripts')
</body>
</html>
