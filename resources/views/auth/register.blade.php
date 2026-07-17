<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LibraryCRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/auth-register.css') }}" rel="stylesheet">
    @include('partials.page-loader-styles')
</head>
<body>
@include('partials.page-loader')
<div class="container">
    <div class="register-card">
        <div class="text-center mb-4">
            <div class="brand-icon-reg"><i class="bi bi-rocket-takeoff-fill"></i></div>
            <h4 class="fw-800">Start Your Free Trial</h4>
            <p class="text-muted small">14 days free. No credit card required.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger small" style="border-radius:11px;">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/register" id="registerForm">
            @csrf

            <!-- Plan Selection -->
            <div class="mb-4 step-field" style="animation-delay:.05s">
                <label class="form-label fw-600 small">Choose Plan</label>
                <div class="row g-2">
                    @foreach($plans as $plan)
                    <div class="col-4">
                        <label class="plan-card d-block {{ old('plan_id') == $plan->id ? 'selected' : '' }}">
                            <input type="radio" name="plan_id" value="{{ $plan->id }}" class="d-none" {{ old('plan_id') == $plan->id ? 'checked' : ($loop->first ? 'checked' : '') }}>
                            <div class="fw-600 small">{{ $plan->name }}</div>
                            <div class="text-primary fw-bold">₹{{ number_format($plan->price) }}<span class="text-muted fw-normal">/mo</span></div>
                            <div class="text-muted" style="font-size:11px;">{{ $plan->max_branches == -1 ? 'Unlimited' : $plan->max_branches }} branch</div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mb-3 step-field" style="animation-delay:.1s">
                <label class="form-label fw-500 small">Library Name *</label>
                <input type="text" name="library_name" class="form-control" value="{{ old('library_name') }}"
                    placeholder="e.g. Sharma Study Library" required>
            </div>

            <div class="mb-3 step-field" style="animation-delay:.13s">
                <label class="form-label fw-500 small">Library URL *</label>
                <div class="input-group">
                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}"
                        placeholder="yourname" pattern="[a-z0-9-]+" required>
                    <span class="input-group-text small text-muted" style="border-radius:0 11px 11px 0;">.librarycrm.com</span>
                </div>
                <div class="form-text">Lowercase letters, numbers, hyphens only</div>
            </div>

            <div class="row g-3 mb-3 step-field" style="animation-delay:.16s">
                <div class="col-md-6">
                    <label class="form-label fw-500 small">Email *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500 small">Phone *</label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="10 digit number" required>
                </div>
            </div>

            <div class="row g-3 mb-4 step-field" style="animation-delay:.19s">
                <div class="col-md-6">
                    <label class="form-label fw-500 small">Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Min 8 characters" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500 small">Confirm Password *</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-register" id="regBtn">
                <span id="regBtnText"><i class="bi bi-rocket-takeoff me-2"></i>Start Free Trial</span>
            </button>
        </form>

        <div class="text-center mt-3">
            <p class="small text-muted">Already have an account? <a href="/login" class="fw-600" style="color:#667eea">Sign in</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/auth-register.js') }}"></script>
<script src="{{ asset('assets/js/page-loader.js') }}"></script>
</body>
</html>
