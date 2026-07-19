<ul class="nav flex-column py-3">
    <li><a href="/owner/dashboard" class="nav-link {{ request()->is('owner/dashboard') ? 'active' : '' }}"><i class="bi bi-grid-fill"></i> Dashboard</a></li>

    <li class="sidebar-section mt-2">Members</li>
    <li><a href="/owner/members" class="nav-link {{ request()->is('owner/members') ? 'active' : '' }}"><i class="bi bi-people-fill"></i> All Members</a></li>
    <li><a href="/owner/members/create" class="nav-link {{ request()->is('owner/members/create') ? 'active' : '' }}"><i class="bi bi-person-plus-fill"></i> Add Member</a></li>

    <li class="sidebar-section mt-2">Finance</li>
    <li><a href="/owner/fees" class="nav-link {{ request()->is('owner/fees') ? 'active' : '' }}"><i class="bi bi-cash-stack"></i> Fee Payments</a></li>
    <li><a href="/owner/fees/collect" class="nav-link {{ request()->is('owner/fees/collect') ? 'active' : '' }}"><i class="bi bi-plus-circle-fill"></i> Collect Fee</a></li>

    <li class="sidebar-section mt-2">Operations</li>
    <li><a href="/owner/attendance" class="nav-link {{ request()->is('owner/attendance') ? 'active' : '' }}"><i class="bi bi-calendar-check-fill"></i> Attendance</a></li>
    <li><a href="/owner/attendance/qr" class="nav-link {{ request()->is('owner/attendance/qr') ? 'active' : '' }}"><i class="bi bi-qr-code-scan"></i> QR Code</a></li>
    <li><a href="/owner/seats" class="nav-link {{ request()->is('owner/seats') ? 'active' : '' }}"><i class="bi bi-grid-3x3-gap-fill"></i> Seat Layout</a></li>
    <li><a href="/owner/shifts" class="nav-link {{ request()->is('owner/shifts') ? 'active' : '' }}"><i class="bi bi-clock-fill"></i> Shifts</a></li>

    <li class="sidebar-section mt-2">Communication</li>
    <li><a href="/owner/announcements" class="nav-link {{ request()->is('owner/announcements*') ? 'active' : '' }}"><i class="bi bi-megaphone-fill"></i> Announcements</a></li>

    <li class="sidebar-section mt-2">Reports</li>
    <li><a href="/owner/reports" class="nav-link {{ request()->is('owner/reports*') ? 'active' : '' }}"><i class="bi bi-file-earmark-bar-graph-fill"></i> Reports & Export</a></li>

    <li class="sidebar-section mt-2">Account</li>
    <li><a href="/owner/settings" class="nav-link {{ request()->is('owner/settings') ? 'active' : '' }}"><i class="bi bi-gear-fill"></i> Library Settings</a></li>
    <li><a href="/owner/profile" class="nav-link {{ request()->is('owner/profile') ? 'active' : '' }}"><i class="bi bi-person-circle"></i> My Profile</a></li>
    <li><a href="/owner/subscription/plans" class="nav-link {{ request()->is('owner/subscription*') ? 'active' : '' }}"><i class="bi bi-star-fill"></i> Subscription</a></li>
</ul>
