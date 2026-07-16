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

<!-- Overview stat strip -->
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="seat-stat-tile">
            <div class="sst-value">{{ $stats['total'] }}</div>
            <div class="sst-label"><i class="bi bi-grid-3x3-gap me-1"></i>Total Seats</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="seat-stat-tile sst-free">
            <div class="sst-value">{{ $stats['free'] }}</div>
            <div class="sst-label"><i class="bi bi-check-circle me-1"></i>Fully Free</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="seat-stat-tile sst-partial">
            <div class="sst-value">{{ $stats['partial'] }}</div>
            <div class="sst-label"><i class="bi bi-pie-chart me-1"></i>Partially Sold</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="seat-stat-tile sst-full">
            <div class="sst-value">{{ $stats['full'] }}</div>
            <div class="sst-label"><i class="bi bi-lock me-1"></i>Fully Sold</div>
        </div>
    </div>
</div>

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

            @if($shifts->count() === 0)
            <div class="seat-hint-box mt-4">
                <i class="bi bi-info-circle me-2"></i>
                No shifts configured yet — seats will be sold on a full-day basis. Add shifts to sell the same seat in multiple time slots.
                <a href="/owner/shifts" class="d-block mt-1 fw-600">Manage Shifts &rarr;</a>
            </div>
            @endif

            <!-- Legend -->
            <div class="mt-4 border-top pt-3">
                <p class="small fw-500 mb-2">Legend:</p>
                <div class="d-flex gap-3 flex-wrap">
                    <span class="seat-legend-item"><span class="seat-legend-dot seat-dot-free"></span>Free</span>
                    <span class="seat-legend-item"><span class="seat-legend-dot seat-dot-partial"></span>Partially Sold</span>
                    <span class="seat-legend-item"><span class="seat-legend-dot seat-dot-full"></span>Fully Sold</span>
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
                            $d = $seatData[$seat->id];
                            if ($d['blocked']) {
                                $stateClass = 'seat-' . ($d['blocked_reason'] ?: 'inactive');
                                $stateLabel = ucfirst($d['blocked_reason'] ?: 'Inactive');
                                $stateIcon  = $d['blocked_reason'] === 'maintenance' ? 'bi-tools' : ($d['blocked_reason'] === 'reserved' ? 'bi-bookmark-fill' : 'bi-slash-circle');
                            } elseif ($d['total_shifts'] == 0) {
                                $stateClass = $d['full_day_taken'] ? 'seat-full' : 'seat-free';
                                $stateLabel = $d['full_day_taken'] ? 'Sold' : 'Free';
                                $stateIcon  = $d['full_day_taken'] ? 'bi-person-fill' : 'bi-person';
                            } elseif ($d['booked_count'] == 0) {
                                $stateClass = 'seat-free'; $stateLabel = 'Free'; $stateIcon = 'bi-person';
                            } elseif ($d['booked_count'] >= $d['total_shifts']) {
                                $stateClass = 'seat-full'; $stateLabel = 'Fully Sold'; $stateIcon = 'bi-person-fill';
                            } else {
                                $stateClass = 'seat-partial'; $stateLabel = $d['booked_count'] . '/' . $d['total_shifts'] . ' shifts sold'; $stateIcon = 'bi-person-half';
                            }
                        @endphp
                        <div class="seat-desk {{ $stateClass }}" data-seat-number="{{ strtolower($seat->seat_number) }}"
                            data-seat-id="{{ $seat->id }}" data-bs-toggle="modal" data-bs-target="#seatDetailModal"
                            title="{{ $seat->seat_number }} - {{ $stateLabel }}">
                            <div class="seat-desk-top"></div>
                            <i class="bi {{ $stateIcon }} seat-desk-icon"></i>
                            <span class="seat-desk-label">{{ $seat->seat_number }}</span>
                            @if($seat->type !== 'regular')
                            <span class="seat-type-chip">{{ ucfirst($seat->type) }}</span>
                            @endif
                            @if(!$d['blocked'] && $d['total_shifts'] > 0)
                            <span class="seat-fill-track">
                                <span class="seat-fill-bar" style="width: {{ round($d['booked_count'] / $d['total_shifts'] * 100) }}%"></span>
                            </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<!-- Shared Seat Detail Modal -->
<div class="modal fade" id="seatDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content seat-modal-content">
      <div class="modal-header border-0 pb-0">
        <div>
          <h5 class="modal-title fw-bold mb-0" id="seatModalTitle">Seat</h5>
          <div class="text-muted small" id="seatModalSubtitle"></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-2">
        <div id="seatModalBanner" class="seat-modal-banner d-none"></div>
        <div id="seatModalSlots" class="seat-slot-list"></div>
      </div>
      <div class="modal-footer border-0 pt-0 flex-wrap gap-2" id="seatModalFooter"></div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
    window.SEAT_DATA = @json($seatData);
    window.SHIFT_LIST = @json($shiftList);
    window.SEAT_ROUTES = {
        toggle:       '/owner/seats/__ID__/toggle',
        status:       '/owner/seats/__ID__/status',
        destroy:      '/owner/seats/__ID__',
        memberCreate: '/owner/members/create',
        memberShow:   '/owner/members/__ID__',
    };
    window.CSRF_TOKEN = '{{ csrf_token() }}';
</script>
<script src="{{ asset('assets/js/owner-seats.js') }}"></script>
@endpush
