@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')

@section('sidebar-menu')
<ul class="nav flex-column py-3">
    <li><a href="/admin/dashboard" class="nav-link active"><i class="bi bi-grid-fill"></i> Dashboard</a></li>
    <li class="sidebar-section mt-2">Management</li>
    <li><a href="/admin/libraries" class="nav-link"><i class="bi bi-building"></i> Libraries</a></li>
    <li><a href="/admin/plans" class="nav-link"><i class="bi bi-star-fill"></i> Plans</a></li>
    <li><a href="/admin/payments" class="nav-link"><i class="bi bi-cash-coin"></i> Payments</a></li>
</ul>
@endsection

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-building"></i></div>
            <h3>{{ $total_libraries }}</h3>
            <p>Total Libraries</p>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#dcfce7;color:#166534"><i class="bi bi-check-circle-fill"></i></div>
            <h3>{{ $active_libraries }}</h3>
            <p>Active Libraries</p>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#fef3c7;color:#92400e"><i class="bi bi-clock-fill"></i></div>
            <h3>{{ $pending_libraries }}</h3>
            <p>Pending Approval</p>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-box" style="background:#dcfce7;color:#166534"><i class="bi bi-currency-rupee"></i></div>
            <h3>₹{{ number_format($total_revenue) }}</h3>
            <p>Total Revenue</p>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Recent Libraries -->
    <div class="col-lg-8">
        <div class="table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-building me-2"></i>Recent Libraries</span>
                <a href="/admin/libraries" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Library</th><th>Plan</th><th>Status</th><th>Expires</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recent_libraries as $lib)
                        <tr>
                            <td>
                                <div class="fw-500">{{ $lib->name }}</div>
                                <small class="text-muted">{{ $lib->email }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $lib->plan->name }}</span></td>
                            <td>
                                @if($lib->status == 'active') <span class="badge badge-active">Active</span>
                                @elseif($lib->status == 'pending') <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($lib->status == 'suspended') <span class="badge badge-inactive">Suspended</span>
                                @else <span class="badge badge-expired">Expired</span>
                                @endif
                            </td>
                            <td>{{ $lib->plan_expires_at ? $lib->plan_expires_at->format('d M Y') : '-' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($lib->status != 'active')
                                    <form method="POST" action="/admin/libraries/{{ $lib->id }}/approve">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    @else
                                    <form method="POST" action="/admin/libraries/{{ $lib->id }}/suspend">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger">Suspend</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No libraries registered yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Plan Stats -->
    <div class="col-lg-4">
        <div class="table-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2"></i>Plan Distribution</h6>
            @foreach($plans as $plan)
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="fw-500">{{ $plan->name }}</div>
                    <small class="text-muted">₹{{ number_format($plan->price) }}/mo</small>
                </div>
                <div class="text-end">
                    <div class="fw-bold">{{ $plan->libraries_count }}</div>
                    <small class="text-muted">libraries</small>
                </div>
            </div>
            @endforeach
            <hr>
            <a href="/admin/plans" class="btn btn-outline-primary w-100 btn-sm">Manage Plans</a>
        </div>
    </div>
</div>
@endsection
