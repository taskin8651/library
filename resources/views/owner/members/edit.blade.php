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
                            <option value="{{ $seat->id }}" {{ $member->seat_id == $seat->id ? 'selected' : '' }}>{{ $seat->seat_number }} ({{ $seat->type }})</option>
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
                            <option value="{{ $shift->id }}" {{ $member->shift_id == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                            @endforeach
                        </select>
                        <label for="f_shift">Shift</label>
                    </div>
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
@endsection
