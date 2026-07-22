<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDash;
use App\Http\Controllers\Admin\PaymentController as AdminPayment;
use App\Http\Controllers\Admin\SettingsController as AdminSettings;
use App\Http\Controllers\Owner\DashboardController as OwnerDash;
use App\Http\Controllers\Owner\MemberController;
use App\Http\Controllers\Owner\FeeController;
use App\Http\Controllers\Owner\AttendanceController;
use App\Http\Controllers\Owner\SeatController;
use App\Http\Controllers\Owner\ShiftController;
use App\Http\Controllers\Owner\SubscriptionController;
use App\Http\Controllers\Owner\AnnouncementController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Owner\ProfileController;
use App\Http\Controllers\Owner\SettingsController;
use App\Http\Controllers\Student\DashboardController as StudentDash;
use App\Http\Controllers\Student\ScanController;

// ─── Public Routes ───────────────────────────────────────────
Route::get('/', fn() => view('landing'))->name('home');

// SEO
Route::get('/robots.txt', fn() => response()->view('seo.robots')->header('Content-Type', 'text/plain'));
Route::get('/sitemap.xml', fn() => response()->view('seo.sitemap')->header('Content-Type', 'application/xml'));
Route::get('/manifest.json', fn() => response()->view('seo.manifest')->header('Content-Type', 'application/manifest+json'));

// Auth
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// QR Check-in (public - student scans this)
Route::get('/checkin/{slug}', [AttendanceController::class, 'checkInPage']);
Route::post('/checkin/process', [AttendanceController::class, 'processCheckIn'])->name('checkin.process');

// ─── Super Admin Routes ───────────────────────────────────────
Route::prefix('admin')->middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/dashboard', [AdminDash::class, 'index'])->name('admin.dashboard');
    Route::get('/libraries', [AdminDash::class, 'libraries'])->name('admin.libraries');
    Route::post('/libraries/{library}/approve', [AdminDash::class, 'approveLibrary'])->name('admin.libraries.approve');
    Route::post('/libraries/{library}/suspend', [AdminDash::class, 'suspendLibrary'])->name('admin.libraries.suspend');
    Route::get('/plans', [AdminDash::class, 'plans'])->name('admin.plans');
    Route::put('/plans/{plan}', [AdminDash::class, 'updatePlan'])->name('admin.plans.update');
    Route::get('/payments', [AdminPayment::class, 'index'])->name('admin.payments.index');
    Route::post('/payments/{subscription}/approve', [AdminPayment::class, 'approve'])->name('admin.payments.approve');
    Route::post('/payments/{subscription}/reject', [AdminPayment::class, 'reject'])->name('admin.payments.reject');
    Route::get('/settings', [AdminSettings::class, 'edit'])->name('admin.settings.edit');
    Route::put('/settings', [AdminSettings::class, 'update'])->name('admin.settings.update');
});

// ─── Library Owner Routes ─────────────────────────────────────
Route::prefix('owner')->middleware(['auth', 'role:owner,staff'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [OwnerDash::class, 'index'])->name('owner.dashboard');

    // Members
    Route::get('/members', [MemberController::class, 'index'])->name('owner.members.index');
    Route::get('/members/create', [MemberController::class, 'create'])->name('owner.members.create');
    Route::post('/members', [MemberController::class, 'store'])->name('owner.members.store');
    Route::get('/members/{member}', [MemberController::class, 'show'])->name('owner.members.show');
    Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->name('owner.members.edit');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('owner.members.update');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('owner.members.destroy');

    // Fees
    Route::get('/fees', [FeeController::class, 'index'])->name('owner.fees.index');
    Route::get('/fees/collect', [FeeController::class, 'create'])->name('owner.fees.create');
    Route::post('/fees', [FeeController::class, 'store'])->name('owner.fees.store');
    Route::get('/fees/{payment}/receipt', [FeeController::class, 'receipt'])->name('owner.fees.receipt');
    Route::get('/fees/{payment}/download', [FeeController::class, 'downloadReceipt'])->name('owner.fees.download');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('owner.attendance.index');
    Route::get('/attendance/qr', [AttendanceController::class, 'qrCode'])->name('owner.attendance.qr');

    // Seats
    Route::get('/seats', [SeatController::class, 'index'])->name('owner.seats.index');
    Route::post('/seats', [SeatController::class, 'store'])->name('owner.seats.store');
    Route::delete('/seats/{seat}', [SeatController::class, 'destroy'])->name('owner.seats.destroy');
    Route::post('/seats/{seat}/toggle', [SeatController::class, 'toggle'])->name('owner.seats.toggle');
    Route::post('/seats/{seat}/status', [SeatController::class, 'setStatus'])->name('owner.seats.status');

    // Shifts
    Route::get('/shifts', [ShiftController::class, 'index'])->name('owner.shifts.index');
    Route::post('/shifts', [ShiftController::class, 'store'])->name('owner.shifts.store');
    Route::put('/shifts/{shift}', [ShiftController::class, 'update'])->name('owner.shifts.update');
    Route::post('/shifts/{shift}/toggle', [ShiftController::class, 'toggle'])->name('owner.shifts.toggle');
    Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy'])->name('owner.shifts.destroy');

    // Subscription
    Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])->name('owner.subscription.plans');
    Route::post('/subscription/order', [SubscriptionController::class, 'createOrder'])->name('owner.subscription.order');
    Route::post('/subscription/submit-utr', [SubscriptionController::class, 'submitUtr'])->name('owner.subscription.submit-utr');
    Route::post('/subscription/razorpay/order', [SubscriptionController::class, 'createRazorpayOrder'])->name('owner.subscription.razorpay.order');
    Route::post('/subscription/razorpay/verify', [SubscriptionController::class, 'verifyRazorpayPayment'])->name('owner.subscription.razorpay.verify');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('owner.profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('owner.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('owner.profile.password');

    // Library Settings
    Route::get('/settings', [SettingsController::class, 'edit'])->name('owner.settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('owner.settings.update');

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('owner.announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('owner.announcements.store');
    Route::post('/announcements/{announcement}/toggle', [AnnouncementController::class, 'toggle'])->name('owner.announcements.toggle');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('owner.announcements.destroy');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('owner.reports.index');
    Route::get('/reports/fees/export', [ReportController::class, 'exportFees'])->name('owner.reports.fees.export');
    Route::get('/reports/attendance/export', [ReportController::class, 'exportAttendance'])->name('owner.reports.attendance.export');
    Route::get('/reports/members/export', [ReportController::class, 'exportMembers'])->name('owner.reports.members.export');
});

// ─── Student Routes ───────────────────────────────────────────
Route::prefix('student')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [StudentDash::class, 'index'])->name('student.dashboard');
    Route::get('/scan', [ScanController::class, 'page'])->name('student.scan');
    Route::post('/scan/checkin', [ScanController::class, 'checkin'])->name('student.scan.checkin');
});
