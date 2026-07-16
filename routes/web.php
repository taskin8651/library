<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDash;
use App\Http\Controllers\Owner\DashboardController as OwnerDash;
use App\Http\Controllers\Owner\MemberController;
use App\Http\Controllers\Owner\FeeController;
use App\Http\Controllers\Owner\AttendanceController;
use App\Http\Controllers\Owner\SeatController;
use App\Http\Controllers\Owner\ShiftController;
use App\Http\Controllers\Owner\SubscriptionController;
use App\Http\Controllers\Owner\AnnouncementController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Student\DashboardController as StudentDash;

// ─── Public Routes ───────────────────────────────────────────
Route::get('/', fn() => view('landing'))->name('home');

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
    Route::post('/subscription/verify', [SubscriptionController::class, 'verifyPayment'])->name('owner.subscription.verify');

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
});
