@extends('layouts.app')
@section('title', 'Fee Payments')
@section('page-title', 'Fee Payments')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('content')

@php
    $last7Days = collect(range(6, 0))->map(function ($d) use ($library) {
        $date = today()->subDays($d);
        return [
            'label'  => $date->format('D'),
            'amount' => (float) \App\Models\FeePayment::where('library_id', $library->id)
                ->whereDate('payment_date', $date)->sum('amount'),
        ];
    });
    $recentPaymentsFeed = \App\Models\FeePayment::with('member.user')
        ->where('library_id', $library->id)->latest()->take(6)->get();
@endphp

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#dcfce7;color:#166534"><i class="bi bi-calendar-day"></i></div>
            <h3>₹{{ number_format($summary['today']) }}</h3>
            <p>Today</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-calendar-month"></i></div>
            <h3>₹{{ number_format($summary['month']) }}</h3>
            <p>This Month</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#fef3c7;color:#92400e"><i class="bi bi-cash"></i></div>
            <h3>₹{{ number_format($summary['cash']) }}</h3>
            <p>Cash (Month)</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#ede9fe;color:#6d28d9"><i class="bi bi-phone"></i></div>
            <h3>₹{{ number_format($summary['upi']) }}</h3>
            <p>UPI (Month)</p>
        </div>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="table-card p-4 h-100">
            <div class="section-head mb-2">
                <h6><i class="bi bi-graph-up me-2"></i>Revenue &mdash; Last 7 Days</h6>
            </div>
            <canvas id="revenueChart" height="90"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="table-card p-4 h-100">
            <div class="section-head mb-3">
                <h6><i class="bi bi-activity me-2"></i>Payment Timeline</h6>
            </div>
            @if($recentPaymentsFeed->count() > 0)
            <div class="timeline">
                @foreach($recentPaymentsFeed as $pf)
                <div class="timeline-item tl-success">
                    <div class="tl-title">{{ $pf->member->user->name ?? 'A member' }} &middot; ₹{{ number_format($pf->amount) }}</div>
                    <div class="tl-meta">{{ strtoupper($pf->payment_mode) }} &middot; {{ $pf->created_at->diffForHumans() }}</div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-muted small mb-0">No payments recorded yet.</p>
            @endif
        </div>
    </div>
</div>

<div class="table-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-receipt me-2"></i>All Payments</span>
        <div class="d-flex gap-2 flex-wrap">
            <form method="GET" class="d-flex gap-2 flex-wrap">
                <div class="member-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search member..." value="{{ request('search') }}">
                </div>
                <select name="mode" class="form-select form-select-sm" style="width:110px">
                    <option value="">All Mode</option>
                    <option value="cash" {{ request('mode')=='cash'?'selected':'' }}>Cash</option>
                    <option value="upi" {{ request('mode')=='upi'?'selected':'' }}>UPI</option>
                </select>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="width:140px">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" style="width:140px">
                <button class="btn btn-sm btn-outline-secondary">Filter</button>
            </form>
            <a href="/owner/fees/collect" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>Collect Fee</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 rtable">
            <thead class="table-light">
                <tr><th>Receipt No.</th><th>Member</th><th>Amount</th><th>Mode</th><th>Date</th><th>Valid Till</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                @php
                    $modeIcon = ['cash'=>'bi-cash-stack','upi'=>'bi-phone-fill','bank'=>'bi-bank','other'=>'bi-three-dots'][$p->payment_mode] ?? 'bi-cash';
                    $modeColor = ['cash'=>'#166534','upi'=>'#6d28d9','bank'=>'#1d4ed8','other'=>'#6c757d'][$p->payment_mode] ?? '#6c757d';
                @endphp
                <tr>
                    <td data-label="Receipt"><code>{{ $p->receipt_number }}</code></td>
                    <td data-label="Member">
                        <div class="fw-500">{{ $p->member->user->name ?? '-' }}</div>
                        <small class="text-muted">{{ $p->member->uid ?? '' }}</small>
                    </td>
                    <td data-label="Amount" class="fw-600 text-success">₹{{ number_format($p->amount) }}</td>
                    <td data-label="Mode">
                        <span class="badge bg-light text-uppercase" style="color:{{ $modeColor }}"><i class="bi {{ $modeIcon }} me-1"></i>{{ $p->payment_mode }}</span>
                    </td>
                    <td data-label="Date">{{ $p->payment_date->format('d M Y') }}</td>
                    <td data-label="Valid Till">{{ $p->valid_till->format('d M Y') }}</td>
                    <td data-label="Action">
                        <div class="d-flex gap-1">
                            <a href="/owner/fees/{{ $p->id }}/receipt" class="btn btn-sm btn-outline-primary" title="View Receipt"><i class="bi bi-eye"></i></a>
                            <a href="/owner/fees/{{ $p->id }}/download" class="btn btn-sm btn-outline-danger" title="Download PDF"><i class="bi bi-file-pdf"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <div class="es-icon"><i class="bi bi-receipt"></i></div>
                        <h6>No payments found</h6>
                        <p>Try adjusting your filters, or collect your first fee payment.</p>
                        <a href="/owner/fees/collect" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Collect Fee</a>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="p-3">{{ $payments->withQueryString()->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($last7Days->pluck('label')) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($last7Days->pluck('amount')) !!},
            borderColor: '#667eea',
            backgroundColor: 'rgba(102,126,234,0.1)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#667eea',
            pointRadius: 4,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => '₹' + v } },
        }
    }
});
</script>
@endpush
