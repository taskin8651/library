@extends('layouts.app')
@section('title', 'Payments')
@section('page-title', 'UPI Payment Verification')

@section('sidebar-menu')
<ul class="nav flex-column py-3">
    <li><a href="/admin/dashboard" class="nav-link"><i class="bi bi-grid-fill"></i> Dashboard</a></li>
    <li class="sidebar-section mt-2">Management</li>
    <li><a href="/admin/libraries" class="nav-link"><i class="bi bi-building"></i> Libraries</a></li>
    <li><a href="/admin/plans" class="nav-link"><i class="bi bi-star-fill"></i> Plans</a></li>
    <li><a href="/admin/payments" class="nav-link active"><i class="bi bi-cash-coin"></i> Payments
        @if($awaitingVerification->count())<span class="badge bg-danger ms-1">{{ $awaitingVerification->count() }}</span>@endif
    </a></li>
</ul>
@endsection

@section('content')

<div class="table-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-hourglass-split me-2"></i>Awaiting Verification</span>
        <span class="badge bg-warning text-dark">{{ $awaitingVerification->count() }} pending</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Library</th><th>Plan</th><th>Amount</th><th>UTR / Ref No.</th><th>Submitted</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse($awaitingVerification as $sub)
                <tr>
                    <td>
                        <div class="fw-500">{{ $sub->library->name }}</div>
                        <small class="text-muted">{{ $sub->library->email }} &middot; {{ $sub->library->phone }}</small>
                    </td>
                    <td><span class="badge bg-light text-dark">{{ $sub->plan->name ?? '-' }}</span></td>
                    <td class="fw-600">₹{{ number_format($sub->amount) }}</td>
                    <td><code>{{ $sub->utr }}</code></td>
                    <td>{{ $sub->updated_at->format('d M Y, h:i A') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="POST" action="/admin/payments/{{ $sub->id }}/approve">
                                @csrf
                                <button class="btn btn-sm btn-success"><i class="bi bi-check-lg me-1"></i>Approve</button>
                            </form>
                            <form method="POST" action="/admin/payments/{{ $sub->id }}/reject" data-confirm="Mark this payment as failed? The owner will need to submit it again.">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No payments waiting for verification</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="table-card">
    <div class="card-header"><i class="bi bi-clock-history me-2"></i>Payment History</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Library</th><th>Plan</th><th>Amount</th><th>Status</th><th>UTR</th><th>Date</th></tr>
            </thead>
            <tbody>
                @forelse($history as $sub)
                <tr>
                    <td>{{ $sub->library->name ?? '-' }}</td>
                    <td>{{ $sub->plan->name ?? '-' }}</td>
                    <td class="fw-600">₹{{ number_format($sub->amount) }}</td>
                    <td>
                        <span class="badge {{ $sub->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </td>
                    <td><code>{{ $sub->utr ?? '-' }}</code></td>
                    <td>{{ $sub->updated_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No processed payments yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
