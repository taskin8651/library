@extends('layouts.app')
@section('title', 'Collect Fee')
@section('page-title', 'Collect Fee Payment')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
        @endif

        <form method="POST" action="/owner/fees">
            @csrf
            <div class="table-card p-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-cash me-2"></i>Payment Details</h6>

                <div class="form-floating mb-3">
                    <select id="f_member" name="member_id" class="form-select" required>
                        <option value="">-- Choose Member --</option>
                        @foreach($members as $m)
                        <option value="{{ $m->id }}" {{ (old('member_id') == $m->id || request('member_id') == $m->id) ? 'selected' : '' }}>
                            {{ $m->user->name }} - {{ $m->uid }} ({{ $m->user->phone }})
                        </option>
                        @endforeach
                    </select>
                    <label for="f_member">Select Member *</label>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" id="f_amount" name="amount" class="form-control" placeholder="Amount" value="{{ old('amount') }}" min="1" required>
                            <label for="f_amount">Amount (₹) *</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="f_mode" name="payment_mode" class="form-select" required>
                                <option value="cash" {{ old('payment_mode','cash')=='cash'?'selected':'' }}>Cash</option>
                                <option value="upi" {{ old('payment_mode')=='upi'?'selected':'' }}>UPI</option>
                                <option value="bank" {{ old('payment_mode')=='bank'?'selected':'' }}>Bank Transfer</option>
                                <option value="other" {{ old('payment_mode')=='other'?'selected':'' }}>Other</option>
                            </select>
                            <label for="f_mode">Payment Mode *</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3" id="upi_ref_div" style="display:none">
                    <div class="form-floating">
                        <input type="text" id="f_upi_ref" name="upi_ref" class="form-control" placeholder="UPI Reference" value="{{ old('upi_ref') }}">
                        <label for="f_upi_ref">UPI Reference / Transaction ID</label>
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <input type="date" id="f_payment_date" name="payment_date" class="form-control" placeholder="Payment Date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                    <label for="f_payment_date">Payment Date *</label>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" id="f_valid_from" name="valid_from" class="form-control" placeholder="Valid From" value="{{ old('valid_from', date('Y-m-d')) }}" required>
                            <label for="f_valid_from">Valid From *</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" id="f_valid_till" name="valid_till" class="form-control" placeholder="Valid Till" value="{{ old('valid_till', date('Y-m-d', strtotime('+1 month'))) }}" required>
                            <label for="f_valid_till">Valid Till *</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mb-4">
                    <textarea id="f_notes" name="notes" class="form-control" placeholder="Notes" style="height:80px">{{ old('notes') }}</textarea>
                    <label for="f_notes">Notes (optional)</label>
                </div>

                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-check-circle me-2"></i>Record Payment & Generate Receipt
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/owner-fees-create.js') }}"></script>
@endpush
