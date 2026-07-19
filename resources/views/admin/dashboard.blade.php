@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/admin-panel.css') }}?v={{ @filemtime(public_path('assets/css/admin-panel.css')) }}" rel="stylesheet">
@endpush

@section('content')

@php
    // Read-only, derived-from-existing-data stats — none of this touches the
    // controller; it mirrors the same style of query DashboardController@index
    // already runs, just for the extra tiles/charts this redesign adds.
    $inactive_libraries = \App\Models\Library::whereIn('status', ['suspended', 'expired'])->count();
    $today_libraries = \App\Models\Library::whereDate('created_at', today())->count();

    $growthMonths = collect(range(5, 0))->map(function ($i) {
        $date = now()->subMonths($i);
        return [
            'label' => $date->format('M'),
            'count' => \App\Models\Library::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count(),
        ];
    });
@endphp

<!-- Welcome Hero -->
<div class="welcome-hero mb-4">
    <div class="row align-items-center g-3">
        <div class="col-lg-7">
            <h4>Welcome back, {{ explode(' ', auth()->user()->name)[0] }} 👋</h4>
            <p>Softlix Admin &middot; {{ now()->format('l, d M Y') }}</p>
        </div>
        <div class="col-lg-5">
            <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                <a href="/admin/libraries" class="quick-action" style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.1);color:#fff">
                    <span class="qa-icon" style="background:rgba(102,126,234,.25);color:#a5b4fc"><i class="bi bi-building"></i></span>
                    <span><span class="qa-label d-block" style="color:#fff">Libraries</span></span>
                </a>
                <a href="/admin/plans" class="quick-action" style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.1);color:#fff">
                    <span class="qa-icon" style="background:rgba(251,146,60,.2);color:#fb923c"><i class="bi bi-star-fill"></i></span>
                    <span><span class="qa-label d-block" style="color:#fff">Plans</span></span>
                </a>
                <a href="/admin/payments" class="quick-action" style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.1);color:#fff">
                    <span class="qa-icon" style="background:rgba(74,222,128,.2);color:#4ade80"><i class="bi bi-cash-coin"></i></span>
                    <span><span class="qa-label d-block" style="color:#fff">Payments</span></span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-2">
        <div class="summary-card">
            <div class="sc-icon" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-building"></i></div>
            <div><div class="sc-num">{{ $total_libraries }}</div><div class="sc-label">Total</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="summary-card">
            <div class="sc-icon" style="background:#dcfce7;color:#166534"><i class="bi bi-check-circle-fill"></i></div>
            <div><div class="sc-num">{{ $active_libraries }}</div><div class="sc-label">Active</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="summary-card">
            <div class="sc-icon" style="background:#fee2e2;color:#991b1b"><i class="bi bi-slash-circle-fill"></i></div>
            <div><div class="sc-num">{{ $inactive_libraries }}</div><div class="sc-label">Inactive</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="summary-card">
            <div class="sc-icon" style="background:#fef3c7;color:#92400e"><i class="bi bi-clock-fill"></i></div>
            <div><div class="sc-num">{{ $pending_libraries }}</div><div class="sc-label">Pending</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="summary-card">
            <div class="sc-icon" style="background:#ede9fe;color:#6d28d9"><i class="bi bi-calendar-plus-fill"></i></div>
            <div><div class="sc-num">{{ $today_libraries }}</div><div class="sc-label">Today</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="summary-card">
            <div class="sc-icon" style="background:#dcfce7;color:#166534"><i class="bi bi-currency-rupee"></i></div>
            <div><div class="sc-num">₹{{ number_format($total_revenue) }}</div><div class="sc-label">Revenue</div></div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="table-card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow me-2"></i>Library Growth (Last 6 Months)</h6>
            <div class="chart-wrap"><canvas id="growthChart"></canvas></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="table-card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill me-2"></i>Plan Distribution</h6>
            <div class="chart-wrap chart-wrap-donut"><canvas id="planChart"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Recent Libraries -->
    <div class="col-lg-8">
        <div class="table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-building me-2"></i>Recent Libraries</span>
                <a href="/admin/libraries" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 rtable">
                    <thead class="table-light">
                        <tr><th>Library</th><th>Plan</th><th>Status</th><th>Expires</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recent_libraries as $lib)
                        <tr>
                            <td data-label="Library">
                                <div class="entity-row">
                                    <span class="entity-avatar">{{ substr($lib->name, 0, 1) }}</span>
                                    <div class="min-w-0">
                                        <div class="fw-600 text-truncate">{{ $lib->name }}</div>
                                        <small class="text-muted text-truncate d-block">{{ $lib->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Plan"><span class="badge bg-light text-dark">{{ $lib->plan->name }}</span></td>
                            <td data-label="Status">
                                @if($lib->status == 'active') <span class="badge badge-active">Active</span>
                                @elseif($lib->status == 'pending') <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($lib->status == 'suspended') <span class="badge badge-inactive">Suspended</span>
                                @else <span class="badge badge-expired">Expired</span>
                                @endif
                            </td>
                            <td data-label="Expires">{{ $lib->plan_expires_at ? $lib->plan_expires_at->format('d M Y') : '-' }}</td>
                            <td data-label="Actions">
                                <div class="d-flex gap-1">
                                    @if($lib->status != 'active')
                                    <form method="POST" action="/admin/libraries/{{ $lib->id }}/approve">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    @else
                                    <form method="POST" action="/admin/libraries/{{ $lib->id }}/suspend">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger">Suspend</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5">
                            <div class="empty-state">
                                <div class="es-icon"><i class="bi bi-building"></i></div>
                                <h6>No libraries registered yet</h6>
                                <p>New signups will show up here as soon as an owner registers.</p>
                            </div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Plan Stats -->
    <div class="col-lg-4">
        <div class="table-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-star-fill me-2"></i>Plans at a Glance</h6>
            @forelse($plans as $plan)
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="entity-avatar" style="width:32px;height:32px;font-size:12px;border-radius:9px"><i class="bi bi-star-fill" style="font-size:12px"></i></span>
                    <div>
                        <div class="fw-600">{{ $plan->name }}</div>
                        <small class="text-muted">₹{{ number_format($plan->price) }}/mo</small>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold">{{ $plan->libraries_count }}</div>
                    <small class="text-muted">libraries</small>
                </div>
            </div>
            @empty
            <p class="text-muted small mb-0">No plans configured yet.</p>
            @endforelse
            <hr>
            <a href="/admin/plans" class="btn btn-outline-primary w-100 btn-sm">Manage Plans</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('growthChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($growthMonths->pluck('label')) !!},
        datasets: [{
            label: 'New Libraries',
            data: {!! json_encode($growthMonths->pluck('count')) !!},
            borderColor: '#667eea',
            backgroundColor: 'rgba(102,126,234,0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointBackgroundColor: '#667eea',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});

@if($plans->count() > 0)
new Chart(document.getElementById('planChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($plans->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($plans->pluck('libraries_count')) !!},
            backgroundColor: ['#667eea', '#764ba2', '#4ade80', '#fb923c', '#f472b6'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }
    }
});
@endif
</script>
@endpush
