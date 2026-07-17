@extends('layouts.app')
@section('title', 'QR Code')
@section('page-title', 'Entry QR Code')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-7 col-xl-6">
        @if($qr && $url)
        <div class="table-card qr-card p-4 p-md-5 text-center">
            <div class="qr-card-badge"><i class="bi bi-qr-code-scan"></i></div>
            <h5 class="fw-bold mb-1">{{ $library->name }}</h5>
            <p class="text-muted small mb-4">Students scan this QR code to Check In / Check Out</p>

            <!-- QR Code -->
            <div class="qr-frame mb-4">
                <span class="qr-corner tl"></span><span class="qr-corner tr"></span>
                <span class="qr-corner bl"></span><span class="qr-corner br"></span>
                <div class="qr-code-wrap" id="qrCodeWrap">
                    {!! $qr !!}
                </div>
            </div>

            <div class="qr-url-box mb-4">
                <div class="qr-url-label"><i class="bi bi-link-45deg me-1"></i>Check-in URL</div>
                <div class="qr-url-row">
                    <code id="qrUrlText">{{ $url }}</code>
                    <button type="button" class="qr-copy-btn" id="qrCopyBtn" title="Copy link">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
            </div>

            <div class="qr-actions">
                <button type="button" onclick="window.print()" class="btn btn-outline-secondary qr-action-btn">
                    <i class="bi bi-printer"></i><span>Print QR</span>
                </button>
                <button type="button" class="btn btn-outline-secondary qr-action-btn" id="qrDownloadBtn">
                    <i class="bi bi-download"></i><span>Download</span>
                </button>
                <button type="button" class="btn btn-outline-secondary qr-action-btn d-none" id="qrShareBtn">
                    <i class="bi bi-share-fill"></i><span>Share</span>
                </button>
                <a href="{{ $url }}" target="_blank" class="btn btn-primary qr-action-btn">
                    <i class="bi bi-box-arrow-up-right"></i><span>Open Check-in Page</span>
                </a>
            </div>

            <hr class="my-4">
            <div class="qr-howto text-start">
                <p class="small fw-600 mb-2"><i class="bi bi-info-circle me-1"></i>How it works</p>
                <ol class="small text-muted mb-0">
                    <li class="mb-1">Print this QR code and place it at library entrance</li>
                    <li class="mb-1">Student scans QR with phone camera</li>
                    <li class="mb-1">Enters their 6-digit UID on the check-in page</li>
                    <li>System auto check-in / check-out</li>
                </ol>
            </div>
        </div>
        @else
        <div class="table-card p-4">
            <div class="empty-state">
                <div class="es-icon"><i class="bi bi-qr-code"></i></div>
                <h6>QR Code Unavailable</h6>
                <p>We couldn't generate your check-in QR code right now. Please refresh the page, and contact support if this keeps happening.</p>
                <button type="button" class="btn btn-primary btn-sm" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Retry
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('assets/css/owner-qr.css') }}?v={{ @filemtime(public_path('assets/css/owner-qr.css')) }}" rel="stylesheet">
<link href="{{ asset('assets/css/owner-qr-print.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script>window.QR_CHECKIN_URL = @json($url ?? '');</script>
<script src="{{ asset('assets/js/owner-qr.js') }}?v={{ @filemtime(public_path('assets/js/owner-qr.js')) }}"></script>
@endpush
