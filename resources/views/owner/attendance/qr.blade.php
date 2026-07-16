@extends('layouts.app')
@section('title', 'QR Code')
@section('page-title', 'Entry QR Code')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="table-card p-4">
            <h5 class="fw-bold mb-1">{{ $library->name }}</h5>
            <p class="text-muted small mb-4">Students scan this QR code to Check In / Check Out</p>

            <!-- QR Code -->
            <div class="d-inline-block p-3 border rounded-3 mb-4 bg-white">
                {!! $qr !!}
            </div>

            <div class="bg-light rounded-3 p-3 mb-4">
                <p class="small text-muted mb-1">Check-in URL:</p>
                <code>{{ $url }}</code>
            </div>

            <div class="d-flex gap-2 justify-content-center">
                <button onclick="window.print()" class="btn btn-outline-secondary">
                    <i class="bi bi-printer me-2"></i>Print QR
                </button>
                <a href="{{ $url }}" target="_blank" class="btn btn-primary">
                    <i class="bi bi-box-arrow-up-right me-2"></i>Open Check-in Page
                </a>
            </div>

            <hr class="my-4">
            <div class="text-start">
                <p class="small fw-600 mb-2">How it works:</p>
                <ol class="small text-muted">
                    <li class="mb-1">Print this QR code and place it at library entrance</li>
                    <li class="mb-1">Student scans QR with phone camera</li>
                    <li class="mb-1">Enters their 6-digit UID on the check-in page</li>
                    <li class="mb-1">System auto check-in / check-out</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('assets/css/owner-qr-print.css') }}" rel="stylesheet">
@endpush
