@extends('layouts.app')
@section('title', 'Members')
@section('page-title', 'Members')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/owner-members-card.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="member-toolbar member-toolbar-glass table-card p-3 mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="fw-700"><i class="bi bi-people me-2"></i>All Members <span class="text-muted fw-500" id="memberVisibleCount">({{ $members->total() }})</span></span>
        <div class="d-flex gap-2 flex-wrap">
            <form method="GET" class="d-flex gap-2">
                <div class="member-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" id="memberLiveSearch" class="form-control form-control-sm" placeholder="Search name/phone/UID..." value="{{ request('search') }}" autocomplete="off">
                </div>
                <select name="status" class="form-select form-select-sm" style="width:130px">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                    <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
                    <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Expired</option>
                </select>
                <button class="btn btn-sm btn-outline-secondary">Filter</button>
            </form>
            <a href="/owner/members/create" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Member</a>
        </div>
    </div>
    <div class="text-muted small mt-2 d-none" id="memberSearchHint"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Instant filter on this page &mdash; press "Filter" to search all members.</div>
</div>

<div id="memberGrid">
@forelse($members as $member)
@if($loop->first)
<div class="row g-3">
@endif

    <div class="col-md-6 col-lg-4 member-grid-item" data-name="{{ strtolower($member->user->name) }}" data-phone="{{ $member->user->phone }}" data-uid="{{ strtolower($member->uid) }}">
        <div class="member-card card-stat">
            <div class="member-card-accent member-accent-{{ $member->status }}"></div>

            <div class="member-card-top">
                <div class="d-flex align-items-center gap-3">
                    <div class="member-avatar">{{ substr($member->user->name, 0, 1) }}</div>
                    <div>
                        <div class="member-card-name">{{ $member->user->name }}</div>
                        <div class="member-card-phone"><i class="bi bi-telephone-fill me-1"></i>{{ $member->user->phone }}</div>
                    </div>
                </div>
                <span class="badge badge-{{ $member->status }}">{{ ucfirst($member->status) }}</span>
            </div>

            <div class="member-uid-chip">
                <i class="bi bi-upc-scan"></i>{{ $member->uid }}
            </div>

            <div class="member-info-row">
                <span class="member-info-chip"><i class="bi bi-grid-fill"></i>{{ $member->seat?->seat_number ?? 'No seat' }}</span>
                <span class="member-info-chip"><i class="bi bi-clock-fill"></i>{{ $member->shift?->name ?? 'No shift' }}</span>
            </div>

            @if($member->plan_end_date)
            @php
                $totalDays = $member->plan_start_date ? max(1, $member->plan_start_date->diffInDays($member->plan_end_date)) : 30;
                $daysLeft = $member->daysLeft();
                $pct = max(0, min(100, ($daysLeft / $totalDays) * 100));
                $barClass = $daysLeft <= 3 ? 'danger' : ($daysLeft <= 7 ? 'warn' : '');
            @endphp
            <div class="mb-2">
                <div class="mini-progress {{ $barClass }}"><span style="width:{{ $pct }}%"></span></div>
            </div>
            @endif

            <div class="member-expiry-row">
                <span>
                    <i class="bi bi-calendar-event me-1"></i>
                    @if($member->plan_end_date)
                        Expires {{ $member->plan_end_date->format('d M Y') }}
                    @else
                        No plan date
                    @endif
                </span>
                @if($member->plan_end_date && $member->daysLeft() <= 7)
                    <span class="badge bg-warning text-dark">{{ $member->daysLeft() }}d left</span>
                @endif
            </div>

            <div class="member-card-actions">
                <a href="/owner/members/{{ $member->id }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                <a href="/owner/members/{{ $member->id }}/edit" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                <a href="/owner/fees/collect?member_id={{ $member->id }}" class="btn btn-sm btn-outline-success" title="Collect Fee"><i class="bi bi-cash"></i></a>
            </div>
        </div>
    </div>

@if($loop->last)
</div>
@endif
@empty
<div class="table-card">
    <div class="empty-state">
        <div class="es-icon"><i class="bi bi-people"></i></div>
        <h6>No members yet</h6>
        <p>Add your first student to start tracking seats, fees and attendance.</p>
        <a href="/owner/members/create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Member</a>
    </div>
</div>
@endforelse
</div>

<div class="table-card d-none" id="memberNoLocalMatch">
    <div class="empty-state">
        <div class="es-icon"><i class="bi bi-search"></i></div>
        <h6>No matches on this page</h6>
        <p>Try the "Filter" button to search across all members.</p>
    </div>
</div>

@if($members->hasPages())
<div class="mt-3" id="memberPagination">{{ $members->withQueryString()->links() }}</div>
@endif

@endsection

@push('scripts')
<script src="{{ asset('assets/js/owner-members-search.js') }}"></script>
@endpush
