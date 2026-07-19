@php
    // Read-only count for the sidebar badge — mirrors PaymentController@index's
    // "awaiting verification" query so every admin page (not just /admin/payments)
    // can show a live pending-payments badge without each page passing it down.
    $sidebarPendingPayments = \App\Models\Subscription::where('status', 'pending')->whereNotNull('utr')->count();
@endphp
<ul class="nav flex-column py-3">
    <li><a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}"><i class="bi bi-grid-fill"></i> Dashboard</a></li>

    <li class="sidebar-section mt-2">Management</li>
    <li><a href="/admin/libraries" class="nav-link {{ request()->is('admin/libraries*') ? 'active' : '' }}"><i class="bi bi-building"></i> Libraries</a></li>
    <li><a href="/admin/plans" class="nav-link {{ request()->is('admin/plans*') ? 'active' : '' }}"><i class="bi bi-star-fill"></i> Plans</a></li>
    <li>
        <a href="/admin/payments" class="nav-link {{ request()->is('admin/payments*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i> Payments
            @if($sidebarPendingPayments)
                <span class="badge bg-danger ms-auto">{{ $sidebarPendingPayments }}</span>
            @endif
        </a>
    </li>
</ul>
