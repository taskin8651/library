<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#667eea">
    <title>My Dashboard - {{ $library->name }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="LibraryCRM">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/student-dashboard.css') }}" rel="stylesheet">
</head>
<body>
    <div class="topbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <div class="brand-mark"><i class="bi bi-book-fill"></i></div>
            <span class="fw-600">{{ $library->name }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" id="installAppBtn" data-pwa-install-btn class="btn btn-sm btn-install d-none">
                <i class="bi bi-download"></i>
            </button>
            <span class="small opacity-75 d-none d-sm-inline">{{ auth()->user()->name }}</span>
            <form method="POST" action="/logout" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-logout"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
            </form>
        </div>
    </div>

    <a href="/student/scan" class="scan-fab">
        <i class="bi bi-qr-code-scan"></i>
        <span>Scan to Check In</span>
    </a>

    <div class="container py-4">

        @php
            $hour = now()->hour;
            $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
            $totalPlanDays = ($member->plan_start_date && $member->plan_end_date)
                ? max(1, $member->plan_start_date->diffInDays($member->plan_end_date))
                : 1;
            $progressPct = $member->plan_end_date ? max(0, min(100, ($days_left / $totalPlanDays) * 100)) : 0;
            $ringColor = $days_left > 7 ? '#22c55e' : ($days_left > 3 ? '#f59e0b' : '#ef4444');
            $ringSoft   = $days_left > 7 ? '#f0fdf4' : ($days_left > 3 ? '#fffbeb' : '#fef2f2');
            $circumference = 2 * M_PI * 56;
            $offset = $circumference - ($progressPct / 100) * $circumference;
        @endphp

        <p class="text-muted mb-3 reveal reveal-1">{{ $greeting }}, <strong>{{ explode(' ', auth()->user()->name)[0] }}</strong> 👋</p>

        <!-- Announcements -->
        @if($announcements->count() > 0)
        @php
            // Safe "markdown-lite" renderer: escapes first, then adds a small
            // fixed set of safe tags — never trusts raw user HTML.
            $mdLite = function ($text) {
                $safe = e($text);
                $safe = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $safe);
                $safe = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $safe);
                $safe = preg_replace('/(?:^|\n)- (.+)/', '<br>&bull; $1', $safe);
                return $safe;
            };
        @endphp
        <div class="mb-4 reveal reveal-1">
            @foreach($announcements as $note)
            @php
                $noteStyle = [
                    'info'    => ['bg' => '#eff6ff', 'border' => '#bfdbfe', 'icon' => 'bi-info-circle-fill', 'color' => '#1d4ed8'],
                    'success' => ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'icon' => 'bi-check-circle-fill', 'color' => '#166534'],
                    'warning' => ['bg' => '#fffbeb', 'border' => '#fde68a', 'icon' => 'bi-exclamation-triangle-fill', 'color' => '#92400e'],
                    'danger'  => ['bg' => '#fef2f2', 'border' => '#fecaca', 'icon' => 'bi-megaphone-fill', 'color' => '#991b1b'],
                ][$note->type];
            @endphp
            <div class="d-flex align-items-start gap-3 rounded-3 p-3 mb-2" style="background: {{ $noteStyle['bg'] }}; border: 1px solid {{ $noteStyle['border'] }};">
                <i class="bi {{ $noteStyle['icon'] }}" style="color: {{ $noteStyle['color'] }}; font-size: 18px; margin-top: 2px;"></i>
                <div>
                    <div class="fw-600 small" style="color: {{ $noteStyle['color'] }}">{{ $note->title }}</div>
                    <div class="text-muted small">{!! $mdLite($note->message) !!}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Member Hero Card -->
        <div class="card-stat hero-card mb-4 reveal reveal-1">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-ring">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        <div>
                            <h5 class="fw-bold mb-0">{{ auth()->user()->name }}</h5>
                            <small class="text-muted">{{ $library->name }} Member</small>
                        </div>
                    </div>

                    <div class="uid-chip mb-3" onclick="copyUid(this)" data-uid="{{ $member->uid }}" title="Click to copy">
                        <span>{{ $member->uid }}</span>
                        <i class="bi bi-clipboard"></i>
                    </div>

                    <div class="row g-2">
                        <div class="col-auto">
                            <span class="badge info-chip text-dark px-3 py-2">
                                <i class="bi bi-grid me-1"></i>Seat: {{ $member->seat?->seat_number ?? 'Not Assigned' }}
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="badge info-chip text-dark px-3 py-2">
                                <i class="bi bi-clock me-1"></i>{{ $member->shift?->name ?? 'No Shift' }}
                                @if($member->shift) ({{ $member->shift->start_time }} - {{ $member->shift->end_time }}) @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 text-center mt-4 mt-md-0">
                    <div class="ring-wrap">
                        <svg viewBox="0 0 132 132">
                            <circle class="ring-track" cx="66" cy="66" r="56"></circle>
                            <circle class="ring-progress" id="daysRing" cx="66" cy="66" r="56"
                                stroke="{{ $ringColor }}"
                                stroke-dasharray="{{ $circumference }}"
                                stroke-dashoffset="{{ $circumference }}"
                                data-offset="{{ $offset }}"></circle>
                        </svg>
                        <div class="ring-center">
                            <span class="num" style="color: {{ $ringColor }}">{{ $days_left }}</span>
                            <span class="lbl">days left</span>
                        </div>
                    </div>
                    <p class="text-muted small mt-2 mb-0">
                        Expires: {{ $member->plan_end_date?->format('d M Y') ?? '-' }}
                    </p>
                    @if($days_left <= 5)
                    <div class="alert alert-warning py-1 px-2 mt-2 small mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>Plan expiring soon! Contact library.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="card-stat text-center reveal reveal-2">
                    <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-calendar3"></i></div>
                    <div class="fw-bold fs-3 text-primary count-up" data-target="{{ $this_month }}">0</div>
                    <div class="text-muted small">Days this month</div>
                </div>
            </div>
            <div class="col-4">
                <div class="card-stat text-center reveal reveal-3">
                    <div class="stat-icon" style="background:{{ $today_in ? '#dcfce7' : '#f1f3f6' }};color:{{ $today_in ? '#166534' : '#6c757d' }}">
                        <i class="bi {{ $today_in ? 'bi-check-circle-fill' : 'bi-dash-circle' }}"></i>
                    </div>
                    @if($today_in)
                        <div class="badge-live"><span class="pulse-dot"></span>In Library</div>
                    @else
                        <div class="fw-bold fs-4 text-muted">—</div>
                    @endif
                    <div class="text-muted small mt-1">Today</div>
                </div>
            </div>
            <div class="col-4">
                <div class="card-stat text-center reveal reveal-4">
                    <div class="stat-icon" style="background:#dcfce7;color:#166534"><i class="bi bi-currency-rupee"></i></div>
                    <div class="fw-bold fs-3 text-success">₹<span class="count-up" data-target="{{ (int) ($last_payment?->amount ?? 0) }}">0</span></div>
                    <div class="text-muted small">Last payment</div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="card-stat reveal reveal-5">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Recent Attendance</h6>
            @if($recent_attendance->count() > 0)
            <div class="table-responsive table-card-body">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Duration</th></tr>
                    </thead>
                    <tbody>
                        @foreach($recent_attendance as $i => $a)
                        <tr class="reveal" style="animation-delay: {{ .45 + $i * .05 }}s">
                            <td>{{ $a->date->format('d M') }}</td>
                            <td>{{ $a->check_in?->format('h:i A') ?? '-' }}</td>
                            <td>{!! $a->check_out?->format('h:i A') ?? '<span class="badge-live"><span class="pulse-dot"></span>In Library</span>' !!}</td>
                            <td>{{ $a->duration() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted text-center py-3">No attendance records yet</p>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/student-dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/pwa-install.js') }}"></script>
</body>
</html>
