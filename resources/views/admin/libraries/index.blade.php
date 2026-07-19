@extends('layouts.app')
@section('title', 'Libraries')
@section('page-title', 'All Libraries')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/admin-panel.css') }}?v={{ @filemtime(public_path('assets/css/admin-panel.css')) }}" rel="stylesheet">
@endpush

@section('content')

@php
    // Summary tiles need counts across ALL libraries, not just the current
    // paginated page — queried fresh here rather than derived from $libraries.
    $totalCount = \App\Models\Library::count();
    $activeCount = \App\Models\Library::where('status', 'active')->count();
    $inactiveCount = \App\Models\Library::whereIn('status', ['suspended', 'expired'])->count();
    $todayCount = \App\Models\Library::whereDate('created_at', today())->count();
@endphp

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="sc-icon" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-building"></i></div>
            <div><div class="sc-num">{{ $totalCount }}</div><div class="sc-label">Total</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="sc-icon" style="background:#dcfce7;color:#166534"><i class="bi bi-check-circle-fill"></i></div>
            <div><div class="sc-num">{{ $activeCount }}</div><div class="sc-label">Active</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="sc-icon" style="background:#fee2e2;color:#991b1b"><i class="bi bi-slash-circle-fill"></i></div>
            <div><div class="sc-num">{{ $inactiveCount }}</div><div class="sc-label">Inactive</div></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="summary-card">
            <div class="sc-icon" style="background:#ede9fe;color:#6d28d9"><i class="bi bi-calendar-plus-fill"></i></div>
            <div><div class="sc-num">{{ $todayCount }}</div><div class="sc-label">Today</div></div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-building me-2"></i>Libraries ({{ $libraries->total() }})</span>
        <form method="GET" class="admin-toolbar">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name or email..." value="{{ request('search') }}" style="width:200px">
            <select name="status" class="form-select form-select-sm" style="width:140px">
                <option value="">All Status</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="suspended" {{ request('status')=='suspended'?'selected':'' }}>Suspended</option>
                <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Expired</option>
            </select>
            <button class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
            @if(request('search') || request('status'))
            <a href="/admin/libraries" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 rtable">
            <thead class="table-light">
                <tr><th>Library</th><th>Phone</th><th>Plan</th><th>Status</th><th>Expires</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($libraries as $lib)
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
                    <td data-label="Phone">{{ $lib->phone }}</td>
                    <td data-label="Plan"><span class="badge bg-light text-dark">{{ $lib->plan->name }}</span></td>
                    <td data-label="Status">
                        @if($lib->status=='active') <span class="badge badge-active">Active</span>
                        @elseif($lib->status=='pending') <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($lib->status=='suspended') <span class="badge badge-inactive">Suspended</span>
                        @else <span class="badge badge-expired">Expired</span>
                        @endif
                    </td>
                    <td data-label="Expires">{{ $lib->plan_expires_at?->format('d M Y') ?? '-' }}</td>
                    <td data-label="Actions">
                        <div class="d-flex gap-1">
                            @if($lib->status != 'active')
                            <form method="POST" action="/admin/libraries/{{ $lib->id }}/approve">
                                @csrf <button class="btn btn-sm btn-success"><i class="bi bi-check-lg me-1"></i>Approve</button>
                            </form>
                            @else
                            <form method="POST" action="/admin/libraries/{{ $lib->id }}/suspend">
                                @csrf <button class="btn btn-sm btn-outline-danger"><i class="bi bi-slash-circle me-1"></i>Suspend</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6">
                    <div class="empty-state">
                        <div class="es-icon"><i class="bi bi-building"></i></div>
                        <h6>No libraries found</h6>
                        <p>Try a different search term or clear the status filter.</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($libraries->hasPages())
    <div class="p-3">{{ $libraries->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
