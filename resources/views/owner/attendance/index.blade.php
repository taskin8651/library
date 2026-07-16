@extends('layouts.app')
@section('title', 'Attendance')
@section('page-title', 'Attendance Log')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('content')

@php
    $last7CheckIns = collect(range(6, 0))->map(function ($d) use ($library) {
        $date = today()->subDays($d);
        return [
            'label' => $date->format('D'),
            'count' => \App\Models\Attendance::where('library_id', $library->id)->whereDate('date', $date)->count(),
        ];
    });
    $viewedDayFeed = $attendance->sortByDesc(fn($a) => $a->check_out ?? $a->check_in)->take(6);
@endphp

<!-- Today Summary -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#dcfce7;color:#166534"><i class="bi bi-person-check-fill"></i></div>
            <h3>{{ $currently_in->count() }}</h3>
            <p>Present Now</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-list-check"></i></div>
            <h3>{{ $attendance->total() }}</h3>
            <p>Records ({{ request('date', date('Y-m-d')) === date('Y-m-d') ? 'Today' : request('date') }})</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#fef3c7;color:#92400e"><i class="bi bi-qr-code-scan"></i></div>
            <h3>QR</h3>
            <p>Check-in Method</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#ede9fe;color:#6d28d9"><i class="bi bi-calendar-event"></i></div>
            <h3>{{ \Carbon\Carbon::parse(request('date', date('Y-m-d')))->format('d M') }}</h3>
            <p>Viewing Date</p>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="table-card p-4 h-100">
            <div class="section-head mb-2">
                <h6><i class="bi bi-bar-chart-fill me-2"></i>Check-ins &mdash; Last 7 Days</h6>
            </div>
            <canvas id="checkinChart" height="90"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="table-card p-4 h-100">
            <div class="section-head mb-3">
                <h6><i class="bi bi-activity me-2"></i>Latest Activity</h6>
            </div>
            @if($viewedDayFeed->count() > 0)
            <div class="timeline">
                @foreach($viewedDayFeed as $ev)
                <div class="timeline-item {{ $ev->check_out ? 'tl-warn' : 'tl-success' }}">
                    <div class="tl-title">{{ $ev->member->user->name ?? 'A member' }} {{ $ev->check_out ? 'checked out' : 'checked in' }}</div>
                    <div class="tl-meta">Seat {{ $ev->seat->seat_number ?? '-' }} &middot; {{ ($ev->check_out ?? $ev->check_in)->diffForHumans() }}</div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-muted small mb-0">No activity for this date.</p>
            @endif
        </div>
    </div>
</div>

<!-- Currently In Library -->
@if($currently_in->count() > 0)
<div class="table-card mb-3">
    <div class="card-header d-flex align-items-center gap-2">
        <span class="pulse-dot-live"></span>
        <span>Currently In Library ({{ $currently_in->count() }})</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 rtable">
            <thead class="table-light"><tr><th>Member</th><th>Seat</th><th>Check In</th><th>Duration</th></tr></thead>
            <tbody>
                @foreach($currently_in as $a)
                <tr>
                    <td data-label="Member" class="fw-500">{{ $a->member->user->name ?? '-' }}</td>
                    <td data-label="Seat"><span class="badge bg-primary">{{ $a->seat->seat_number ?? '-' }}</span></td>
                    <td data-label="Check In">{{ $a->check_in->format('h:i A') }}</td>
                    <td data-label="Duration">{{ $a->duration() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Attendance Log -->
<div class="table-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-calendar me-2"></i>Attendance Log</span>
        <form method="GET" class="d-flex gap-2">
            <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date', date('Y-m-d')) }}">
            <button class="btn btn-sm btn-outline-secondary">Filter</button>
            <a href="/owner/attendance/qr" class="btn btn-sm btn-primary"><i class="bi bi-qr-code me-1"></i>QR Code</a>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 rtable">
            <thead class="table-light"><tr><th>Member</th><th>Seat</th><th>Check In</th><th>Check Out</th><th>Duration</th></tr></thead>
            <tbody>
                @forelse($attendance as $a)
                <tr class="reveal-auto" style="animation-delay: {{ min($loop->index, 20) * 0.03 }}s">
                    <td data-label="Member" class="fw-500">{{ $a->member->user->name ?? '-' }}</td>
                    <td data-label="Seat">{{ $a->seat->seat_number ?? '-' }}</td>
                    <td data-label="Check In">{{ $a->check_in ? $a->check_in->format('h:i A') : '-' }}</td>
                    <td data-label="Check Out">{!! $a->check_out ? $a->check_out->format('h:i A') : '<span class="badge-live"><span class="pulse-dot-live"></span>In Library</span>' !!}</td>
                    <td data-label="Duration">{{ $a->duration() }}</td>
                </tr>
                @empty
                <tr><td colspan="5">
                    <div class="empty-state">
                        <div class="es-icon"><i class="bi bi-calendar-x"></i></div>
                        <h6>No attendance records</h6>
                        <p>No check-ins recorded for this date yet.</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($attendance->hasPages())
    <div class="p-3">{{ $attendance->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('checkinChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($last7CheckIns->pluck('label')) !!},
        datasets: [{
            label: 'Check-ins',
            data: {!! json_encode($last7CheckIns->pluck('count')) !!},
            backgroundColor: '#f59e0b',
            borderRadius: 6,
            maxBarThickness: 36,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});
</script>
@endpush
