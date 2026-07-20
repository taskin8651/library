@extends('layouts.app')
@section('title', 'Website Settings')
@section('page-title', 'Website Settings')

@section('sidebar-menu')
@include('admin.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/admin-panel.css') }}?v={{ @filemtime(public_path('assets/css/admin-panel.css')) }}" rel="stylesheet">
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

<form method="POST" action="/admin/settings" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="row g-4">
    <!-- Branding -->
    <div class="col-lg-5">
        <div class="table-card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-palette-fill me-2"></i>Branding</h6>

            <div class="mb-3">
                <label class="form-label fw-500 small">Site Name</label>
                <input type="text" name="site_name" class="form-control @error('site_name') is-invalid @enderror" value="{{ old('site_name', $settings->site_name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-500 small d-block">Logo</label>
                <div class="upload-row">
                    @if($settings->logo_url)
                        <img src="{{ $settings->logo_url }}" class="upload-preview" id="logoPreview" alt="Logo">
                    @else
                        <span class="upload-preview upload-preview-empty" id="logoPreview"><i class="bi bi-image"></i></span>
                    @endif
                    <label class="btn btn-outline-secondary btn-sm upload-btn">
                        <i class="bi bi-upload me-1"></i>Change
                        <input type="file" name="logo" accept="image/*" class="d-none" onchange="previewImage(this, 'logoPreview')">
                    </label>
                </div>
                <div class="form-text">Shown in the navbar across the public website.</div>
            </div>

            <div class="mb-1">
                <label class="form-label fw-500 small d-block">Favicon</label>
                <div class="upload-row">
                    @if($settings->favicon_url)
                        <img src="{{ $settings->favicon_url }}" class="upload-preview" style="width:40px;height:40px" id="faviconPreview" alt="Favicon">
                    @else
                        <span class="upload-preview upload-preview-empty" style="width:40px;height:40px" id="faviconPreview"><i class="bi bi-star" style="font-size:16px"></i></span>
                    @endif
                    <label class="btn btn-outline-secondary btn-sm upload-btn">
                        <i class="bi bi-upload me-1"></i>Change
                        <input type="file" name="favicon" accept="image/*" class="d-none" onchange="previewImage(this, 'faviconPreview')">
                    </label>
                </div>
                <div class="form-text">Small square image, shown in the browser tab.</div>
            </div>
        </div>
    </div>

    <!-- SEO -->
    <div class="col-lg-7">
        <div class="table-card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-search me-2"></i>SEO &amp; Sharing</h6>

            <div class="mb-3">
                <label class="form-label fw-500 small">Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $settings->meta_title) }}" maxlength="255" placeholder="Softlix – Smart Library Management Software">
            </div>

            <div class="mb-3">
                <label class="form-label fw-500 small">Meta Description</label>
                <textarea name="meta_description" class="form-control" rows="3" maxlength="500" placeholder="Shown in Google search results and social share previews.">{{ old('meta_description', $settings->meta_description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-500 small">Meta Keywords</label>
                <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $settings->meta_keywords) }}" placeholder="library software, seat booking, attendance system">
                <div class="form-text">Comma-separated.</div>
            </div>

            <div class="mb-1">
                <label class="form-label fw-500 small d-block">Social Share Image (OG Image)</label>
                <div class="upload-row">
                    @if($settings->og_image_url)
                        <img src="{{ $settings->og_image_url }}" class="upload-preview upload-preview-wide" id="ogImagePreview" alt="OG Image">
                    @else
                        <span class="upload-preview upload-preview-wide upload-preview-empty" id="ogImagePreview"><i class="bi bi-image"></i></span>
                    @endif
                    <label class="btn btn-outline-secondary btn-sm upload-btn">
                        <i class="bi bi-upload me-1"></i>Change
                        <input type="file" name="og_image" accept="image/*" class="d-none" onchange="previewImage(this, 'ogImagePreview')">
                    </label>
                </div>
                <div class="form-text">Shown when the site is shared on WhatsApp, Facebook, Twitter, etc. Recommended 1200x630.</div>
            </div>
        </div>
    </div>

    <!-- Contact -->
    <div class="col-lg-5">
        <div class="table-card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-telephone-fill me-2"></i>Contact Information</h6>
            <div class="mb-3">
                <label class="form-label fw-500 small">Contact Email</label>
                <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror" value="{{ old('contact_email', $settings->contact_email) }}">
            </div>
            <div class="mb-1">
                <label class="form-label fw-500 small">Contact Phone</label>
                <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $settings->contact_phone) }}">
            </div>
        </div>
    </div>

    <!-- Social Links -->
    <div class="col-lg-7">
        <div class="table-card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-share-fill me-2"></i>Social Links</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-500 small"><i class="bi bi-facebook me-1"></i>Facebook</label>
                    <input type="url" name="facebook_url" class="form-control @error('facebook_url') is-invalid @enderror" value="{{ old('facebook_url', $settings->facebook_url) }}" placeholder="https://facebook.com/...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500 small"><i class="bi bi-twitter-x me-1"></i>Twitter / X</label>
                    <input type="url" name="twitter_url" class="form-control @error('twitter_url') is-invalid @enderror" value="{{ old('twitter_url', $settings->twitter_url) }}" placeholder="https://x.com/...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500 small"><i class="bi bi-instagram me-1"></i>Instagram</label>
                    <input type="url" name="instagram_url" class="form-control @error('instagram_url') is-invalid @enderror" value="{{ old('instagram_url', $settings->instagram_url) }}" placeholder="https://instagram.com/...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500 small"><i class="bi bi-linkedin me-1"></i>LinkedIn</label>
                    <input type="url" name="linkedin_url" class="form-control @error('linkedin_url') is-invalid @enderror" value="{{ old('linkedin_url', $settings->linkedin_url) }}" placeholder="https://linkedin.com/...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-500 small"><i class="bi bi-youtube me-1"></i>YouTube</label>
                    <input type="url" name="youtube_url" class="form-control @error('youtube_url') is-invalid @enderror" value="{{ old('youtube_url', $settings->youtube_url) }}" placeholder="https://youtube.com/...">
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
