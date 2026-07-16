@extends('layouts.app')
@section('title', 'Member Details')
@section('page-title', 'Member Details')
@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/owner-members-card.css') }}" rel="stylesheet">
@endpush

@section('content')

@php
    $totalDays = ($member->plan_start_date && $member->plan_end_date) ? max(1, $member->plan_start_date->diffInDays($member->plan_end_date)) : 1;
    $daysLeft = $member->daysLeft();
    $pct = $member->plan_end_date ? max(0, min(100, ($daysLeft / $totalDays) * 100)) : 0;
    $barClass = $daysLeft <= 3 ? 'danger' : ($daysLeft <= 7 ? 'warn' : '');
@endphp

<div class="row g-3">
    <div class="col-lg-4">
        <div class="table-card p-4 text-center member-card" style="overflow:visible">
            <div class="member-card-accent member-accent-{{ $member->status }}"></div>
            <div class="member-avatar mx-auto mb-3" style="width:72px;height:72px;font-size:28px;">
                {{ substr($member->user->name, 0, 1) }}
            </div>
            <h5 class="fw-bold mb-0">{{ $member->user->name }}</h5>
            <p class="text-muted small">{{ $member->user->email }}</p>

            <div class="member-uid-chip mx-auto mb-3" style="font-size:16px;letter-spacing:2px;">
                <i class="bi bi-upc-scan"></i>{{ $member->uid }}
            </div>

            <div class="row g-2 text-start mb-3">
                <div class="col-6"><small class="text-muted">Phone</small><div class="fw-500">{{ $member->user->phone }}</div></div>
                <div class="col-6"><small class="text-muted">Status</small><div><span class="badge badge-{{ $member->status }}">{{ ucfirst($member->status) }}</span></div></div>
                <div class="col-6"><small class="text-muted">Seat</small><div class="fw-500">{{ $member->seat?->seat_number ?? '-' }}</div></div>
                <div class="col-6"><small class="text-muted">Shift</small><div class="fw-500">{{ $member->shift?->name ?? '-' }}</div></div>
                <div class="col-6"><small class="text-muted">Plan Start</small><div class="fw-500">{{ $member->plan_start_date?->format('d M Y') ?? '-' }}</div></div>
                <div class="col-6"><small class="text-muted">Plan End</small><div class="fw-500 {{ $daysLeft <= 7 ? 'text-danger' : '' }}">{{ $member->plan_end_date?->format('d M Y') ?? '-' }}</div></div>
            </div>

            @if($member->plan_end_date)
            <div class="text-start mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Membership Progress</small>
                    <small class="fw-600">{{ $daysLeft }} days left</small>
                </div>
                <div class="mini-progress {{ $barClass }}"><span style="width:{{ $pct }}%"></span></div>
            </div>
            @endif

            <div class="d-flex gap-2 mt-3">
                <a href="/owner/members/{{ $member->id }}/edit" class="btn btn-outline-primary flex-fill btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
                <a href="/owner/fees/collect?member_id={{ $member->id }}" class="btn btn-success flex-fill btn-sm"><i class="bi bi-cash me-1"></i>Collect Fee</a>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="table-card mb-3">
            <div class="card-header"><i class="bi bi-receipt me-2"></i>Payment History</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 rtable">
                    <thead class="table-light"><tr><th>Receipt</th><th>Amount</th><th>Mode</th><th>Date</th><th>Valid Till</th><th></th></tr></thead>
                    <tbody>
                        @forelse($member->feePayments as $p)
                        <tr>
                            <td data-label="Receipt"><code>{{ $p->receipt_number }}</code></td>
                            <td data-label="Amount" class="text-success fw-600">₹{{ number_format($p->amount) }}</td>
                            <td data-label="Mode" class="text-uppercase">{{ $p->payment_mode }}</td>
                            <td data-label="Date">{{ $p->payment_date->format('d M Y') }}</td>
                            <td data-label="Valid Till">{{ $p->valid_till->format('d M Y') }}</td>
                            <td data-label="Receipt"><a href="/owner/fees/{{ $p->id }}/receipt" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="6">
                            <div class="empty-state py-4">
                                <div class="es-icon"><i class="bi bi-receipt"></i></div>
                                <p class="mb-0">No payments yet</p>
                            </div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Attendance (Last 20 days)</div>
            <div class="table-responsive">
                <table class="table mb-0 rtable">
                    <thead class="table-light"><tr><th>Date</th><th>In</th><th>Out</th><th>Duration</th></tr></thead>
                    <tbody>
                        @forelse($member->attendance->take(20) as $a)
                        <tr>
                            <td data-label="Date">{{ $a->date->format('d M') }}</td>
                            <td data-label="In">{{ $a->check_in?->format('h:i A') ?? '-' }}</td>
                            <td data-label="Out">{{ $a->check_out?->format('h:i A') ?? 'In Library' }}</td>
                            <td data-label="Duration">{{ $a->duration() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4">
                            <div class="empty-state py-4">
                                <div class="es-icon"><i class="bi bi-clock-history"></i></div>
                                <p class="mb-0">No attendance records</p>
                            </div>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
