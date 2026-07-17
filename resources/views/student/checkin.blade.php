<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Check In - {{ $library->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/student-checkin.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/ripple.css') }}" rel="stylesheet">
    @include('partials.page-loader-styles')
</head>
<body data-library-slug="{{ $library->slug }}">
    @include('partials.page-loader')
    <div class="checkin-card">
        @if($library->logo)
            <div class="logo-wrap"><img src="{{ $library->logo_url }}" height="60" class="mb-3 rounded-3"></div>
        @else
            <div class="logo-wrap">
                <div class="logo-ring"><i class="bi bi-book-fill"></i></div>
            </div>
        @endif
        <h5 class="fw-bold mb-1">{{ $library->name }}</h5>
        <p class="text-muted small mb-4">Enter your UID to Check In / Check Out</p>

        <div class="mb-3">
            <input type="text" id="uid-input" class="uid-input form-control"
                placeholder="UID" maxlength="6" autofocus autocomplete="off">
        </div>

        <button class="btn btn-checkin" onclick="processCheckIn()" id="checkinBtn">
            <span id="checkinBtnText"><i class="bi bi-door-open me-2"></i>Check In / Out</span>
        </button>

        <div id="result-box" class="result-box">
            <div id="result-icon" class="result-icon-wrap"></div>
            <div id="result-title" class="fw-bold fs-5"></div>
            <div id="result-message" class="text-muted small mt-1"></div>
        </div>

        <p class="text-muted mt-4 mb-0" style="font-size:12px;">
            <i class="bi bi-info-circle me-1"></i>UID is printed on your admission card
        </p>
    </div>

    <script src="{{ asset('assets/js/student-checkin.js') }}"></script>
    <script src="{{ asset('assets/js/page-loader.js') }}"></script>
    <script src="{{ asset('assets/js/ripple.js') }}"></script>
</body>
</html>
