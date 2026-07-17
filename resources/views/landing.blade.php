<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        $seoTitle = 'LibraryCRM – Smart Library Management Software for India\'s Study Libraries';
        $seoDesc  = 'LibraryCRM helps study library owners manage members, collect fees, track seats and run QR-based attendance — all from one dashboard. 14-day free trial, no credit card needed.';
        $seoUrl   = url('/');
        $seoImage = asset('images/og-image.png');
    @endphp

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDesc }}">
    <meta name="keywords" content="library management software, study library software India, library CRM, library attendance QR code, library fee collection software, coaching library management, reading room management software, library seat booking software">
    <meta name="author" content="LibraryCRM">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="theme-color" content="#667eea">
    <link rel="canonical" href="{{ $seoUrl }}">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:site_name" content="LibraryCRM">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDesc }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="LibraryCRM — Smart Library Management Software">
    <meta property="og:locale" content="en_IN">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDesc }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    <!-- Favicons / App Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="LibraryCRM">

    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "LibraryCRM",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web, Android, iOS",
        "description": {!! json_encode($seoDesc) !!},
        "url": {!! json_encode($seoUrl) !!},
        "image": {!! json_encode($seoImage) !!},
        "offers": {
            "@type": "Offer",
            "price": "399",
            "priceCurrency": "INR",
            "description": "Starter plan, 14-day free trial"
        }
    }
    </script>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "LibraryCRM",
        "url": {!! json_encode($seoUrl) !!},
        "logo": {!! json_encode(asset('images/icon-512.png')) !!}
    }
    </script>

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Landing CSS -->
    <link href="{{ asset('assets/css/landing.css') }}" rel="stylesheet">
</head>
<body>

<!-- ============================================================
     PAGE LOADER
     ============================================================ -->
<div id="page-loader">
    <div>
        <div class="loader-ring"></div>
        <div class="loader-text">LibraryCRM</div>
    </div>
</div>

<!-- ============================================================
     CURSOR GLOW (desktop)
     ============================================================ -->
<div id="cursor-glow"></div>

<!-- ============================================================
     NAVBAR
     ============================================================ -->
<nav class="navbar navbar-expand-lg fixed-top" id="navbar">
    <div class="container">

        <a class="navbar-brand" href="/">
            <span class="brand-icon"><i class="bi bi-book-fill text-white"></i></span>
            LibraryCRM
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <i class="bi bi-list text-white fs-5"></i>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="#how">How it Works</a></li>
                <li class="nav-item"><a class="nav-link" href="#pricing">Pricing</a></li>
                <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
            </ul>
            <div class="d-flex gap-2 mt-3 mt-lg-0 align-items-center">
                <a href="/login"    class="btn-nav-login">Login</a>
                <a href="/register" class="btn-nav-trial">
                    <i class="bi bi-rocket-takeoff me-1"></i>Free Trial
                </a>
            </div>
        </div>

    </div>
</nav>

<!-- ============================================================
     HERO
     ============================================================ -->
<section class="hero" id="home">
    <div class="hero-blob-1"></div>
    <div class="hero-blob-2"></div>
    <div class="hero-blob-3"></div>

    <div class="container position-relative" style="z-index:2">
        <div class="row align-items-center g-5">

            <!-- Left Text -->
            <div class="col-lg-6">
                <div class="hero-badge">
                    <i class="bi bi-stars"></i>
                    Built for India's Study Libraries
                </div>

                <h1 class="mb-4">
                    Manage Your<br>Library <span class="grad-text">Smarter</span>
                </h1>

                <p class="hero-sub mb-5">
                    Say goodbye to paper registers. Track students, collect fees, and
                    manage seats — all from one place, right from your phone.
                </p>

                <div class="hero-ctas d-flex gap-3 flex-wrap mb-4">
                    <a href="/register" class="btn-hero-primary">
                        <i class="bi bi-rocket-takeoff"></i>
                        Start Free Trial
                    </a>
                    <a href="/login" class="btn-hero-secondary">
                        <i class="bi bi-play-circle"></i>
                        View Demo
                    </a>
                    <button type="button" id="installAppBtn" data-pwa-install-btn class="btn-hero-secondary d-none">
                        <i class="bi bi-download"></i>
                        Install Student App
                    </button>
                </div>

                <div class="hero-trust">
                    <i class="bi bi-shield-check" style="color:#4ade80"></i>
                    14-day free trial
                    <span class="dot"></span>
                    No credit card needed
                    <span class="dot"></span>
                    Setup in 5 mins
                </div>
            </div>

            <!-- Right — Dashboard Preview Card -->
            <div class="col-lg-6">
                <div class="hero-card">

                    <div class="hero-card-bar">
                        <div class="dot bg-danger"></div>
                        <div class="dot bg-warning"></div>
                        <div class="dot bg-success"></div>
                        <span>LibraryCRM Dashboard</span>
                    </div>

                    <div class="hero-card-body">

                        <div class="hero-card-toprow">
                            <span class="hero-card-badge"><span class="pulse-mini"></span>Live</span>
                            <span class="hero-card-period">Today, {{ now()->format('d M') }}</span>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="stat-mini">
                                    <div class="stat-mini-icon" style="background:rgba(102,126,234,.18);color:#a5b4fc"><i class="bi bi-people-fill"></i></div>
                                    <div class="label">Active Members</div>
                                    <div class="val">142</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-mini">
                                    <div class="stat-mini-icon" style="background:rgba(74,222,128,.15);color:#4ade80"><i class="bi bi-cash-stack"></i></div>
                                    <div class="label">Today's Revenue</div>
                                    <div class="val" style="color:#4ade80">₹4,800</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-mini">
                                    <div class="stat-mini-icon" style="background:rgba(56,189,248,.15);color:#38bdf8"><i class="bi bi-door-open-fill"></i></div>
                                    <div class="label">Currently In</div>
                                    <div class="val">38</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-mini">
                                    <div class="stat-mini-icon" style="background:rgba(251,146,60,.15);color:#fb923c"><i class="bi bi-exclamation-triangle-fill"></i></div>
                                    <div class="label">Expiring Soon</div>
                                    <div class="val" style="color:#fb923c">12</div>
                                </div>
                            </div>
                        </div>

                        <!-- Weekly trend mini chart -->
                        <div class="mini-chart">
                            <div class="mini-chart-head">
                                <span>Check-ins This Week</span>
                                <span class="mini-chart-trend"><i class="bi bi-graph-up-arrow"></i>+12%</span>
                            </div>
                            <div class="mini-chart-bars">
                                <span class="bar" style="--h:42%;animation-delay:.05s"></span>
                                <span class="bar" style="--h:58%;animation-delay:.12s"></span>
                                <span class="bar" style="--h:50%;animation-delay:.19s"></span>
                                <span class="bar" style="--h:72%;animation-delay:.26s"></span>
                                <span class="bar" style="--h:64%;animation-delay:.33s"></span>
                                <span class="bar" style="--h:88%;animation-delay:.40s"></span>
                                <span class="bar" style="--h:100%;animation-delay:.47s"></span>
                            </div>
                            <div class="mini-chart-labels">
                                <span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span><span>S</span>
                            </div>
                        </div>

                        <div class="live-box">
                            <div class="live-avatar">R</div>
                            <div>
                                <div class="live-name">Rahul Kumar checked in ✓</div>
                                <div class="live-meta">Seat A3 &nbsp;•&nbsp; Morning Shift &nbsp;•&nbsp; just now</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     STATS BAND
     ============================================================ -->
<section class="stats-band">
    <div class="container">
        <div class="row align-items-center justify-content-center g-4">

            <div class="col-6 col-md-3 reveal delay-1">
                <div class="stat-item">
                    <div class="num" data-target="500">0+</div>
                    <div class="lbl">Libraries Using It</div>
                </div>
            </div>

            <div class="col-auto d-none d-md-block"><div class="stat-divider"></div></div>

            <div class="col-6 col-md-3 reveal delay-2">
                <div class="stat-item">
                    <div class="num" data-target="50000">0+</div>
                    <div class="lbl">Students Managed</div>
                </div>
            </div>

            <div class="col-auto d-none d-md-block"><div class="stat-divider"></div></div>

            <div class="col-6 col-md-3 reveal delay-3">
                <div class="stat-item">
                    <div class="num">₹2Cr+</div>
                    <div class="lbl">Fees Collected</div>
                </div>
            </div>

            <div class="col-auto d-none d-md-block"><div class="stat-divider"></div></div>

            <div class="col-6 col-md-3 reveal delay-4">
                <div class="stat-item">
                    <div class="num">99.9%</div>
                    <div class="lbl">Uptime Guarantee</div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     FEATURES
     ============================================================ -->
<section class="features-section" id="features">
    <div class="container">

        <div class="text-center mb-5 reveal">
            <div class="section-tag">Features</div>
            <h2 class="section-title mt-2">Everything You Need</h2>
            <p class="section-sub mx-auto mt-3">All tools built specifically for study libraries</p>
        </div>

        <div class="row g-4">

            <div class="col-md-4 reveal delay-1">
                <div class="feature-card">
                    <div class="feat-icon" style="background:#dbeafe;color:#1d4ed8">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="feat-title">Member Management</div>
                    <p class="feat-desc">Add students, assign seats &amp; shifts. Every member gets an auto-generated UID. Track plan dates with ease.</p>
                </div>
            </div>

            <div class="col-md-4 reveal delay-2">
                <div class="feature-card">
                    <div class="feat-icon" style="background:#dcfce7;color:#166534">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="feat-title">Fee Collection</div>
                    <p class="feat-desc">Record both cash and UPI payments. Digital receipts are generated automatically. View your monthly collection charts.</p>
                </div>
            </div>

            <div class="col-md-4 reveal delay-3">
                <div class="feature-card">
                    <div class="feat-icon" style="background:#fef3c7;color:#92400e">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <div class="feat-title">QR Check-in</div>
                    <p class="feat-desc">Students scan a QR code to check in/out. No app needed — it works right in the phone's browser.</p>
                </div>
            </div>

            <div class="col-md-4 reveal delay-1">
                <div class="feature-card">
                    <div class="feat-icon" style="background:#fce7f3;color:#9d174d">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </div>
                    <div class="feat-title">Seat Layout</div>
                    <p class="feat-desc">Visual seat map with rows, cabins, and VIP sections. See which seats are available in real time.</p>
                </div>
            </div>

            <div class="col-md-4 reveal delay-2">
                <div class="feature-card">
                    <div class="feat-icon" style="background:#ede9fe;color:#6d28d9">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <div class="feat-title">PDF Receipts</div>
                    <p class="feat-desc">Add your own logo &amp; stamp. Students can download or print professional receipts instantly.</p>
                </div>
            </div>

            <div class="col-md-4 reveal delay-3">
                <div class="feature-card">
                    <div class="feat-icon" style="background:#ffedd5;color:#c2410c">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <div class="feat-title">Expiry Alerts</div>
                    <p class="feat-desc">See members expiring this week. Never miss a renewal. Improve your cash flow visibility.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     HOW IT WORKS
     ============================================================ -->
<section class="how-section" id="how">
    <div class="container">
        <div class="row align-items-center g-5">

            <!-- Steps -->
            <div class="col-lg-6 reveal-left">
                <div class="section-tag">How It Works</div>
                <h2 class="section-title mt-2 mb-4">Get Started<br>in 5 Minutes</h2>

                <div class="step-item">
                    <div class="step-num">1</div>
                    <div>
                        <div class="fw-700 mb-1" style="color:#1a1d23">Register</div>
                        <div style="color:#6b7280;font-size:14px">Create a free account with your library name, slug, and email. Your 14-day trial starts right away.</div>
                    </div>
                </div>
                <div class="step-connector"></div>

                <div class="step-item">
                    <div class="step-num">2</div>
                    <div>
                        <div class="fw-700 mb-1" style="color:#1a1d23">Set Up</div>
                        <div style="color:#6b7280;font-size:14px">Add seats, create shifts, and upload your library logo.</div>
                    </div>
                </div>
                <div class="step-connector"></div>

                <div class="step-item">
                    <div class="step-num">3</div>
                    <div>
                        <div class="fw-700 mb-1" style="color:#1a1d23">Add Members</div>
                        <div style="color:#6b7280;font-size:14px">Fill in student details. A UID is auto-generated, and the password defaults to their phone number.</div>
                    </div>
                </div>
                <div class="step-connector"></div>

                <div class="step-item">
                    <div class="step-num">4</div>
                    <div>
                        <div class="fw-700 mb-1" style="color:#1a1d23">Put Up the QR Code</div>
                        <div style="color:#6b7280;font-size:14px">Print the QR code at your entrance. Students scan it to check in/out — automatic attendance!</div>
                    </div>
                </div>
            </div>

            <!-- Phone Mockup -->
            <div class="col-lg-6 reveal-right">
                <div class="phone-mockup-wrap">
                    <div class="phone-glow"></div>

                    <div class="phone-mockup">
                        <div class="phone-float-chip"><i class="bi bi-lightning-charge-fill"></i>Instant Sync</div>

                        <div class="phone-screen">
                            <div class="phone-notch"></div>
                            <div class="phone-status-bar">
                                <span>9:41</span>
                                <span class="icons"><i class="bi bi-reception-4"></i><i class="bi bi-wifi"></i><i class="bi bi-battery-full"></i></span>
                            </div>

                            <div class="phone-stat success">
                                <div class="check-badge"><i class="bi bi-check-lg"></i></div>
                                <div>
                                    <div class="ps-label">Check-in Successful</div>
                                    <div class="ps-val" style="color:#4ade80">Welcome!</div>
                                </div>
                            </div>

                            <div class="phone-stat">
                                <div class="ps-label">Member</div>
                                <div class="ps-val" style="font-size:16px;color:#fff">Rahul Kumar</div>
                            </div>

                            <div class="row g-2 mt-1">
                                <div class="col-6">
                                    <div class="phone-stat">
                                        <div class="ps-label">Seat</div>
                                        <div class="ps-val" style="font-size:18px;color:#667eea">A3</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="phone-stat">
                                        <div class="ps-label">Days Left</div>
                                        <div class="ps-val" style="font-size:18px;color:#fb923c">8</div>
                                    </div>
                                </div>
                            </div>

                            <div class="phone-qr">
                                <div class="qr-icon-wrap">
                                    <i class="bi bi-qr-code"></i>
                                    <div class="qr-scanline"></div>
                                </div>
                                <div style="color:rgba(255,255,255,.4);font-size:10px;margin-top:10px">Scan to Check In</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     STUDENT APP
     ============================================================ -->
<section class="features-section" id="student-app">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 reveal-left">
                <div class="section-tag">For Students</div>
                <h2 class="section-title mt-2 mb-3">Your Attendance,<br>Right on Your Phone</h2>
                <p class="section-sub mb-4">No paper registers, no queues. Students install LibraryCRM like an app on their phone and mark attendance in two taps.</p>

                <div class="step-item">
                    <div class="step-num">1</div>
                    <div>
                        <div class="fw-700 mb-1" style="color:#1a1d23">Install the App</div>
                        <div style="color:#6b7280;font-size:14px">Tap "Install Student App" below (or "Add to Home Screen" from your browser menu) — no Play Store needed.</div>
                    </div>
                </div>
                <div class="step-connector"></div>

                <div class="step-item">
                    <div class="step-num">2</div>
                    <div>
                        <div class="fw-700 mb-1" style="color:#1a1d23">Log In</div>
                        <div style="color:#6b7280;font-size:14px">Sign in with the ID and password your library owner gave you.</div>
                    </div>
                </div>
                <div class="step-connector"></div>

                <div class="step-item">
                    <div class="step-num">3</div>
                    <div>
                        <div class="fw-700 mb-1" style="color:#1a1d23">Scan &amp; Go</div>
                        <div style="color:#6b7280;font-size:14px">Scan the QR code at your library's entrance with the in-app scanner to check in or out — instantly.</div>
                    </div>
                </div>
                <div class="step-connector"></div>

                <div class="step-item">
                    <div class="step-num">4</div>
                    <div>
                        <div class="fw-700 mb-1" style="color:#1a1d23">Track Everything</div>
                        <div style="color:#6b7280;font-size:14px">See your seat, shift, days left on your plan, and full attendance history — all on your phone.</div>
                    </div>
                </div>

                <button type="button" id="installAppBtnSecondary" data-pwa-install-btn class="btn-hero-primary mt-4 d-none">
                    <i class="bi bi-download"></i>
                    Install Student App
                </button>
            </div>

            <div class="col-lg-6 reveal-right">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <div class="feature-card mini-feature-card h-100">
                            <div class="feat-icon" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-phone-fill"></i></div>
                            <div class="feat-title">Install Like an App</div>
                            <p class="feat-desc">Add to your home screen — opens full-screen, no browser bars.</p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="feature-card mini-feature-card h-100">
                            <div class="feat-icon" style="background:#dcfce7;color:#166534"><i class="bi bi-shield-lock-fill"></i></div>
                            <div class="feat-title">Secure Login</div>
                            <p class="feat-desc">Only you can mark your own attendance — no UID typing needed.</p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="feature-card mini-feature-card h-100">
                            <div class="feat-icon" style="background:#fef3c7;color:#92400e"><i class="bi bi-qr-code-scan"></i></div>
                            <div class="feat-title">Camera QR Scan</div>
                            <p class="feat-desc">Point your camera at the entrance QR — check-in happens automatically.</p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="feature-card mini-feature-card h-100">
                            <div class="feat-icon" style="background:#ede9fe;color:#6d28d9"><i class="bi bi-graph-up"></i></div>
                            <div class="feat-title">Live History</div>
                            <p class="feat-desc">Every check-in/out and plan expiry, visible anytime.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     PRICING
     ============================================================ -->
<section class="pricing-section" id="pricing">
    <div class="container">

        <div class="text-center mb-5 reveal">
            <div class="section-tag">Pricing</div>
            <h2 class="section-title mt-2">Simple, Transparent Pricing</h2>
            <p class="section-sub mx-auto mt-3">14-day free trial — no credit card required</p>
        </div>

        <div class="row justify-content-center g-4 align-items-stretch">

            <!-- Starter -->
            <div class="col-md-5 reveal delay-1">
                <div class="pricing-card popular">
                    <div class="popular-chip">⚡ Available Now</div>
                    <div class="plan-name" style="color:#667eea">Starter</div>
                    <div class="plan-price mb-2"><sup>₹</sup>399<span class="per">/mo</span></div>
                    <p style="color:#6b7280;font-size:13px;margin-bottom:24px">Perfect for small libraries</p>
                    <div class="plan-feature"><span class="fi fi-yes"><i class="bi bi-check"></i></span>1 Library Branch</div>
                    <div class="plan-feature"><span class="fi fi-yes"><i class="bi bi-check"></i></span>Unlimited Members</div>
                    <div class="plan-feature"><span class="fi fi-yes"><i class="bi bi-check"></i></span>QR Check-in</div>
                    <div class="plan-feature"><span class="fi fi-yes"><i class="bi bi-check"></i></span>Fee Receipts (PDF)</div>
                    <div class="plan-feature"><span class="fi fi-no"><i class="bi bi-x"></i></span><span style="color:#9ca3af">Staff Accounts</span></div>
                    <div class="plan-feature"><span class="fi fi-no"><i class="bi bi-x"></i></span><span style="color:#9ca3af">White Label</span></div>
                    <a href="/register?plan=1" class="btn-plan btn-plan-fill">Start Free Trial</a>
                </div>
            </div>

        </div>

        <p class="text-center mt-5" style="color:#9ca3af;font-size:13px">
            More plans (Pro &amp; Premium) coming soon.
        </p>
    </div>
</section>

<!-- ============================================================
     TESTIMONIALS
     ============================================================ -->
<section class="testi-section">
    <div class="container">

        <div class="text-center mb-5 reveal">
            <div class="section-tag">Testimonials</div>
            <h2 class="section-title mt-2">What Library Owners Say</h2>
        </div>

        <div class="row g-4">

            <div class="col-md-4 reveal delay-1">
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-text">"Earlier we wrote everything in paper registers. Now it's all digital — fees, attendance, seats, everything in one place. Saves so much time!"</p>
                    <div class="testi-author">
                        <div class="testi-avatar">R</div>
                        <div>
                            <div class="testi-name">Rajesh Kumar</div>
                            <div class="testi-loc">📍 Patna, Bihar</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 reveal delay-2">
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-text">"The QR check-in feature is the best! Students scan it themselves — we don't have to do anything. Attendance happens automatically."</p>
                    <div class="testi-author">
                        <div class="testi-avatar">S</div>
                        <div>
                            <div class="testi-name">Sunita Devi</div>
                            <div class="testi-loc">📍 Muzaffarpur, Bihar</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 reveal delay-3">
                <div class="testi-card">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-text">"Students are really happy with the PDF receipt feature — it looks professional. Fee collection has gotten faster too. Highly recommended!"</p>
                    <div class="testi-author">
                        <div class="testi-avatar">A</div>
                        <div>
                            <div class="testi-name">Amit Singh</div>
                            <div class="testi-loc">📍 Bhagalpur, Bihar</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     FAQ
     ============================================================ -->
<section class="faq-section" id="faq">
    <div class="container">

        <div class="text-center mb-5 reveal">
            <div class="section-tag">FAQ</div>
            <h2 class="section-title mt-2">Frequently Asked Questions</h2>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-7 reveal">

                <div class="faq-item">
                    <div class="faq-q">What do I get in the free trial? <span class="faq-icon"><i class="bi bi-plus"></i></span></div>
                    <div class="faq-a">All features are free for 14 days. No credit card required. Choose a paid plan after the trial ends.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-q">Do students need to install an app? <span class="faq-icon"><i class="bi bi-plus"></i></span></div>
                    <div class="faq-a">Not at all! Students just scan the QR code. The check-in page opens right in their phone's browser — no app download needed.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-q">Can I add my own logo and branding? <span class="faq-icon"><i class="bi bi-plus"></i></span></div>
                    <div class="faq-a">Yes! On every plan you can add your logo, name, tagline, and receipt stamp. The Premium plan gets full white-label branding.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-q">Is my data secure? <span class="faq-icon"><i class="bi bi-plus"></i></span></div>
                    <div class="faq-a">Absolutely. Each library gets its own isolated data space, with daily backups. Student data stays private and is never shared.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-q">Can I manage multiple branches? <span class="faq-icon"><i class="bi bi-plus"></i></span></div>
                    <div class="faq-a">The Pro plan supports 3 branches, and Premium supports unlimited branches. Each branch's data stays completely separate.</div>
                </div>

                <div class="faq-item">
                    <div class="faq-q">How do I make payments? <span class="faq-icon"><i class="bi bi-plus"></i></span></div>
                    <div class="faq-a">Everything is accepted through Razorpay — UPI, Credit/Debit Card, Net Banking. Secure checkout guaranteed.</div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     CTA
     ============================================================ -->
<section class="cta-section">
    <div class="container">
        <div class="reveal">
            <h2 class="cta-title">Take Your Library<br>Digital Today</h2>
            <p class="cta-sub">Thousands of library owners have already gone digital. It's your turn now.</p>
            <a href="/register" class="btn-cta">
                <i class="bi bi-rocket-takeoff" style="font-size:18px"></i>
                Start Your Free Trial — Today
            </a>
            <p class="cta-note mt-3">14 days free &nbsp;•&nbsp; No credit card &nbsp;•&nbsp; Cancel anytime</p>
        </div>
    </div>
</section>

<!-- ============================================================
     FOOTER
     ============================================================ -->
<footer>
    <div class="container">
        <div class="row g-4">

            <div class="col-md-4">
                <div class="footer-brand">
                    <i class="bi bi-book-fill me-2" style="color:#667eea"></i>LibraryCRM
                </div>
                <p class="footer-desc mt-2">Smart management software built for India's study libraries.</p>
            </div>

            <div class="col-6 col-md-2 offset-md-2">
                <div class="footer-heading">Product</div>
                <a href="#features" class="footer-link">Features</a>
                <a href="#pricing"  class="footer-link">Pricing</a>
                <a href="#how"      class="footer-link">How it Works</a>
                <a href="#faq"      class="footer-link">FAQ</a>
            </div>

            <div class="col-6 col-md-2">
                <div class="footer-heading">Account</div>
                <a href="/login"    class="footer-link">Login</a>
                <a href="/register" class="footer-link">Register</a>
                <a href="/register" class="footer-link">Free Trial</a>
            </div>

            <div class="col-6 col-md-2">
                <div class="footer-heading">Legal</div>
                <a href="#" class="footer-link">Privacy Policy</a>
                <a href="#" class="footer-link">Terms of Service</a>
                <a href="#" class="footer-link">Refund Policy</a>
            </div>

        </div>

        <hr class="footer-divider">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <p class="footer-copy mb-0">© {{ date('Y') }} LibraryCRM. All rights reserved.</p>
            <p class="footer-copy mb-0">Made with ❤️ for Bihar's Libraries</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Landing JS -->
<script src="{{ asset('assets/js/landing.js') }}"></script>
<!-- PWA install (Student App) -->
<script src="{{ asset('assets/js/pwa-install.js') }}"></script>

</body>
</html>
