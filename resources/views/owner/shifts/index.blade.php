@extends('layouts.app')
@section('title', 'Shifts')
@section('page-title', 'Shifts')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/owner-seats.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="row g-3">
    <!-- Add Shift Form -->
    <div class="col-lg-4">
        <div class="table-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-plus-square me-2"></i>Add Shift</h6>
            <form method="POST" action="/owner/shifts">
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
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Add Shift</button>
            </form>

            <div class="seat-hint-box mt-4">
                <i class="bi bi-info-circle me-2"></i>
                Each shift is a daily time window (e.g. Morning 6–12). The same seat can be sold to different members in non-overlapping shifts — set them up here, then sell slots from <a href="/owner/seats">Seat Layout</a>.
            </div>
        </div>
    </div>

    <!-- Shift List -->
    <div class="col-lg-8">
        <div class="table-card p-4">
            <h6 class="mb-3"><i class="bi bi-clock-history me-2"></i>Your Shifts <span class="text-muted fw-500">({{ $shifts->count() }})</span></h6>

            @if($shifts->count() === 0)
                <div class="empty-state">
                    <div class="es-icon"><i class="bi bi-clock"></i></div>
                    <h6>No shifts added yet</h6>
                    <p>Add shifts from the left to start selling the same seat across multiple time slots.</p>
                </div>
            @else
                <div class="d-flex flex-column gap-2">
                    @foreach($shifts as $shift)
                    <div class="shift-row {{ !$shift->is_active ? 'shift-row-inactive' : '' }}">
                        <div class="sr-time-badge">
                            <i class="bi bi-clock"></i>
                            {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                        </div>
                        <div class="sr-main">
                            <div class="sr-name">{{ $shift->name }} @if(!$shift->is_active)<span class="badge bg-secondary ms-1">Inactive</span>@endif</div>
                            <div class="sr-meta">₹{{ number_format($shift->price, 2) }}/mo &middot; {{ $shift->members_count }} active member{{ $shift->members_count == 1 ? '' : 's' }}</div>
                        </div>
                        <div class="sr-actions">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editShiftModal{{ $shift->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="/owner/shifts/{{ $shift->id }}/toggle" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-power"></i>
                                </button>
                            </form>
                            <form method="POST" action="/owner/shifts/{{ $shift->id }}" class="d-inline" data-confirm="Delete shift '{{ $shift->name }}'? This cannot be undone.">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" {{ $shift->members_count > 0 ? 'disabled title="Has active members"' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Edit Shift Modal -->
                    <div class="modal fade" id="editShiftModal{{ $shift->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content seat-modal-content">
                                <form method="POST" action="/owner/shifts/{{ $shift->id }}">
                                    @csrf @method('PUT')
                                    <div class="modal-header border-0 pb-0">
                                        <h6 class="modal-title fw-bold">Edit Shift</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
