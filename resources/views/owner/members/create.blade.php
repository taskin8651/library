@extends('layouts.app')
@section('title', 'Add Member')
@section('page-title', 'Add New Member')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $e)<div><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="/owner/members">
            @csrf
            <div class="table-card p-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">Personal Information</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" id="f_name" name="name" class="form-control" placeholder="Full Name" value="{{ old('name') }}" required>
                            <label for="f_name">Full Name *</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" id="f_email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required>
                            <label for="f_email">Email *</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="tel" id="f_phone" name="phone" class="form-control" placeholder="Phone" value="{{ old('phone') }}" maxlength="10" required>
                            <label for="f_phone">Phone *</label>
                        </div>
                        <div class="form-text">Default password will be phone number</div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" id="f_dob" name="dob" class="form-control" placeholder="Date of Birth" value="{{ old('dob') }}">
                            <label for="f_dob">Date of Birth</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <textarea id="f_address" name="address" class="form-control" placeholder="Address" style="height:80px">{{ old('address') }}</textarea>
                            <label for="f_address">Address</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" id="f_aadhar" name="aadhar" class="form-control" placeholder="Aadhar Number" value="{{ old('aadhar') }}" maxlength="12">
                            <label for="f_aadhar">Aadhar Number</label>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3 border-bottom pb-2">Seat & Shift Assignment</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="f_seat" name="seat_id" class="form-select">
                                <option value="">-- No Seat Assigned --</option>
                                @foreach($seats as $seat)
                                <option value="{{ $seat->id }}" {{ old('seat_id')==$seat->id?'selected':'' }}
                                    {{ $seat->isOccupied()?'disabled':'' }}>
                                    {{ $seat->seat_number }} ({{ $seat->type }})
                                    {{ $seat->isOccupied()?'[Occupied]':'' }}
                                </option>
                                @endforeach
                            </select>
                            <label for="f_seat">Seat</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="f_shift" name="shift_id" class="form-select">
                                <option value="">-- No Shift --</option>
                                @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id')==$shift->id?'selected':'' }}>
                                    {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }}) - ₹{{ $shift->price }}/mo
                                </option>
                                @endforeach
                            </select>
                            <label for="f_shift">Shift</label>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3 border-bottom pb-2">Plan Duration</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" id="f_plan_start" name="plan_start_date" class="form-control" placeholder="Plan Start Date" value="{{ old('plan_start_date', date('Y-m-d')) }}" required>
                            <label for="f_plan_start">Plan Start Date *</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" id="f_plan_end" name="plan_end_date" class="form-control" placeholder="Plan End Date" value="{{ old('plan_end_date', date('Y-m-d', strtotime('+1 month'))) }}" required>
                            <label for="f_plan_end">Plan End Date *</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-person-plus me-2"></i>Add Member
                    </button>
                    <a href="/owner/members" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
