@extends('layouts.app')
@section('title', 'Announcements')
@section('page-title', 'Announcements')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@push('styles')
<link href="{{ asset('assets/css/owner-announcements.css') }}" rel="stylesheet">
@endpush

@section('content')

@php
    // Safe "markdown-lite" renderer: escapes first, then adds a small
    // fixed set of safe tags — never trusts raw user HTML.
    $mdLite = function ($text) {
        $safe = e($text);
        $safe = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $safe);
        $safe = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $safe);
        $safe = preg_replace('/(?:^|\n)- (.+)/', '<br>&bull; $1', $safe);
        return $safe;
    };
@endphp

<div class="row g-3">
    <!-- Compose Form -->
    <div class="col-lg-5">
        <div class="table-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-megaphone-fill me-2"></i>New Announcement</h6>
            @if($errors->any())
            <div class="alert alert-danger small">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif
            <form method="POST" action="/owner/announcements" id="announceForm">
                @csrf
                <div class="form-floating mb-3">
                    <input type="text" name="title" id="annTitle" class="form-control" value="{{ old('title') }}" maxlength="150" required placeholder="Title">
                    <label for="annTitle">Title *</label>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-500">Message *</label>
                    <div class="rte-toolbar">
                        <button type="button" class="rte-btn" data-wrap="**" title="Bold"><i class="bi bi-type-bold"></i></button>
                        <button type="button" class="rte-btn" data-wrap="*" title="Italic"><i class="bi bi-type-italic"></i></button>
                        <button type="button" class="rte-btn" data-list title="Bullet list"><i class="bi bi-list-ul"></i></button>
                    </div>
                    <textarea name="message" id="annMessage" class="form-control rte-textarea" rows="4" maxlength="2000" required placeholder="Write your announcement...">{{ old('message') }}</textarea>
                    <div class="form-text">Select text and use the toolbar for <strong>bold</strong>, <em>italic</em> or bullet points.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-500">Type</label>
                    <div class="announce-type-picker" id="annTypePicker">
                        <label class="atype atype-info">
                            <input type="radio" name="type" value="info" {{ old('type','info')=='info'?'checked':'' }}>
                            <i class="bi bi-info-circle-fill"></i>Info
                        </label>
                        <label class="atype atype-success">
                            <input type="radio" name="type" value="success" {{ old('type')=='success'?'checked':'' }}>
                            <i class="bi bi-check-circle-fill"></i>Success
                        </label>
                        <label class="atype atype-warning">
                            <input type="radio" name="type" value="warning" {{ old('type')=='warning'?'checked':'' }}>
                            <i class="bi bi-exclamation-triangle-fill"></i>Warning
                        </label>
                        <label class="atype atype-danger">
                            <input type="radio" name="type" value="danger" {{ old('type')=='danger'?'checked':'' }}>
                            <i class="bi bi-megaphone-fill"></i>Urgent
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-500">Audience</label>
                    <div class="form-floating">
                        <select name="target_audience" id="annAudience" class="form-select">
                            <option value="all" {{ old('target_audience','all')=='all'?'selected':'' }}>All Members</option>
                            <option value="active" {{ old('target_audience')=='active'?'selected':'' }}>Active Members Only</option>
                            <option value="expiring" {{ old('target_audience')=='expiring'?'selected':'' }}>Expiring Soon Only (&le;7 days)</option>
                        </select>
                        <label for="annAudience">Audience</label>
                    </div>
                    <div class="form-text">Who should see this on their dashboard.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-500">Schedule (optional)</label>
                    <div class="form-floating">
                        <input type="datetime-local" name="scheduled_at" id="annSchedule" class="form-control" placeholder="Schedule" value="{{ old('scheduled_at') }}" min="{{ now()->format('Y-m-d\TH:i') }}">
                        <label for="annSchedule">Post later at&hellip;</label>
                    </div>
                    <div class="form-text">Leave empty to post immediately.</div>
                </div>

                <!-- Live preview -->
                <div class="mb-4">
                    <label class="form-label small fw-500">Preview</label>
                    <div class="announce-preview" id="annPreview" data-type="info">
                        <i class="bi bi-info-circle-fill preview-icon"></i>
                        <div>
                            <div class="preview-title" id="previewTitle">Your announcement title</div>
                            <div class="preview-message" id="previewMessage">Your message will appear here as you type.</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send-fill me-2"></i>Post Announcement</button>
            </form>
        </div>
    </div>

    <!-- List -->
    <div class="col-lg-7">
        <div class="table-card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>All Announcements ({{ $announcements->total() }})</div>
            <div class="p-3">
                @forelse($announcements as $a)
                @php
                    $typeMeta = [
                        'info'    => ['color' => 'primary', 'icon' => 'bi-info-circle-fill'],
                        'success' => ['color' => 'success', 'icon' => 'bi-check-circle-fill'],
                        'warning' => ['color' => 'warning', 'icon' => 'bi-exclamation-triangle-fill'],
                        'danger'  => ['color' => 'danger', 'icon' => 'bi-megaphone-fill'],
                    ][$a->type];
                    $audienceLabel = ['all' => 'All Members', 'active' => 'Active Only', 'expiring' => 'Expiring Soon'][$a->target_audience] ?? 'All Members';
                @endphp
                <div class="announce-row {{ !$loop->last ? 'mb-2' : '' }}">
                    <div class="announce-row-icon bg-{{ $typeMeta['color'] }}-subtle text-{{ $typeMeta['color'] }}">
                        <i class="bi {{ $typeMeta['icon'] }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-600 small">{{ $a->title }}</span>
                            <span class="badge bg-{{ $typeMeta['color'] }} text-uppercase" style="font-size:9.5px">{{ $a->type }}</span>
                            <span class="badge {{ $a->is_active ? 'badge-active' : 'badge-inactive' }}" style="font-size:9.5px">{{ $a->is_active ? 'Active' : 'Hidden' }}</span>
                            <span class="badge bg-light text-dark" style="font-size:9.5px"><i class="bi bi-people-fill me-1"></i>{{ $audienceLabel }}</span>
                            @if($a->isScheduledForFuture())
                            <span class="badge bg-warning text-dark" style="font-size:9.5px"><i class="bi bi-clock-history me-1"></i>Scheduled {{ $a->scheduled_at->format('d M, h:i A') }}</span>
                            @endif
                        </div>
                        <div class="text-muted small mt-1">{!! $mdLite(\Illuminate\Support\Str::limit($a->message, 90)) !!}</div>
                        <div class="text-muted mt-1" style="font-size:11px"><i class="bi bi-clock me-1"></i>{{ $a->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="d-flex gap-1">
                        <form method="POST" action="/owner/announcements/{{ $a->id }}/toggle">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary" title="{{ $a->is_active ? 'Hide' : 'Show' }}">
                                <i class="bi {{ $a->is_active ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                            </button>
                        </form>
                        <form method="POST" action="/owner/announcements/{{ $a->id }}" data-confirm="Delete this announcement? This cannot be undone.">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <div class="es-icon"><i class="bi bi-megaphone"></i></div>
                    <h6>No announcements yet</h6>
                    <p>Post your first announcement to reach all your members instantly.</p>
                </div>
                @endforelse
            </div>
            @if($announcements->hasPages())
            <div class="p-3 pt-0">{{ $announcements->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/owner-announcements.js') }}"></script>
@endpush
