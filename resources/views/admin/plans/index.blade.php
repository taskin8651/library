@extends('layouts.app')
@section('title', 'Plans')
@section('page-title', 'Manage Plans')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/admin-panel.css') }}?v={{ @filemtime(public_path('assets/css/admin-panel.css')) }}" rel="stylesheet">
@endpush

@section('content')

@php
    $activePlanCount = $plans->where('is_active', true)->count();
    $totalSubscribed = $plans->sum('libraries_count');
@endphp

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="sc-icon" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-star-fill"></i></div>
            <div><div class="sc-num">{{ $plans->count() }}</div><div class="sc-label">Total Plans</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="sc-icon" style="background:#dcfce7;color:#166534"><i class="bi bi-check-circle-fill"></i></div>
            <div><div class="sc-num">{{ $activePlanCount }}</div><div class="sc-label">Active Plans</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="sc-icon" style="background:#fee2e2;color:#991b1b"><i class="bi bi-x-circle-fill"></i></div>
            <div><div class="sc-num">{{ $plans->count() - $activePlanCount }}</div><div class="sc-label">Inactive Plans</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="sc-icon" style="background:#ede9fe;color:#6d28d9"><i class="bi bi-building"></i></div>
            <div><div class="sc-num">{{ $totalSubscribed }}</div><div class="sc-label">Subscribed Libraries</div></div>
        </div>
    </div>
</div>

<div class="row g-3">
@forelse($plans as $plan)
<div class="col-md-6 col-lg-4">
    <div class="admin-plan-card {{ $plan->is_active ? '' : 'is-inactive' }}">
        <div class="apc-head">
            <h5 class="fw-bold mb-0">{{ $plan->name }}</h5>
            <span class="badge {{ $plan->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $plan->is_active ? 'Active' : 'Inactive' }}</span>
        </div>
        <p class="text-muted small mb-2">{{ $plan->description }}</p>
        <div class="apc-price">₹{{ number_format($plan->price) }}<span>/mo</span></div>

        <div class="apc-count-pill">
            <span><i class="bi bi-building me-1"></i>Libraries on this plan</span>
            <strong>{{ $plan->libraries_count }}</strong>
        </div>

        <div class="mb-3">
            <div class="apc-feature-row">
                <i class="bi bi-diagram-3-fill yes"></i>
                {{ $plan->max_branches == -1 ? 'Unlimited branches' : $plan->max_branches . ' branch' . ($plan->max_branches == 1 ? '' : 'es') }}
            </div>
            <div class="apc-feature-row">
                <i class="bi {{ $plan->staff_accounts ? 'bi-check-circle-fill yes' : 'bi-x-circle no' }}"></i>
                Staff accounts
            </div>
            <div class="apc-feature-row">
                <i class="bi {{ $plan->white_label ? 'bi-check-circle-fill yes' : 'bi-x-circle no' }}"></i>
                White label branding
            </div>
        </div>

        <form method="POST" action="/admin/plans/{{ $plan->id }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label small fw-600">Price (₹ / month)</label>
                <input type="number" name="price" class="form-control form-control-sm" value="{{ $plan->price }}">
            </div>
            <label class="toggle-switch mb-3">
                <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }}>
                <span class="ts-track"></span>
                <span class="ts-label">Plan is active</span>
            </label>
            <button class="btn btn-primary btn-sm w-100"><i class="bi bi-save me-1"></i>Update Plan</button>
        </form>
    </div>
</div>
@empty
<div class="col-12">
    <div class="table-card">
        <div class="empty-state">
            <div class="es-icon"><i class="bi bi-star"></i></div>
            <h6>No plans configured yet</h6>
            <p>Plans are seeded from the database — none are set up right now.</p>
        </div>
    </div>
</div>
@endforelse
</div>
@endsection
