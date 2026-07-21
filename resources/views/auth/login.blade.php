<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#667eea">
    <title>Login - Softlix | Library Management Software</title>
    <meta name="description" content="Sign in to your Softlix library management dashboard to manage members, fees, seats and attendance.">
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="https://softlix.in/login">

    <!-- Open Graph / Twitter -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://softlix.in/login">
    <meta property="og:site_name" content="Softlix">
    <meta property="og:title" content="Login - Softlix | Library Management Software">
    <meta property="og:description" content="Sign in to your Softlix library management dashboard to manage members, fees, seats and attendance.">
    <meta property="og:image" content="https://softlix.in/images/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="en_IN">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Login - Softlix | Library Management Software">
    <meta name="twitter:description" content="Sign in to your Softlix library management dashboard to manage members, fees, seats and attendance.">
    <meta name="twitter:image" content="https://softlix.in/images/og-image.png">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    @include('partials.page-loader-styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/auth-login.css') }}?v={{ @filemtime(public_path('assets/css/auth-login.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/ripple.css') }}" rel="stylesheet">
</head>
<body>
    @include('partials.page-loader')
    <div class="split-wrap">
        <!-- Brand / marketing panel -->
        <div class="brand-panel">
            <div class="brand-logo"><i class="bi bi-book-fill"></i></div>
            <h1>Manage your library<br>the <span style="background: linear-gradient(90deg,#a5b4fc,#5eead4,#a5b4fc); background-size: 200% auto; -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: shine 4s linear infinite;">smart way</span></h1>
            <p class="lead-text">Members, fees, seats, attendance and subscriptions — all in one clean dashboard built for study libraries.</p>

            <div class="feature-row"><div class="fi"><i class="bi bi-qr-code-scan"></i></div><span>QR-based attendance check-in/out</span></div>
            <div class="feature-row"><div class="fi"><i class="bi bi-cash-stack"></i></div><span>Fee tracking with instant PDF receipts</span></div>
            <div class="feature-row"><div class="fi"><i class="bi bi-grid-3x3-gap-fill"></i></div><span>Visual seat layout &amp; occupancy</span></div>
        </div>

        <!-- Form panel -->
        <div class="form-panel">
            <div class="login-card">
                <div class="text-center mb-4">
                    <div class="brand-icon"><i class="bi bi-book-fill"></i></div>
                    <h4 class="fw-800">Softlix</h4>
                    <p class="text-muted small">Sign in to your account</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success small d-flex align-items-center gap-2" style="border-radius:11px;">
                        <i class="bi bi-check-circle"></i><span>{{ session('success') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger small d-flex align-items-center gap-2" style="border-radius:11px;" role="alert">
                        <i class="bi bi-exclamation-circle"></i><span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="/login" id="loginForm">
                    @csrf
                    <div class="field">
                        <label class="form-label fw-500 small" for="emailInput">Email Address</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope field-icon"></i>
                            <input type="email" name="email" id="emailInput" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                placeholder="you@example.com" required autofocus autocomplete="username">
                        </div>
                    </div>
                    <div class="field">
                        <label class="form-label fw-500 small" for="passwordInput">Password</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock field-icon"></i>
                            <input type="password" name="password" id="passwordInput" class="form-control has-toggle @error('password') is-invalid @enderror" placeholder="••••••••" required autocomplete="current-password">
                            <button type="button" class="toggle-pass" onclick="togglePasswordField('passwordInput','toggleIcon')" aria-label="Show password">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4 d-flex align-items-center justify-content-between">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small" for="remember">Remember me</label>
                        </div>
                        <button type="button" class="forgot-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</button>
                    </div>
                    <button type="submit" class="btn btn-login" id="loginBtn">
                        <span id="loginBtnText">Sign In</span>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted">New library? <a href="/register" class="text-decoration-none fw-600" style="color:#667eea">Start Free Trial</a></p>
                </div>

                <hr class="my-4">
                
            </div>
        </div>
    </div>

    <!-- Forgot Password helper (no self-service reset flow yet) -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold" id="forgotPasswordLabel"><i class="bi bi-key-fill me-2"></i>Reset Your Password</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Self-service password reset isn't available yet. Please contact your library owner or admin — they can reset your password from their dashboard.
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Got it</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/auth-login.js') }}?v={{ @filemtime(public_path('assets/js/auth-login.js')) }}"></script>
    <script src="{{ asset('assets/js/ripple.js') }}"></script>
    <script src="{{ asset('assets/js/page-loader.js') }}"></script>
</body>
</html>
