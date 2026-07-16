@extends('layouts.app')
@section('title', 'Receipt')
@section('page-title', 'Fee Receipt')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="d-flex justify-content-end gap-2 mb-3">
            <a href="/owner/fees/{{ $payment->id }}/download" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i>Print
            </button>
            <a href="/owner/fees/collect" class="btn btn-primary">
                <i class="bi bi-plus me-1"></i>New Payment
            </a>
        </div>

        <!-- Receipt Card -->
        <div class="table-card p-4" id="receipt-print">
            <!-- Header -->
            <div class="text-center border-bottom pb-3 mb-3">
                @if($library->logo)
                    <img src="{{ $library->logo_url }}" height="60" class="mb-2">
                @endif
                <h5 class="fw-bold mb-0">{{ $library->name }}</h5>
                @if($library->tagline)
                    <p class="text-muted small mb-0">{{ $library->tagline }}</p>
                @endif
                @if($library->address)
                    <p class="text-muted small mb-0">{{ $library->address }}</p>
                @endif
                <div class="mt-2">
                    <span class="badge bg-success fs-6 px-3 py-2">FEE RECEIPT</span>
                </div>
            </div>

            <!-- Receipt Info -->
            <div class="row mb-3">
                <div class="col-6">
                    <small class="text-muted">Receipt No.</small>
                    <div class="fw-bold">{{ $payment->receipt_number }}</div>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted">Date</small>
                    <div class="fw-bold">{{ $payment->payment_date->format('d M Y') }}</div>
                </div>
            </div>

            <!-- Member Info -->
            <div class="bg-light rounded-3 p-3 mb-3">
                <div class="row">
                    <div class="col-8">
                        <small class="text-muted d-block">Member Name</small>
                        <div class="fw-bold">{{ $payment->member->user->name }}</div>
                        <small class="text-muted">UID: {{ $payment->member->uid }}</small>
                    </div>
                    <div class="col-4 text-end">
                        <small class="text-muted d-block">Seat</small>
                        <div class="fw-bold">{{ $payment->member->seat?->seat_number ?? '-' }}</div>
                        <small class="text-muted">{{ $payment->member->shift?->name ?? '-' }}</small>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <table class="table table-borderless mb-3">
                <tr>
                    <td class="text-muted ps-0">Amount Paid</td>
                    <td class="text-end fw-bold text-success fs-5 pe-0">₹{{ number_format($payment->amount) }}</td>
                </tr>
                <tr>
                    <td class="text-muted ps-0">Payment Mode</td>
                    <td class="text-end pe-0 text-uppercase fw-500">{{ $payment->payment_mode }}</td>
                </tr>
                @if($payment->upi_ref)
                <tr>
                    <td class="text-muted ps-0">UPI Ref</td>
                    <td class="text-end pe-0">{{ $payment->upi_ref }}</td>
                </tr>
                @endif
                <tr>
                    <td class="text-muted ps-0">Valid From</td>
                    <td class="text-end pe-0">{{ $payment->valid_from->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="text-muted ps-0">Valid Till</td>
                    <td class="text-end pe-0 fw-bold text-primary">{{ $payment->valid_till->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="text-muted ps-0">Collected By</td>
                    <td class="text-end pe-0">{{ $payment->collected_by }}</td>
                </tr>
            </table>

            <!-- Footer -->
            <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                @if($library->stamp)
                    <img src="{{ asset('storage/'.$library->stamp) }}" height="60" alt="Stamp">
                @else
                    <div></div>
                @endif
                <div class="text-end">
                    <div class="text-muted small">Authorized Signature</div>
                    <div style="border-top: 1px solid #000; margin-top: 40px; width: 120px;"></div>
                </div>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">Thank you! This is a computer-generated receipt.</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('assets/css/owner-receipt-print.css') }}" rel="stylesheet">
@endpush
