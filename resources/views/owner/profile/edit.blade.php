@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/owner-profile.css') }}?v={{ @filemtime(public_path('assets/css/owner-profile.css')) }}" rel="stylesheet">
@endpush

@section('content')

<!-- Profile Header -->
<div class="profile-hero mb-4">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <span class="profile-avatar">{{ substr($user->name ?? '?', 0, 1) }}</span>
        <div>
            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <span class="badge bg-light text-dark text-capitalize">{{ $user->role }}</span>
            <span class="text-muted small ms-2">{{ $user->email }}</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Profile Info -->
    <div class="col-lg-6">
        <div class="table-card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Profile Information</h6>

            @if($errors->any() && !$errors->has('current_password') && !$errors->has('password'))
            <div class="alert alert-danger small" style="border-radius:11px;">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
            @endif

            <form method="POST" action="/owner/profile">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-500 small">Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-500 small">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-500 small">Mobile Number</label>
                    <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" inputmode="numeric" maxlength="10" required>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="col-lg-6">
        <div class="table-card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock-fill me-2"></i>Change Password</h6>

            @if($errors->has('current_password') || $errors->has('password'))
            <div class="alert alert-danger small" style="border-radius:11px;">
                @if($errors->has('current_password')){{ $errors->first('current_password') }}@endif
                @if($errors->has('password')){{ $errors->first('password') }}@endif
            </div>
            @endif

            <form method="POST" action="/owner/profile/password">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-500 small">Current Password</label>
                    <div class="pwd-wrap">
                        <input type="password" name="current_password" id="currentPasswordInput" class="form-control" required>
                        <button type="button" class="pwd-toggle" onclick="toggleProfilePassword('currentPasswordInput', this)"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-500 small">New Password</label>
                    <div class="pwd-wrap">
                        <input type="password" name="password" id="newPasswordInput" class="form-control" placeholder="Min 8 characters" required autocomplete="new-password">
                        <button type="button" class="pwd-toggle" onclick="toggleProfilePassword('newPasswordInput', this)"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-500 small">Confirm New Password</label>
                    <div class="pwd-wrap">
                        <input type="password" name="password_confirmation" id="confirmPasswordInput" class="form-control" required autocomplete="new-password">
                        <button type="button" class="pwd-toggle" onclick="toggleProfilePassword('confirmPasswordInput', this)"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-key me-1"></i>Update Password</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/owner-profile.js') }}?v={{ @filemtime(public_path('assets/js/owner-profile.js')) }}"></script>
@endpush
