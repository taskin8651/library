<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#667eea">
    <title>Start Free Trial - Softlix | Library Management Software</title>
    <meta name="description" content="Create your Softlix library account and start managing members, fees, seats and attendance in minutes. 3-day free trial, no credit card needed.">
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="https://softlix.in/register">

    <!-- Open Graph / Twitter -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://softlix.in/register">
    <meta property="og:site_name" content="Softlix">
    <meta property="og:title" content="Start Free Trial - Softlix | Library Management Software">
    <meta property="og:description" content="Create your Softlix library account and start managing members, fees, seats and attendance in minutes. 3-day free trial, no credit card needed.">
    <meta property="og:image" content="https://softlix.in/images/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="en_IN">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Start Free Trial - Softlix | Library Management Software">
    <meta name="twitter:description" content="Create your Softlix library account and start managing members, fees, seats and attendance in minutes. 3-day free trial, no credit card needed.">
    <meta name="twitter:image" content="https://softlix.in/images/og-image.png">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/auth-register.css') }}?v={{ @filemtime(public_path('assets/css/auth-register.css')) }}" rel="stylesheet">
    <link href="{{ asset('assets/css/ripple.css') }}" rel="stylesheet">
    @include('partials.page-loader-styles')
</head>
<body>
@include('partials.page-loader')

@php $activePlan = $plans->first(); @endphp

<div class="split-wrap">
    <!-- Brand / trust panel -->
    <div class="brand-panel">
        <div class="brand-logo"><i class="bi bi-rocket-takeoff-fill"></i></div>
        <h1>Start your library's<br>digital journey</h1>
        <p class="lead-text">Set up members, fees, seats and attendance in minutes — no paperwork, no credit card required.</p>

        <div class="feature-row"><div class="fi"><i class="bi bi-clock-history"></i></div><span>Free for {{ $activePlan->trial_days ?? 3 }} days, no card needed</span></div>
        <div class="feature-row"><div class="fi"><i class="bi bi-lightning-charge-fill"></i></div><span>Live in about 5 minutes</span></div>
        <div class="feature-row"><div class="fi"><i class="bi bi-x-circle"></i></div><span>Cancel anytime, no lock-in</span></div>
    </div>

    <!-- Form panel -->
    <div class="form-panel">
        <div class="register-card">
            <div class="text-center mb-4">
                <div class="brand-icon-reg"><i class="bi bi-book-fill"></i></div>
                <h4 class="fw-800">Create Your Library Account</h4>
                <p class="text-muted small">Start your free trial in a few quick steps</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger small" style="border-radius:11px;" role="alert">
                    @foreach($errors->all() as $error)
                        <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(!$activePlan)
                <div class="alert alert-warning small" style="border-radius:11px;">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    No plan is available for sign-up right now. Please contact support.
                </div>
            @else
            <form method="POST" action="/register" id="registerForm">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $activePlan->id }}">

                <!-- Active plan summary (only the currently-available plan is offered) -->
                <div class="active-plan-card mb-4 step-field" style="animation-delay:.05s">
                    <div class="apc-top">
                        <div>
                            <div class="apc-badge"><i class="bi bi-stars me-1"></i>{{ $activePlan->name }} Plan</div>
                            <div class="apc-price">₹{{ number_format($activePlan->price) }}<span>/mo</span></div>
                        </div>
                        <div class="apc-trial">{{ $activePlan->trial_days }}-day<br>free trial</div>
                    </div>
                    <div class="apc-features">
                        <span><i class="bi bi-check-circle-fill"></i>{{ $activePlan->max_branches == -1 ? 'Unlimited' : $activePlan->max_branches }} branch{{ $activePlan->max_branches == 1 ? '' : 'es' }}</span>
                        <span><i class="bi bi-check-circle-fill"></i>Unlimited members</span>
                        <span><i class="bi bi-check-circle-fill"></i>QR check-in</span>
                        @if($activePlan->staff_accounts)<span><i class="bi bi-check-circle-fill"></i>Staff accounts</span>@endif
                    </div>
                </div>

                <div class="mb-3 step-field" style="animation-delay:.1s">
                    <label class="form-label fw-500 small" for="libraryNameInput">Library Name *</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-building field-icon"></i>
                        <input type="text" name="library_name" id="libraryNameInput" class="form-control @error('library_name') is-invalid @enderror" value="{{ old('library_name') }}"
                            placeholder="e.g. Sharma Study Library" required autocomplete="organization">
                    </div>
                </div>

                <div class="row g-3 mb-3 step-field" style="animation-delay:.16s">
                    <div class="col-md-6">
                        <label class="form-label fw-500 small" for="emailInput">Email *</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope field-icon"></i>
                            <input type="email" name="email" id="emailInput" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500 small" for="phoneInput">Mobile Number *</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-phone field-icon"></i>
                            <input type="tel" name="phone" id="phoneInput" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="10 digit number" required autocomplete="tel" inputmode="numeric" maxlength="10">
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3 step-field" style="animation-delay:.19s">
                    <div class="col-md-6">
                        <label class="form-label fw-500 small" for="passwordInput">Password *</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock field-icon"></i>
                            <input type="password" name="password" id="passwordInput" class="form-control has-toggle @error('password') is-invalid @enderror" placeholder="Min 8 characters" required autocomplete="new-password">
                            <button type="button" class="toggle-pass" onclick="togglePasswordField('passwordInput','passwordToggleIcon')" aria-label="Show password">
                                <i class="bi bi-eye" id="passwordToggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500 small" for="passwordConfirmInput">Confirm Password *</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock field-icon"></i>
                            <input type="password" name="password_confirmation" id="passwordConfirmInput" class="form-control has-toggle" placeholder="Repeat password" required autocomplete="new-password">
                            <button type="button" class="toggle-pass" onclick="togglePasswordField('passwordConfirmInput','passwordConfirmToggleIcon')" aria-label="Show password">
                                <i class="bi bi-eye" id="passwordConfirmToggleIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="terms-check step-field" style="animation-delay:.22s">
                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                    <label for="termsCheck">
                        I agree to the <a href="#" class="fw-600">Terms of Service</a> and <a href="#" class="fw-600">Privacy Policy</a>.
                    </label>
                </div>

                <button type="submit" class="btn btn-register" id="regBtn">
                    <span id="regBtnText"><i class="bi bi-rocket-takeoff me-2"></i>Start Free Trial</span>
                </button>
            </form>
            @endif

            <div class="text-center mt-3">
                <p class="small text-muted mb-0">Already have an account? <a href="/login" class="fw-600">Sign in</a></p>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/auth-register.js') }}?v={{ @filemtime(public_path('assets/js/auth-register.js')) }}"></script>
<script src="{{ asset('assets/js/page-loader.js') }}"></script>
<script src="{{ asset('assets/js/ripple.js') }}"></script>
</body>
</html>
