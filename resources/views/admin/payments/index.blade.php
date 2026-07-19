@extends('layouts.app')
@section('title', 'Payments')
@section('page-title', 'UPI Payment Verification')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/admin-panel.css') }}?v={{ @filemtime(public_path('assets/css/admin-panel.css')) }}" rel="stylesheet">
@endpush

@section('content')

@php
    // Full counts across ALL subscriptions — $history below is capped to the
    // last 30 rows for display, so these summary tiles are queried fresh.
    $approvedCount = \App\Models\Subscription::where('status', 'active')->count();
    $failedCount = \App\Models\Subscription::whereIn('status', ['failed', 'cancelled'])->count();
    $totalCollected = \App\Models\Subscription::where('status', 'active')->sum('amount');
@endphp

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="sc-icon" style="background:#fef3c7;color:#92400e"><i class="bi bi-hourglass-split"></i></div>
            <div><div class="sc-num">{{ $awaitingVerification->count() }}</div><div class="sc-label">Pending</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="sc-icon" style="background:#dcfce7;color:#166534"><i class="bi bi-check-circle-fill"></i></div>
            <div><div class="sc-num">{{ $approvedCount }}</div><div class="sc-label">Approved</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="sc-icon" style="background:#fee2e2;color:#991b1b"><i class="bi bi-x-circle-fill"></i></div>
            <div><div class="sc-num">{{ $failedCount }}</div><div class="sc-label">Failed</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="sc-icon" style="background:#ede9fe;color:#6d28d9"><i class="bi bi-currency-rupee"></i></div>
            <div><div class="sc-num">₹{{ number_format($totalCollected) }}</div><div class="sc-label">Collected</div></div>
        </div>
    </div>
</div>

<div class="table-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-hourglass-split me-2"></i>Awaiting Verification</span>
        <span class="badge bg-warning text-dark">{{ $awaitingVerification->count() }} pending</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 rtable">
            <thead class="table-light">
                <tr><th>Library</th><th>Plan</th><th>Amount</th><th>UTR / Ref No.</th><th>Submitted</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse($awaitingVerification as $sub)
                <tr>
                    <td data-label="Library">
                        <div class="entity-row">
                            <span class="entity-avatar">{{ substr($sub->library->name ?? '?', 0, 1) }}</span>
                            <div class="min-w-0">
                                <div class="fw-600 text-truncate">{{ $sub->library->name }}</div>
                                <small class="text-muted text-truncate d-block">{{ $sub->library->email }} &middot; {{ $sub->library->phone }}</small>
                            </div>
                        </div>
                    </td>
                    <td data-label="Plan"><span class="badge bg-light text-dark">{{ $sub->plan->name ?? '-' }}</span></td>
                    <td data-label="Amount" class="fw-600">₹{{ number_format($sub->amount) }}</td>
                    <td data-label="UTR"><span class="utr-chip">{{ $sub->utr }}</span></td>
                    <td data-label="Submitted">{{ $sub->updated_at->format('d M Y, h:i A') }}</td>
                    <td data-label="Action">
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
                <tr><td colspan="6">
                    <div class="empty-state">
                        <div class="es-icon"><i class="bi bi-check2-circle"></i></div>
                        <h6>All caught up!</h6>
                        <p>No payments are waiting for verification right now.</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="table-card">
    <div class="card-header"><i class="bi bi-clock-history me-2"></i>Payment History</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 rtable">
            <thead class="table-light">
                <tr><th>Library</th><th>Plan</th><th>Amount</th><th>Status</th><th>UTR</th><th>Date</th></tr>
            </thead>
            <tbody>
                @forelse($history as $sub)
                <tr>
                    <td data-label="Library">
                        <div class="entity-row">
                            <span class="entity-avatar" style="width:32px;height:32px;font-size:12px;border-radius:9px">{{ substr($sub->library->name ?? '?', 0, 1) }}</span>
                            <span class="text-truncate">{{ $sub->library->name ?? '-' }}</span>
                        </div>
                    </td>
                    <td data-label="Plan">{{ $sub->plan->name ?? '-' }}</td>
                    <td data-label="Amount" class="fw-600">₹{{ number_format($sub->amount) }}</td>
                    <td data-label="Status">
                        <span class="badge {{ $sub->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </td>
                    <td data-label="UTR">@if($sub->utr)<span class="utr-chip">{{ $sub->utr }}</span>@else-@endif</td>
                    <td data-label="Date">{{ $sub->updated_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="6">
                    <div class="empty-state">
                        <div class="es-icon"><i class="bi bi-receipt"></i></div>
                        <h6>No processed payments yet</h6>
                        <p>Approved and rejected payments will show up here.</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
