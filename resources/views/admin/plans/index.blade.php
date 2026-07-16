@extends('layouts.app')
@section('title', 'Plans')
@section('page-title', 'Manage Plans')
@section('sidebar-menu')
<ul class="nav flex-column py-3">
    <li><a href="/admin/dashboard" class="nav-link"><i class="bi bi-grid-fill"></i> Dashboard</a></li>
    <li class="sidebar-section mt-2">Management</li>
    <li><a href="/admin/libraries" class="nav-link"><i class="bi bi-building"></i> Libraries</a></li>
    <li><a href="/admin/plans" class="nav-link active"><i class="bi bi-star-fill"></i> Plans</a></li>
</ul>
@endsection
@section('content')
<div class="row g-3">
@foreach($plans as $plan)
<div class="col-md-4">
    <div class="table-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">{{ $plan->name }}</h5>
            <span class="badge {{ $plan->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $plan->is_active ? 'Active' : 'Inactive' }}</span>
        </div>
        <p class="text-muted small">{{ $plan->description }}</p>
        <h3 class="fw-bold text-primary">₹{{ number_format($plan->price) }}<span class="text-muted fs-6 fw-normal">/mo</span></h3>
        <p class="text-muted small">{{ $plan->libraries_count }} libraries on this plan</p>
        <form method="POST" action="/admin/plans/{{ $plan->id }}">
            @csrf @method('PUT')
            <div class="mb-2">
                <label class="form-label small">Price (₹)</label>
                <input type="number" name="price" class="form-control form-control-sm" value="{{ $plan->price }}">
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" name="is_active" id="active_{{ $plan->id }}" {{ $plan->is_active ? 'checked' : '' }}>
                <label class="form-check-label small" for="active_{{ $plan->id }}">Active</label>
            </div>
            <button class="btn btn-primary btn-sm w-100">Update Plan</button>
        </form>
    </div>
</div>
@endforeach
</div>
@endsection
