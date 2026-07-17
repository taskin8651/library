@extends('layouts.app')
@section('title', 'Libraries')
@section('page-title', 'All Libraries')

@section('sidebar-menu')
<ul class="nav flex-column py-3">
    <li><a href="/admin/dashboard" class="nav-link"><i class="bi bi-grid-fill"></i> Dashboard</a></li>
    <li class="sidebar-section mt-2">Management</li>
    <li><a href="/admin/libraries" class="nav-link active"><i class="bi bi-building"></i> Libraries</a></li>
    <li><a href="/admin/plans" class="nav-link"><i class="bi bi-star-fill"></i> Plans</a></li>
    <li><a href="/admin/payments" class="nav-link"><i class="bi bi-cash-coin"></i> Payments</a></li>
</ul>
@endsection

@section('content')
<div class="table-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-building me-2"></i>Libraries ({{ $libraries->total() }})</span>
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
            <select name="status" class="form-select form-select-sm" style="width:130px">
                <option value="">All Status</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="suspended" {{ request('status')=='suspended'?'selected':'' }}>Suspended</option>
                <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Expired</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary">Filter</button>
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
                        <div class="fw-500">{{ $lib->name }}</div>
                        <small class="text-muted">{{ $lib->email }}</small>
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
                                @csrf <button class="btn btn-sm btn-success">Approve</button>
                            </form>
                            @else
                            <form method="POST" action="/admin/libraries/{{ $lib->id }}/suspend">
                                @csrf <button class="btn btn-sm btn-outline-danger">Suspend</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No libraries found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($libraries->hasPages())
    <div class="p-3">{{ $libraries->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
