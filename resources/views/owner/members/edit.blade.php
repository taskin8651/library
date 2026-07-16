@extends('layouts.app')
@section('title', 'Edit Member')
@section('page-title', 'Edit Member')
@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection
@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
    @if($errors->any())
    <div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif
    <form method="POST" action="/owner/members/{{ $member->id }}">
        @csrf @method('PUT')
        <div class="table-card p-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Edit Member: {{ $member->user->name }}</h6>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" id="f_name" name="name" class="form-control" placeholder="Full Name" value="{{ old('name', $member->user->name) }}" required>
                        <label for="f_name">Full Name *</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="tel" id="f_phone" name="phone" class="form-control" placeholder="Phone" value="{{ old('phone', $member->user->phone) }}" required>
                        <label for="f_phone">Phone *</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="f_seat" name="seat_id" class="form-select">
                            <option value="">-- No Seat --</option>
                            @foreach($seats as $seat)
                            <option value="{{ $seat->id }}" data-base-label="{{ $seat->seat_number }} ({{ $seat->type }})"
                                {{ old('seat_id', $member->seat_id) == $seat->id ? 'selected' : '' }}>{{ $seat->seat_number }} ({{ $seat->type }})</option>
                            @endforeach
                        </select>
                        <label for="f_seat">Seat</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="f_shift" name="shift_id" class="form-select">
                            <option value="">-- No Shift (Full Day) --</option>
                            @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ old('shift_id', $member->shift_id) == $shift->id ? 'selected' : '' }}>{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                            @endforeach
                        </select>
                        <label for="f_shift">Shift</label>
                    </div>
                </div>
                <div class="col-12">
                    <div id="seatAvailabilityHint" class="form-text"></div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="f_status" name="status" class="form-select">
                            <option value="active" {{ $member->status=='active'?'selected':'' }}>Active</option>
                            <option value="inactive" {{ $member->status=='inactive'?'selected':'' }}>Inactive</option>
                            <option value="expired" {{ $member->status=='expired'?'selected':'' }}>Expired</option>
                        </select>
                        <label for="f_status">Status</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="date" id="f_plan_end" name="plan_end_date" class="form-control" placeholder="Plan End Date" value="{{ old('plan_end_date', $member->plan_end_date?->format('Y-m-d')) }}" required>
                        <label for="f_plan_end">Plan End Date *</label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-floating">
                        <textarea id="f_address" name="address" class="form-control" placeholder="Address" style="height:80px">{{ old('address', $member->address) }}</textarea>
                        <label for="f_address">Address</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                <a href="/owner/members/{{ $member->id }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>

<script>
(function () {
    const SEAT_AVAIL = @json($seatAvailability);
    const seatSelect = document.getElementById('f_seat');
    const shiftSelect = document.getElementById('f_shift');
    const hint = document.getElementById('seatAvailabilityHint');

    function refresh() {
        const shiftId = shiftSelect.value;
        let selectedConflict = null;

        Array.from(seatSelect.options).forEach(opt => {
            if (!opt.value) return;
            const info = SEAT_AVAIL[opt.value];
            if (!info) return;

            let note = null;
            if (info.blocked) {
                note = 'Unavailable (' + (info.blocked_reason || 'inactive') + ')';
            } else if (info.full_day_taken) {
                note = 'Booked full-day by ' + info.full_day_taken.name;
            } else if (shiftId && info.shifts[shiftId]) {
                note = 'Booked by ' + info.shifts[shiftId].name + ' for this shift';
            } else if (!shiftId && Object.values(info.shifts).some(s => s)) {
                note = 'Has shift bookings — pick a specific shift';
            }

            opt.disabled = !!note;
            opt.textContent = opt.dataset.baseLabel + (note ? ' — ' + note : '');
            if (opt.selected && note) selectedConflict = note;
        });

        hint.textContent = selectedConflict ? ('This seat is not available: ' + selectedConflict) : '';
        hint.classList.toggle('text-danger', !!selectedConflict);
    }

    seatSelect.addEventListener('change', refresh);
    shiftSelect.addEventListener('change', refresh);
    refresh();
})();
</script>
@endsection
