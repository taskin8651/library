@extends('layouts.app')
@section('title', 'Shifts')
@section('page-title', 'Shift Management')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/owner-shifts.css') }}?v={{ @filemtime(public_path('assets/css/owner-shifts.css')) }}" rel="stylesheet">
@endpush

@section('content')

@php
    $activeCount = $shifts->where('is_active', true)->count();
    $inactiveCount = $shifts->count() - $activeCount;
    $totalMembers = $shifts->sum('members_count');
@endphp

<!-- Overview stat strip -->
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="shift-stat-tile">
            <div class="sst-value">{{ $shifts->count() }}</div>
            <div class="sst-label"><i class="bi bi-clock-history me-1"></i>Total Shifts</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="shift-stat-tile sst-active">
            <div class="sst-value">{{ $activeCount }}</div>
            <div class="sst-label"><i class="bi bi-check-circle me-1"></i>Active</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="shift-stat-tile sst-inactive">
            <div class="sst-value">{{ $inactiveCount }}</div>
            <div class="sst-label"><i class="bi bi-pause-circle me-1"></i>Inactive</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="shift-stat-tile sst-members">
            <div class="sst-value">{{ $totalMembers }}</div>
            <div class="sst-label"><i class="bi bi-people-fill me-1"></i>Active Members</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Add Shift Form -->
    <div class="col-12 col-lg-4">
        <div class="table-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-plus-square me-2"></i>Add Shift</h6>

            @if($errors->any())
            <div class="alert alert-danger small">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif

            <form method="POST" action="/owner/shifts" id="addShiftForm">
                @csrf
                <div class="form-floating mb-3">
                    <input type="text" id="f_shift_name" name="name" class="form-control" placeholder="e.g. Morning" value="{{ old('name') }}" maxlength="50" required>
                    <label for="f_shift_name">Shift Name *</label>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="time" id="f_start_time" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                            <label for="f_start_time">Start Time *</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="time" id="f_end_time" name="end_time" class="form-control" value="{{ old('end_time') }}" required>
                            <label for="f_end_time">End Time *</label>
                        </div>
                    </div>
                </div>
                <div class="form-floating mb-4">
                    <input type="number" id="f_price" name="price" class="form-control" placeholder="Price" min="0" step="0.01" value="{{ old('price') }}" required>
                    <label for="f_price">Monthly Price (₹) *</label>
                </div>
                <button type="submit" class="btn btn-primary w-100" id="addShiftBtn">
                    <i class="bi bi-plus-lg me-1"></i>Add Shift
                </button>
            </form>

            <div class="shift-hint-box mt-4">
                <i class="bi bi-info-circle me-2"></i>
                Each shift is a daily time window (e.g. Morning 6&ndash;12). The same seat can be sold to different members in non-overlapping shifts &mdash; set them up here, then sell slots from <a href="/owner/seats">Seat Layout</a>.
            </div>
        </div>
    </div>

    <!-- Shift List -->
    <div class="col-12 col-lg-8">
        <div class="table-card">
            <div class="card-header shift-toolbar-wrap">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="fw-700"><i class="bi bi-clock-history me-2"></i>Your Shifts <span class="text-muted fw-500" id="shiftVisibleCount">({{ $shifts->count() }})</span></span>
                    @if($shifts->count() > 0)
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="shiftExportBtn">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
                    </button>
                    @endif
                </div>
                @if($shifts->count() > 0)
                <div class="shift-toolbar">
                    <div class="shift-search-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" id="shiftSearchInput" class="form-control form-control-sm" placeholder="Search shift name..." autocomplete="off">
                    </div>
                    <select id="shiftStatusFilter" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="active">Active Only</option>
                        <option value="inactive">Inactive Only</option>
                    </select>
                </div>
                @endif
            </div>

            @if($shifts->count() === 0)
                <div class="empty-state">
                    <div class="es-icon"><i class="bi bi-clock"></i></div>
                    <h6>No shifts added yet</h6>
                    <p>Add shifts from the left to start selling the same seat across multiple time slots.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 rtable" id="shiftsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Shift</th>
                                <th>Time Window</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Active Members</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shifts as $shift)
                            <tr class="shift-tr" data-name="{{ strtolower($shift->name) }}" data-status="{{ $shift->is_active ? 'active' : 'inactive' }}">
                                <td data-label="Shift">
                                    <div class="shift-name-cell">
                                        <span class="shift-dot {{ $shift->is_active ? '' : 'is-inactive' }}"></span>
                                        <span class="fw-600">{{ $shift->name }}</span>
                                    </div>
                                </td>
                                <td data-label="Time Window">
                                    <span class="shift-time-badge">
                                        <i class="bi bi-clock"></i>
                                        {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                                    </span>
                                </td>
                                <td data-label="Price">
                                    <span class="fw-600">₹{{ number_format($shift->price, 2) }}</span>
                                    <span class="text-muted small">/mo</span>
                                </td>
                                <td data-label="Status">
                                    <span class="badge {{ $shift->is_active ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $shift->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td data-label="Active Members">
                                    <span class="shift-member-chip"><i class="bi bi-people-fill me-1"></i>{{ $shift->members_count }}</span>
                                </td>
                                <td data-label="Actions" class="text-end">
                                    <div class="shift-actions">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editShiftModal{{ $shift->id }}" title="Edit shift">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" action="/owner/shifts/{{ $shift->id }}/toggle" class="d-inline shift-action-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-dark" title="{{ $shift->is_active ? 'Deactivate shift' : 'Activate shift' }}">
                                                <i class="bi bi-power"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="/owner/shifts/{{ $shift->id }}" class="d-inline shift-action-form" data-confirm="Delete shift '{{ $shift->name }}'? This cannot be undone.">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ $shift->members_count > 0 ? 'Has active members — cannot delete' : 'Delete shift' }}" {{ $shift->members_count > 0 ? 'disabled' : '' }}>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="empty-state d-none" id="shiftNoMatch">
                    <div class="es-icon"><i class="bi bi-search"></i></div>
                    <h6>No matching shifts</h6>
                    <p>Try a different search term or status filter.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Shift Modals -->
@foreach($shifts as $shift)
<div class="modal fade" id="editShiftModal{{ $shift->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content seat-modal-content">
            <form method="POST" action="/owner/shifts/{{ $shift->id }}">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold">Edit Shift</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" name="name" class="form-control" value="{{ $shift->name }}" maxlength="50" required>
                        <label>Shift Name *</label>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="time" name="start_time" class="form-control" value="{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}" required>
                                <label>Start Time *</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="time" name="end_time" class="form-control" value="{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}" required>
                                <label>End Time *</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating">
                        <input type="number" name="price" class="form-control" min="0" step="0.01" value="{{ $shift->price }}" required>
                        <label>Monthly Price (₹) *</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script src="{{ asset('assets/js/owner-shifts.js') }}?v={{ @filemtime(public_path('assets/js/owner-shifts.js')) }}"></script>
@endpush
