@extends('layouts.app')
@section('title', 'Library Settings')
@section('page-title', 'Library Settings')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/owner-settings.css') }}?v={{ @filemtime(public_path('assets/css/owner-settings.css')) }}" rel="stylesheet">
@endpush

@section('content')

@if($errors->any())
<div class="alert alert-danger small" style="border-radius:11px;">
    @foreach($errors->all() as $error)
        <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
    @endforeach
</div>
@endif

<form method="POST" action="/owner/settings" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="row g-4">
    <!-- Business Info -->
    <div class="col-lg-7">
        <div class="table-card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-building me-2"></i>Business Information</h6>

            <div class="mb-3">
                <label class="form-label fw-500 small">Library Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $library->name) }}" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-500 small">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $library->email) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500 small">Phone</label>
                    <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $library->phone) }}" inputmode="numeric" maxlength="10" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-500 small">Address</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $library->address) }}" placeholder="Street address">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-500 small">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $library->city) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500 small">State</label>
                    <input type="text" name="state" class="form-control" value="{{ old('state', $library->state) }}">
                </div>
            </div>

            <div class="mb-1">
                <label class="form-label fw-500 small">Tagline</label>
                <input type="text" name="tagline" class="form-control" value="{{ old('tagline', $library->tagline) }}" maxlength="150" placeholder="Shown on fee receipts, e.g. &quot;Your trusted study partner&quot;">
                <div class="form-text">Appears under your logo on fee receipts.</div>
            </div>
        </div>
    </div>

    <!-- Branding -->
    <div class="col-lg-5">
        <div class="table-card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-palette-fill me-2"></i>Branding</h6>

            <div class="mb-3">
                <label class="form-label fw-500 small d-block">Logo</label>
                <div class="upload-row">
                    <img src="{{ $library->logo_url }}" class="upload-preview" id="logoPreview" alt="Logo">
                    <label class="btn btn-outline-secondary btn-sm upload-btn">
                        <i class="bi bi-upload me-1"></i>Change
                        <input type="file" name="logo" accept="image/*" class="d-none" onchange="previewImage(this, 'logoPreview')">
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-500 small d-block">Receipt Stamp</label>
                <div class="upload-row">
                    @if($library->stamp)
                        <img src="{{ asset('storage/'.$library->stamp) }}" class="upload-preview" id="stampPreview" alt="Stamp">
                    @else
                        <span class="upload-preview upload-preview-empty" id="stampPreview"><i class="bi bi-stamp"></i></span>
                    @endif
                    <label class="btn btn-outline-secondary btn-sm upload-btn">
                        <i class="bi bi-upload me-1"></i>Change
                        <input type="file" name="stamp" accept="image/*" class="d-none" onchange="previewImage(this, 'stampPreview')">
                    </label>
                </div>
                <div class="form-text">Printed on fee receipts as your official stamp.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-500 small d-block">Banner</label>
                <div class="upload-row">
                    @if($library->banner)
                        <img src="{{ asset('storage/'.$library->banner) }}" class="upload-preview upload-preview-wide" id="bannerPreview" alt="Banner">
                    @else
                        <span class="upload-preview upload-preview-wide upload-preview-empty" id="bannerPreview"><i class="bi bi-image"></i></span>
                    @endif
                    <label class="btn btn-outline-secondary btn-sm upload-btn">
                        <i class="bi bi-upload me-1"></i>Change
                        <input type="file" name="banner" accept="image/*" class="d-none" onchange="previewImage(this, 'bannerPreview')">
                    </label>
                </div>
            </div>

            <div class="mb-1">
                <label class="form-label fw-500 small">Theme Color</label>
                <div class="d-flex align-items-center gap-2">
                    <input type="color" name="theme_color" class="form-control form-control-color" value="{{ old('theme_color', $library->theme_color ?? '#0d6efd') }}" title="Choose your brand color">
                    <span class="text-muted small">Used across your dashboard's accent color</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle me-1"></i>Save Settings</button>
</div>

</form>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/owner-settings.js') }}?v={{ @filemtime(public_path('assets/js/owner-settings.js')) }}"></script>
@endpush
