@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('content')

@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

    // Read-only supplementary data for the dashboard widgets (no business logic change).
    $upcomingRenewals = \App\Models\Member::with('user')
        ->where('library_id', $library->id)
        ->where('status', 'active')
        ->whereNotNull('plan_end_date')
        ->whereBetween('plan_end_date', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
        ->orderBy('plan_end_date')
        ->take(5)
        ->get();

    $recentAnnouncements = \App\Models\Announcement::where('library_id', $library->id)
        ->where('is_active', true)
        ->latest()
        ->take(3)
        ->get();

    $activityFeed = collect();
    foreach ($recent_payments as $pay) {
        $activityFeed->push([
            'time'  => $pay->created_at,
            'type'  => 'success',
            'icon'  => 'bi-cash-coin',
            'title' => ($pay->member->user->name ?? 'A member') . ' paid ₹' . number_format($pay->amount),
            'meta'  => strtoupper($pay->payment_mode) . ' &middot; ' . $pay->created_at->diffForHumans(),
        ]);
    }
    foreach ($active_sessions as $session) {
        $activityFeed->push([
            'time'  => $session->check_in,
            'type'  => 'info',
            'icon'  => 'bi-door-open-fill',
            'title' => ($session->member->user->name ?? 'A member') . ' checked in',
            'meta'  => 'Seat ' . ($session->seat->seat_number ?? '-') . ' &middot; ' . $session->check_in->diffForHumans(),
        ]);
    }
    $activityFeed = $activityFeed->sortByDesc('time')->take(6);

    // ---- Real trend data (read-only queries, no controller/model changes) ----
    $yesterdayRevenue = \App\Models\FeePayment::where('library_id', $library->id)
        ->whereDate('payment_date', today()->subDay())->sum('amount');
    $revenuePct = $yesterdayRevenue > 0
        ? round((($today_revenue - $yesterdayRevenue) / $yesterdayRevenue) * 100)
        : ($today_revenue > 0 ? 100 : 0);

    $newMembersThisWeek = \App\Models\Member::where('library_id', $library->id)
        ->where('created_at', '>=', now()->subDays(7))->count();
    $newMembersLastWeek = \App\Models\Member::where('library_id', $library->id)
        ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();
    $membersPct = $newMembersLastWeek > 0
        ? round((($newMembersThisWeek - $newMembersLastWeek) / $newMembersLastWeek) * 100)
        : ($newMembersThisWeek > 0 ? 100 : 0);

    $last7Revenue = collect(range(6, 0))->map(fn($d) => (float) \App\Models\FeePayment::where('library_id', $library->id)
        ->whereDate('payment_date', today()->subDays($d))->sum('amount'));
    $last7Attendance = collect(range(6, 0))->map(fn($d) => \App\Models\Attendance::where('library_id', $library->id)
        ->whereDate('date', today()->subDays($d))->count());

    $sparkline = function ($values, $width = 100, $height = 30) {
        $max = max($values->max(), 1);
        $min = min($values->min(), $max - 1);
        $range = max($max - $min, 1);
        $step = $width / max(count($values) - 1, 1);
        return $values->values()->map(function ($v, $i) use ($step, $height, $min, $range) {
            $x = round($i * $step, 1);
            $y = round($height - (($v - $min) / $range) * $height, 1);
            return "$x,$y";
        })->implode(' ');
    };
@endphp

@php
    // Same shared query the topbar bell uses — kept here too so the
    // alert is impossible to miss even before a staff member opens the dropdown.
    $overstayedToday = \App\Models\Attendance::overstayedToday($library->id);
@endphp

@if($overstayedToday->count())
<div class="overstay-banner mb-4">
    <div class="ob-head">
        <div class="ob-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <div class="flex-grow-1">
            <h6>{{ $overstayedToday->count() }} student{{ $overstayedToday->count() > 1 ? 's are' : ' is' }} still checked in past their shift time</h6>
            <p>These members haven't checked out even though their shift has ended. Please verify and check them out.</p>
        </div>
        <a href="/owner/attendance" class="btn btn-sm ob-btn">View Attendance <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    <div class="ob-list">
        @foreach($overstayedToday as $a)
        <div class="ob-item">
            <span class="ob-avatar">{{ substr($a->member->user->name ?? '?', 0, 1) }}</span>
            <div class="flex-grow-1 min-w-0">
                <div class="ob-name">{{ $a->member->user->name ?? 'Member' }}</div>
                <div class="ob-meta">Seat {{ $a->seat?->seat_number ?? '—' }} &middot; {{ $a->member->shift->name }} ended {{ \Carbon\Carbon::parse($a->member->shift->end_time)->format('h:i A') }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Welcome Hero -->
<div class="welcome-hero {{ $library->banner ? 'has-banner' : '' }}" @if($library->banner) style="--hero-banner-url: url('{{ asset('storage/'.$library->banner) }}')" @endif>
    <div class="row align-items-center g-3">
        <div class="col-lg-7">
            <h4>{{ $greeting }}, {{ explode(' ', auth()->user()->name)[0] }} 👋</h4>
            <p>{{ $library->name }} &middot; {{ now()->format('l, d M Y') }}</p>
        </div>
        <div class="col-lg-5">
            <div class="whero-right d-flex gap-2 flex-wrap justify-content-lg-end">
                <a href="/owner/members/create" class="quick-action" style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.1);color:#fff">
                    <span class="qa-icon" style="background:rgba(102,126,234,.25);color:#a5b4fc"><i class="bi bi-person-plus-fill"></i></span>
                    <span>
                        <span class="qa-label d-block" style="color:#fff">Add Member</span>
                    </span>
                </a>
                <a href="/owner/fees/collect" class="quick-action" style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.1);color:#fff">
                    <span class="qa-icon" style="background:rgba(74,222,128,.2);color:#4ade80"><i class="bi bi-cash-coin"></i></span>
                    <span>
                        <span class="qa-label d-block" style="color:#fff">Collect Fee</span>
                    </span>
                </a>
                <a href="/owner/attendance/qr" class="quick-action" style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.1);color:#fff">
                    <span class="qa-icon" style="background:rgba(251,146,60,.2);color:#fb923c"><i class="bi bi-qr-code-scan"></i></span>
                    <span>
                        <span class="qa-label d-block" style="color:#fff">QR Code</span>
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card stat-card-glow">
            <div class="d-flex justify-content-between align-items-start">
                <div class="icon-box" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-people-fill"></i></div>
                @if($newMembersThisWeek > 0 || $membersPct != 0)
                <span class="trend-badge {{ $membersPct >= 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="bi bi-arrow-{{ $membersPct >= 0 ? 'up' : 'down' }}-short"></i>{{ abs($membersPct) }}%
                </span>
                @endif
            </div>
            <h3>{{ $active_members }}</h3>
            <p>Active Members <span class="text-muted">({{ $total_members }} total)</span></p>
            <div class="stat-trend-note">{{ $newMembersThisWeek }} new this week</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card stat-card-glow">
            <div class="d-flex justify-content-between align-items-start">
                <div class="icon-box" style="background:#dcfce7;color:#166534"><i class="bi bi-currency-rupee"></i></div>
                <span class="trend-badge {{ $revenuePct >= 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="bi bi-arrow-{{ $revenuePct >= 0 ? 'up' : 'down' }}-short"></i>{{ abs($revenuePct) }}%
                </span>
            </div>
            <h3>₹{{ number_format($today_revenue) }}</h3>
            <p>Today's Revenue</p>
            <svg class="stat-sparkline" viewBox="0 0 100 30" preserveAspectRatio="none">
                <polyline points="{{ $sparkline($last7Revenue) }}" fill="none" stroke="#22c55e" stroke-width="2"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card stat-card-glow">
            <div class="icon-box" style="background:#fef3c7;color:#92400e"><i class="bi bi-person-check-fill"></i></div>
            <h3>{{ $currently_in }}</h3>
            <p>Currently In Library</p>
            <svg class="stat-sparkline" viewBox="0 0 100 30" preserveAspectRatio="none">
                <polyline points="{{ $sparkline($last7Attendance) }}" fill="none" stroke="#f59e0b" stroke-width="2"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card stat-card-glow">
            <div class="icon-box" style="background:#fce7f3;color:#9d174d"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <h3>{{ $expiring_soon }}</h3>
            <p>Expiring This Week</p>
            <div class="stat-trend-note">Renew before they lapse</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Monthly Revenue -->
    <div class="col-lg-4">
        <div class="stat-card text-center py-4 mb-3">
            <p class="text-muted small mb-1">This Month's Collection</p>
            <h2 class="fw-bold text-success">₹{{ number_format($monthly_revenue) }}</h2>
            <p class="text-muted small">{{ now()->format('F Y') }}</p>
            <a href="/owner/fees" class="btn btn-sm btn-outline-success mt-2">View All Fees</a>
        </div>

        <!-- Upcoming Renewals -->
        <div class="table-card p-4">
            <div class="section-head mb-3">
                <h6><i class="bi bi-calendar-event me-2"></i>Upcoming Renewals</h6>
            </div>
            @forelse($upcomingRenewals as $m)
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <div class="fw-600 small">{{ $m->user->name }}</div>
                    <div class="text-muted" style="font-size:11.5px">{{ $m->plan_end_date->format('d M Y') }}</div>
                </div>
                <span class="badge {{ $m->daysLeft() <= 3 ? 'badge-inactive' : 'badge-expired' }}">{{ $m->daysLeft() }}d left</span>
            </div>
            @empty
            <p class="text-muted small mb-0">No renewals due in the next 7 days.</p>
            @endforelse
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Recent Payments -->
        <div class="table-card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cash me-2"></i>Recent Payments</span>
                <a href="/owner/fees/collect" class="btn btn-sm btn-primary">+ Collect Fee</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 rtable">
                    <thead class="table-light">
                        <tr>
                            <th>Member</th>
                            <th>Amount</th>
                            <th>Mode</th>
                            <th>Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_payments as $pay)
                        <tr>
                            <td data-label="Member">
                                <div class="fw-500">{{ $pay->member->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $pay->payment_date->format('d M Y') }}</small>
                            </td>
                            <td data-label="Amount" class="fw-600 text-success">₹{{ number_format($pay->amount) }}</td>
                            <td data-label="Mode"><span class="badge bg-light text-dark text-uppercase">{{ $pay->payment_mode }}</span></td>
                            <td data-label="Receipt"><a href="/owner/fees/{{ $pay->id }}/receipt" class="btn btn-xs btn-outline-primary btn-sm">View</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4">
                            <div class="empty-state py-4">
                                <div class="es-icon"><i class="bi bi-receipt"></i></div>
                                <p class="mb-0">No payments recorded yet</p>
                            </div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity Timeline -->
        <div class="table-card p-4">
            <div class="section-head mb-3">
                <h6><i class="bi bi-activity me-2"></i>Recent Activity</h6>
            </div>
            @if($activityFeed->count() > 0)
            <div class="timeline">
                @foreach($activityFeed as $item)
                <div class="timeline-item tl-{{ $item['type'] }}">
                    <div class="tl-title"><i class="bi {{ $item['icon'] }} me-1"></i>{{ $item['title'] }}</div>
                    <div class="tl-meta">{!! $item['meta'] !!}</div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-muted small mb-0">No recent activity yet.</p>
            @endif
        </div>
    </div>
</div>

<!-- Currently In Library -->
@if($active_sessions->count() > 0)
<div class="table-card mt-3">
    <div class="card-header"><span class="pulse-dot-live"></span>Currently In Library ({{ $currently_in }})</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 rtable">
            <thead class="table-light">
                <tr><th>Member</th><th>Seat</th><th>Check In</th><th>Duration</th></tr>
            </thead>
            <tbody>
                @foreach($active_sessions as $session)
                <tr>
                    <td data-label="Member" class="fw-500">{{ $session->member->user->name ?? '-' }}</td>
                    <td data-label="Seat"><span class="badge bg-primary">{{ $session->seat->seat_number ?? '-' }}</span></td>
                    <td data-label="Check In">{{ $session->check_in->format('h:i A') }}</td>
                    <td data-label="Duration">{{ $session->duration() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Recent Announcements -->
@if($recentAnnouncements->count() > 0)
<div class="table-card mt-3 p-4">
    <div class="section-head mb-3">
        <h6><i class="bi bi-megaphone-fill me-2"></i>Recent Announcements</h6>
        <a href="/owner/announcements" class="btn btn-sm btn-outline-secondary">Manage</a>
    </div>
    <div class="row g-2">
        @foreach($recentAnnouncements as $note)
        @php
            $typeColor = ['info'=>'primary','success'=>'success','warning'=>'warning','danger'=>'danger'][$note->type] ?? 'secondary';
        @endphp
        <div class="col-md-4">
            <div class="p-3 rounded-3" style="background:#f8f9fc;border:1px solid rgba(17,24,39,.05);height:100%">
                <span class="badge bg-{{ $typeColor }} text-uppercase mb-2">{{ $note->type }}</span>
                <div class="fw-600 small">{{ $note->title }}</div>
                <div class="text-muted" style="font-size:12px">{{ \Illuminate\Support\Str::limit($note->message, 70) }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
