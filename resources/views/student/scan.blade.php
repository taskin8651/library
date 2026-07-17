<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f1117">
    <title>Scan to Check In - {{ $library->name }}</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/student-scan.css') }}" rel="stylesheet">
    @include('partials.page-loader-styles')
</head>
<body data-library-slug="{{ $library->slug }}">
    @include('partials.page-loader')

    <div class="scan-topbar">
        <a href="/student/dashboard" class="scan-back"><i class="bi bi-arrow-left"></i></a>
        <div class="scan-title">Scan to Check In</div>
        <div style="width:38px"></div>
    </div>

    <div class="scan-stage">
        <div id="qr-reader"></div>
        <div class="scan-frame">
            <span class="corner tl"></span><span class="corner tr"></span>
            <span class="corner bl"></span><span class="corner br"></span>
            <div class="scan-line"></div>
        </div>
    </div>

    <p class="scan-hint" id="scanHint">
        <i class="bi bi-qr-code-scan me-1"></i>Point your camera at the QR code at {{ $library->name }}'s entrance
    </p>

    <p class="scan-fallback">
        Camera not working?
        <a href="/checkin/{{ $library->slug }}">Use classic check-in</a>
    </p>

    <!-- Result overlay -->
    <div id="result-overlay" class="result-overlay">
        <div id="result-icon" class="result-icon-wrap"></div>
        <div id="result-title" class="result-title"></div>
        <div id="result-message" class="result-message"></div>
        <div class="spinner-border text-light mt-4" id="result-spinner" role="status"></div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="{{ asset('assets/js/student-scan.js') }}"></script>
    <script src="{{ asset('assets/js/page-loader.js') }}"></script>
</body>
</html>
