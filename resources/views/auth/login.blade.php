<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#667eea">
    <title>Login - LibraryCRM</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/auth-login.css') }}" rel="stylesheet">
</head>
<body>
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
                    <h4 class="fw-800">LibraryCRM</h4>
                    <p class="text-muted small">Sign in to your account</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger small d-flex align-items-center gap-2" style="border-radius:11px;">
                        <i class="bi bi-exclamation-circle"></i><span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="/login" id="loginForm">
                    @csrf
                    <div class="field">
                        <label class="form-label fw-500 small">Email Address</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope field-icon"></i>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                placeholder="you@example.com" required autofocus>
                        </div>
                    </div>
                    <div class="field">
                        <label class="form-label fw-500 small">Password</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock field-icon"></i>
                            <input type="password" name="password" id="passwordInput" class="form-control" placeholder="••••••••" required style="padding-right:38px;">
                            <button type="button" class="toggle-pass" onclick="togglePass()"><i class="bi bi-eye" id="toggleIcon"></i></button>
                        </div>
                    </div>
                    <div class="mb-4 d-flex align-items-center justify-content-between">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small" for="remember">Remember me</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-login" id="loginBtn">
                        <span id="loginBtnText">Sign In</span>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted">New library? <a href="/register" class="text-decoration-none fw-600" style="color:#667eea">Start Free Trial</a></p>
                    <button type="button" id="installAppBtn" data-pwa-install-btn class="btn btn-sm btn-outline-secondary d-none mt-2">
                        <i class="bi bi-download me-1"></i>Install App
                    </button>
                </div>

                <hr class="my-4">
                <div class="demo-box">
                    <p class="small text-muted mb-1 fw-600"><i class="bi bi-info-circle me-1"></i>Demo Credentials</p>
                    <p class="small mb-1"><strong>Admin:</strong> admin@librarycrm.com / password</p>
                    <p class="small mb-0"><strong>Owner:</strong> owner@demo.com / password</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/auth-login.js') }}"></script>
    <script src="{{ asset('assets/js/pwa-install.js') }}"></script>
</body>
</html>
