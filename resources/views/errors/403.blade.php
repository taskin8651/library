<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Access Restricted - Softlix</title>
<meta name="robots" content="noindex, nofollow">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="{{ asset('assets/css/error-page.css') }}?v={{ @filemtime(public_path('assets/css/error-page.css')) }}" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
<div class="error-card text-center">
    <div class="error-brand"><i class="bi bi-book-fill"></i> Softlix</div>

    <div class="icon-ring"><i class="bi bi-shield-lock-fill"></i></div>

    <h4 class="fw-800">Access Restricted</h4>
    <p class="text-muted">{{ $exception->getMessage() ?: "You don't have permission to access this page." }}</p>

    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center mt-4">
        <a href="mailto:hello@softlix.in" class="btn btn-outline-secondary">
            <i class="bi bi-envelope me-1"></i>Contact Support
        </a>
        <a href="/login" class="btn btn-error-primary">
            <i class="bi bi-box-arrow-in-right me-1"></i>Back to Login
        </a>
    </div>
</div>
</body>
</html>
