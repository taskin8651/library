<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>No Membership - Softlix</title>
<meta name="robots" content="noindex, nofollow">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('assets/css/student-no-member.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/ripple.css') }}" rel="stylesheet">
@include('partials.page-loader-styles')
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
@include('partials.page-loader')
<div class="notice-card text-center">
    <div class="icon-ring"><i class="bi bi-exclamation-triangle-fill"></i></div>
    <h4 class="fw-bold">No Active Membership</h4>
    <p class="text-muted">Your account isn't linked to a member profile yet. Contact your library owner to activate your account.</p>
    <form method="POST" action="/logout" class="mt-3">@csrf<button class="btn btn-outline-secondary btn-sm px-4"><i class="bi bi-box-arrow-right me-1"></i>Logout</button></form>
</div>
<script src="{{ asset('assets/js/page-loader.js') }}"></script>
<script src="{{ asset('assets/js/ripple.js') }}"></script>
</body>
</html>
