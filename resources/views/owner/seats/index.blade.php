@extends('layouts.app')
@section('title', 'Seat Layout')
@section('page-title', 'Seat Layout')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/owner-seats.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="row g-3">
    <!-- Add Seats Form -->
    <div class="col-lg-4">
        <div class="table-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-plus-square me-2"></i>Add Seats</h6>
            <form method="POST" action="/owner/seats">
                @csrf
                <div class="form-floating mb-3">
                    <input type="text" id="f_row_label" name="row_label" class="form-control" placeholder="A, B, C..." maxlength="5" required>
                    <label for="f_row_label">Row Label *</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" id="f_seat_count" name="seat_count" class="form-control" placeholder="Number of Seats" value="5" min="1" max="50" required>
                    <label for="f_seat_count">Number of Seats *</label>
                </div>
                <div class="form-floating mb-4">
                    <select id="f_seat_type" name="type" class="form-select">
                        <option value="regular">Regular</option>
                        <option value="cabin">Cabin</option>
                        <option value="vip">VIP</option>
                    </select>
                    <label for="f_seat_type">Seat Type</label>
                </div>
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Add Seats</button>
            </form>

            <!-- Legend -->
            <div class="mt-4 border-top pt-3">
                <p class="small fw-500 mb-2">Legend:</p>
                <div class="d-flex gap-3 flex-wrap">
                    <span class="seat-legend-item"><span class="seat-legend-dot seat-dot-available"></span>Available</span>
                    <span class="seat-legend-item"><span class="seat-legend-dot seat-dot-occupied"></span>Occupied</span>
                    <span class="seat-legend-item"><span class="seat-legend-dot seat-dot-reserved"></span>Reserved</span>
                    <span class="seat-legend-item"><span class="seat-legend-dot seat-dot-maintenance"></span>Maintenance</span>
                    <span class="seat-legend-item"><span class="seat-legend-dot seat-dot-inactive"></span>Inactive</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Seat Grid -->
    <div class="col-lg-8">
        <div class="table-card p-4">
            <div class="section-head mb-3">
                <h6 class="mb-0"><i class="bi bi-grid me-2"></i>Seat Map <span class="text-muted fw-500">({{ $seats->count() }} total)</span></h6>
                @if($seats->count() > 0)
                <div class="member-search-wrap" style="max-width:220px">
                    <i class="bi bi-search"></i>
                    <input type="text" id="seatSearch" class="form-control form-control-sm" placeholder="Find seat number...">
                </div>
                @endif
            </div>

            @if($seats->count() == 0)
                <div class="empty-state">
                    <div class="es-icon"><i class="bi bi-grid-3x3-gap"></i></div>
                    <h6>No seats added yet</h6>
                    <p>Add seats from the form on the left to build your seat map.</p>
                </div>
            @else
                @foreach($rows as $row => $rowSeats)
                <div class="mb-4 seat-row-group">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-dark">Row {{ $row }}</span>
                        <small class="text-muted">{{ $rowSeats->count() }} seats</small>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($rowSeats as $seat)
                        @php
                            $occupied = $seat->isOccupied();
                            if (!$seat->is_active) {
                                $stateClass = 'seat-inactive'; $stateLabel = 'Inactive'; $stateIcon = 'bi-person';
                            } elseif ($occupied) {
                                $stateClass = 'seat-occupied'; $stateLabel = 'Occupied'; $stateIcon = 'bi-person-fill';
                            } elseif ($seat->status === 'maintenance') {
                                $stateClass = 'seat-maintenance'; $stateLabel = 'Maintenance'; $stateIcon = 'bi-tools';
                            } elseif ($seat->status === 'reserved') {
                                $stateClass = 'seat-reserved'; $stateLabel = 'Reserved'; $stateIcon = 'bi-bookmark-fill';
                            } else {
                                $stateClass = 'seat-available'; $stateLabel = 'Available'; $stateIcon = 'bi-person';
                            }
                        @endphp
                        <div class="seat-desk {{ $stateClass }}" data-seat-number="{{ strtolower($seat->seat_number) }}"
                            title="{{ $seat->seat_number }} - {{ $stateLabel }}">
                            <div class="seat-desk-top"></div>
                            <i class="bi {{ $stateIcon }} seat-desk-icon"></i>
                            <span class="seat-desk-label">{{ $seat->seat_number }}</span>
                            @if($seat->type !== 'regular')
                            <span class="seat-type-chip">{{ ucfirst($seat->type) }}</span>
                            @endif

                            <!-- Actions Dropdown -->
                            <div class="seat-desk-menu">
                                <div class="dropdown">
                                    <button class="btn p-0 border-0 bg-transparent" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if(!$occupied && $seat->is_active)
                                            @if($seat->status !== 'available')
                                            <li>
                                                <form method="POST" action="/owner/seats/{{ $seat->id }}/status">
                                                    @csrf <input type="hidden" name="status" value="available">
                                                    <button class="dropdown-item small"><i class="bi bi-check-circle me-1"></i>Mark Available</button>
                                                </form>
                                            </li>
                                            @endif
                                            @if($seat->status !== 'reserved')
                                            <li>
                                                <form method="POST" action="/owner/seats/{{ $seat->id }}/status">
                                                    @csrf <input type="hidden" name="status" value="reserved">
                                                    <button class="dropdown-item small"><i class="bi bi-bookmark me-1"></i>Mark Reserved</button>
                                                </form>
                                            </li>
                                            @endif
                                            @if($seat->status !== 'maintenance')
                                            <li>
                                                <form method="POST" action="/owner/seats/{{ $seat->id }}/status">
                                                    @csrf <input type="hidden" name="status" value="maintenance">
                                                    <button class="dropdown-item small"><i class="bi bi-tools me-1"></i>Mark Maintenance</button>
                                                </form>
                                            </li>
                                            @endif
                                            <li><hr class="dropdown-divider my-1"></li>
                                        @endif
                                        <li>
                                            <form method="POST" action="/owner/seats/{{ $seat->id }}/toggle">
                                                @csrf
                                                <button class="dropdown-item small">
                                                    {{ $seat->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </li>
                                        @if(!$occupied)
                                        <li>
                                            <form method="POST" action="/owner/seats/{{ $seat->id }}" data-confirm="Delete seat {{ $seat->seat_number }}? This cannot be undone.">
                                                @csrf @method('DELETE')
                                                <button class="dropdown-item small text-danger">Delete</button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/owner-seats.js') }}"></script>
@endpush
