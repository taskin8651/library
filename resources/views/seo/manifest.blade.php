@php
    // Dynamic so the installed PWA's home-screen icon reflects whatever logo
    // the admin has uploaded (Admin > Website Settings) instead of a fixed
    // bundled image — same "serve as a route, not a static file" pattern
    // already used for robots.txt/sitemap.xml.
    $settings = \App\Models\Setting::current();
    $siteName = $settings->site_name ?: 'Softlix';

    $icons = [];
    if ($settings->logo_url) {
        $icons[] = ['src' => $settings->logo_url, 'sizes' => 'any', 'type' => 'image/png', 'purpose' => 'any'];
    } else {
        $icons[] = ['src' => asset('images/icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'];
        $icons[] = ['src' => asset('images/icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'];
    }
    // The maskable icon needs its own safe-zone padding baked in, which an
    // arbitrary uploaded logo won't have — always keep the bundled one for
    // Android's adaptive-icon mask regardless of a custom logo.
    $icons[] = ['src' => asset('images/icon-maskable-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'];
@endphp
{
    "name": {!! json_encode($siteName . ' - Student App') !!},
    "short_name": {!! json_encode($siteName) !!},
    "description": "Log in, scan the QR at your library, and track your attendance and plan — right from your phone.",
    "start_url": "/student/dashboard?source=pwa",
    "scope": "/",
    "display": "standalone",
    "orientation": "portrait",
    "background_color": "#ffffff",
    "theme_color": "#667eea",
    "icons": {!! json_encode($icons) !!}
}
