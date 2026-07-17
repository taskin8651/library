@extends('layouts.app')
@section('title', 'Choose Plan')
@section('page-title', 'Subscription Plans')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/owner-subscription.css') }}" rel="stylesheet">
@endpush

@section('content')

@php
    $currentPlan = $plans->firstWhere('id', $library->plan_id);
    $onTrial = $library->isOnTrial();
    $daysLeft = $library->daysLeft();
    $periodStart = $onTrial ? ($library->trial_ends_at?->copy()->subDays(3)) : $library->plan_expires_at?->copy()->subDays(30);
    $totalDays = $onTrial ? 3 : 30;
    $usedDays = max(0, $totalDays - $daysLeft);
    $usagePct = $totalDays > 0 ? max(0, min(100, ($usedDays / $totalDays) * 100)) : 0;

    $subscriptionHistory = \App\Models\Subscription::where('library_id', $library->id)
        ->latest()
        ->take(5)
        ->get();
@endphp

<!-- Status Hero -->
<div class="sub-status-card mb-4">
    <div class="row align-items-center g-3">
        <div class="col-md-7">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge {{ $onTrial ? 'bg-warning text-dark' : 'badge-active' }}">
                    {{ $onTrial ? 'Free Trial' : ($library->isActive() ? 'Active Subscription' : 'Expired') }}
                </span>
                @if($currentPlan)
                <span class="text-muted small">on {{ $currentPlan->name }} plan</span>
                @endif
            </div>
            <h5 class="fw-bold mb-1">{{ $onTrial ? 'Your free trial is running' : 'Your plan is active' }}</h5>
            <p class="text-muted small mb-0">
                @if($library->plan_expires_at)
                    Renews / expires on <strong>{{ $library->plan_expires_at->format('d M Y') }}</strong>
                @else
                    No active billing cycle yet.
                @endif
            </p>
        </div>
        <div class="col-md-5">
            <div class="d-flex justify-content-between mb-1">
                <small class="text-muted">Usage this cycle</small>
                <small class="fw-600">{{ $daysLeft }} days left</small>
            </div>
            <div class="mini-progress {{ $daysLeft <= 3 ? 'danger' : ($daysLeft <= 7 ? 'warn' : '') }}">
                <span style="width:{{ $usagePct }}%"></span>
            </div>
        </div>
    </div>
</div>

<div class="text-center mb-5">
    <h3 class="fw-bold">Choose Your Plan</h3>
    <p class="text-muted">Simple, transparent pricing. Cancel anytime.</p>
</div>

<div class="row justify-content-center g-4 mb-4">
    @foreach($plans as $plan)
    @php $isCurrent = $library->plan_id == $plan->id; @endphp
    <div class="col-md-4">
        <div class="table-card p-4 h-100 sub-plan-card {{ $isCurrent ? 'sub-plan-current' : '' }}">
            @if($isCurrent)
                <div class="text-center mb-3"><span class="badge bg-primary px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i>Current Plan</span></div>
            @endif
            <h5 class="fw-bold">{{ $plan->name }}</h5>
            <div class="mb-3">
                <span class="display-6 fw-bold">₹{{ number_format($plan->price) }}</span>
                <span class="text-muted">/month</span>
            </div>
            <p class="text-muted small">{{ $plan->description }}</p>

            <ul class="list-unstyled mb-4">
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>
                    {{ $plan->max_branches == -1 ? 'Unlimited' : $plan->max_branches }} Branch{{ $plan->max_branches != 1 ? 'es' : '' }}
                </li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Unlimited Members</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>QR Check-in System</li>
                <li class="mb-2">
                    @if($plan->staff_accounts)
                        <i class="bi bi-check-circle-fill text-success me-2"></i>Staff Accounts
                    @else
                        <i class="bi bi-x-circle-fill text-muted me-2"></i><span class="text-muted">Staff Accounts</span>
                    @endif
                </li>
                <li class="mb-2">
                    @if($plan->white_label)
                        <i class="bi bi-check-circle-fill text-success me-2"></i>White Label Branding
                    @else
                        <i class="bi bi-x-circle-fill text-muted me-2"></i><span class="text-muted">White Label</span>
                    @endif
                </li>
            </ul>

            @if($isCurrent)
                <button class="btn btn-outline-primary w-100" disabled>Current Plan</button>
            @else
                <button class="btn btn-primary w-100" onclick="startUpiPayment({{ $plan->id }})">
                    <i class="bi bi-qr-code me-2"></i>{{ $onTrial ? 'Subscribe' : 'Renew / Switch' }} - ₹{{ number_format($plan->price) }}/mo
                </button>
            @endif
        </div>
    </div>
    @endforeach
</div>

<!-- Payment History -->
<div class="table-card">
    <div class="card-header"><i class="bi bi-clock-history me-2"></i>Subscription History</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 rtable">
            <thead class="table-light"><tr><th>Plan</th><th>Amount</th><th>Status</th><th>Started</th><th>Expires</th></tr></thead>
            <tbody>
                @forelse($subscriptionHistory as $sub)
                <tr>
                    <td data-label="Plan">{{ $sub->plan->name ?? '-' }}</td>
                    <td data-label="Amount" class="fw-600">₹{{ number_format($sub->amount) }}</td>
                    <td data-label="Status">
                        <span class="badge {{ $sub->status === 'active' ? 'badge-active' : ($sub->status === 'pending' ? 'bg-warning text-dark' : 'badge-inactive') }}">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </td>
                    <td data-label="Started">{{ $sub->starts_at?->format('d M Y') ?? '-' }}</td>
                    <td data-label="Expires">{{ $sub->expires_at?->format('d M Y') ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5">
                    <div class="empty-state">
                        <div class="es-icon"><i class="bi bi-receipt"></i></div>
                        <h6>No subscription history yet</h6>
                        <p>Your billing history will show up here once you subscribe to a plan.</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- UPI Payment Modal -->
<div class="modal fade" id="upiPaymentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:20px;border:none;">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-bold" id="upiModalTitle">Pay via UPI</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-2 text-center">
        <div id="upiQrBox" class="upi-qr-box"></div>
        <div class="upi-amount" id="upiAmount"></div>

        <div class="upi-id-row">
            <code id="upiIdText"></code>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="upiCopyBtn">
                <i class="bi bi-clipboard me-1"></i>Copy
            </button>
        </div>

        <p class="upi-steps text-start">
            1. Scan the QR (or use the UPI ID) with any UPI app — GPay, PhonePe, Paytm, etc.<br>
            2. After paying, enter the UPI transaction reference (UTR) below.<br>
            3. We'll verify and activate your plan shortly after.
        </p>

        <form id="upiUtrForm" class="text-start mt-3">
            <div class="mb-2">
                <label class="form-label small fw-600">UPI Transaction Ref. (UTR) *</label>
                <input type="text" id="upiUtrInput" class="form-control" placeholder="e.g. 123456789012" required maxlength="50">
            </div>
            <div id="upiFormMsg" class="small mb-2"></div>
            <button type="submit" class="btn btn-primary w-100" id="upiSubmitBtn">
                <i class="bi bi-check-circle me-1"></i>I've Paid — Submit for Verification
            </button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/owner-subscription-plans.js') }}"></script>
@endpush
