@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports & Export')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('content')

@php
    $last30Revenue = collect(range(29, 0))->filter(fn($d) => $d % 3 === 0)->values()->map(function ($d) use ($library) {
        $date = today()->subDays($d);
        return [
            'label'  => $date->format('d M'),
            'amount' => (float) \App\Models\FeePayment::where('library_id', $library->id)
                ->whereDate('payment_date', $date)->sum('amount'),
        ];
    });

    $modeBreakdown = \App\Models\FeePayment::where('library_id', $library->id)
        ->selectRaw('payment_mode, SUM(amount) as total')
        ->groupBy('payment_mode')
        ->pluck('total', 'payment_mode');
@endphp

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#dcfce7;color:#166534"><i class="bi bi-currency-rupee"></i></div>
            <h3>₹{{ number_format($summary['total_fees']) }}</h3>
            <p>Total Fees Collected</p>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-receipt"></i></div>
            <h3>{{ $summary['total_payments'] }}</h3>
            <p>Total Payments</p>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#ede9fe;color:#6d28d9"><i class="bi bi-people-fill"></i></div>
            <h3>{{ $summary['total_members'] }}</h3>
            <p>Total Members</p>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#fef3c7;color:#92400e"><i class="bi bi-calendar-check-fill"></i></div>
            <h3>{{ $summary['total_attendance'] }}</h3>
            <p>Attendance Records</p>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="table-card p-4 h-100">
            <div class="section-head mb-2">
                <h6><i class="bi bi-graph-up me-2"></i>Revenue Trend &mdash; Last 30 Days</h6>
            </div>
            <div class="chart-wrap">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="table-card p-4 h-100">
            <div class="section-head mb-2">
                <h6><i class="bi bi-pie-chart-fill me-2"></i>Payment Mode Split</h6>
            </div>
            @if($modeBreakdown->count() > 0)
            <div class="chart-wrap chart-wrap-donut">
                <canvas id="modeChart"></canvas>
            </div>
            @else
            <div class="empty-state py-4">
                <div class="es-icon"><i class="bi bi-pie-chart"></i></div>
                <p class="mb-0">No payments recorded yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="section-head">
    <div>
        <h6>Export Center</h6>
        <div class="sub">Download detailed reports as CSV for any date range</div>
    </div>
</div>

<div class="row g-3">
    <!-- Fee Report -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="table-card p-4 h-100 report-export-card">
            <div class="icon-box mb-3" style="background:#dcfce7;color:#166534"><i class="bi bi-cash-stack"></i></div>
            <h6 class="fw-bold">Fee Payments Report</h6>
            <p class="text-muted small">Export all fee payments with receipt no., member, amount &amp; mode.</p>
            <form method="GET" action="/owner/reports/fees/export" class="report-export-form">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-500">From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-500">To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm">
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-sm w-100 report-export-btn"><i class="bi bi-download me-2"></i>Download CSV</button>
            </form>
        </div>
    </div>

    <!-- Attendance Report -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="table-card p-4 h-100 report-export-card">
            <div class="icon-box mb-3" style="background:#fef3c7;color:#92400e"><i class="bi bi-calendar-check-fill"></i></div>
            <h6 class="fw-bold">Attendance Report</h6>
            <p class="text-muted small">Export check-in/check-out logs for any date range.</p>
            <form method="GET" action="/owner/reports/attendance/export" class="report-export-form">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-500">From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-500">To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm">
                    </div>
                </div>
                <button type="submit" class="btn btn-warning btn-sm w-100 text-dark report-export-btn"><i class="bi bi-download me-2"></i>Download CSV</button>
            </form>
        </div>
    </div>

    <!-- Members Report -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="table-card p-4 h-100 report-export-card">
            <div class="icon-box mb-3" style="background:#ede9fe;color:#6d28d9"><i class="bi bi-people-fill"></i></div>
            <h6 class="fw-bold">Members List</h6>
            <p class="text-muted small">Export the full member directory with seat, shift &amp; plan dates.</p>
            <a href="/owner/reports/members/export" class="btn btn-primary btn-sm w-100 report-export-btn"><i class="bi bi-download me-2"></i>Download CSV</a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('assets/css/owner-reports.css') }}?v={{ @filemtime(public_path('assets/css/owner-reports.css')) }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('revenueTrendChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($last30Revenue->pluck('label')) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($last30Revenue->pluck('amount')) !!},
            borderColor: '#667eea',
            backgroundColor: 'rgba(102,126,234,0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => '₹' + v } } }
    }
});

@if($modeBreakdown->count() > 0)
new Chart(document.getElementById('modeChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($modeBreakdown->keys()->map(fn($k) => strtoupper($k))) !!},
        datasets: [{
            data: {!! json_encode($modeBreakdown->values()) !!},
            backgroundColor: ['#166534', '#6d28d9', '#1d4ed8', '#9ca3af'],
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
<script src="{{ asset('assets/js/owner-reports.js') }}?v={{ @filemtime(public_path('assets/js/owner-reports.js')) }}"></script>
@endpush
