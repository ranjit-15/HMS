<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\CalendarController as AdminCalendarController;
use App\Http\Controllers\Admin\CalendarClosureController;
use App\Http\Controllers\Admin\CalendarEventController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BorrowRequestController as AdminBorrowRequestController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Student\HiveController;
use App\Http\Controllers\Student\BookingController;
use App\Http\Controllers\Student\LibraryController;
use App\Http\Controllers\Student\BorrowRequestController;
use App\Http\Controllers\Student\CalendarController as StudentCalendarController;
use App\Http\Controllers\Student\ActivityController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\CheckInController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\WaitlistController;
use App\Http\Controllers\Student\FavoriteController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\BookReviewController as AdminBookReviewController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Student\BookReviewController;


Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('student.dashboard')
        : redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('login.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('login.google.callback');
Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'student'])->group(function () {
    // Dashboard with stats
    Route::get('/student/dashboard', [DashboardController::class, 'index'])->name('student.dashboard');

    Route::get('/student/hive', [HiveController::class, 'index'])->name('student.hive');
    Route::get('/student/hive/calendar-events', [HiveController::class, 'calendarEvents'])->name('student.hive.calendar-events');
    Route::get('/student/library', [LibraryController::class, 'index'])->name('student.library');
    Route::get('/student/library/{book}', [LibraryController::class, 'show'])->name('student.library.show');
    Route::get('/student/calendar', [StudentCalendarController::class, 'index'])->name('student.calendar');
    Route::get('/student/calendar/events', [StudentCalendarController::class, 'events'])->name('student.calendar.events');
    Route::get('/student/activity', [ActivityController::class, 'index'])->name('student.activity');
    Route::get('/student/notifications', [NotificationController::class, 'index'])->name('student.notifications');
    Route::post('/student/notifications/mark-read', [NotificationController::class, 'markAllRead'])->name('student.notifications.markAllRead');
    Route::post('/student/library/borrow', [BorrowRequestController::class, 'store'])->name('student.library.borrow');
    Route::post('/student/library/borrows/{borrow}/extend', [BorrowRequestController::class, 'extend'])->name('student.library.extend');
    Route::post('/student/library/borrows/{borrow}/return', [BorrowRequestController::class, 'return'])->name('student.library.return');
    Route::post('/student/library/waitlist/{book}', [WaitlistController::class, 'joinBook'])->name('student.library.waitlist');
    Route::post('/student/hive/book', [BookingController::class, 'store'])->name('student.hive.book');
    Route::post('/student/hive/waitlist/{table}', [WaitlistController::class, 'joinTable'])->name('student.hive.waitlist');
    Route::post('/student/hive/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('student.hive.confirm');
    Route::post('/student/hive/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('student.hive.cancel');
    Route::get('/check-in/{table}/{hash}', [CheckInController::class, '__invoke'])->name('checkin.process');

    // Profile routes
    Route::get('/student/profile', [ProfileController::class, 'show'])->name('student.profile');
    Route::post('/student/profile', [ProfileController::class, 'update'])->name('student.profile.update');
    Route::post('/student/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('student.profile.avatar');
    Route::delete('/student/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('student.profile.avatar.remove');

    // Favorites/Wishlist routes
    Route::get('/student/favorites', [FavoriteController::class, 'index'])->name('student.favorites');
    Route::post('/student/favorites/{book}/toggle', [FavoriteController::class, 'toggle'])->name('student.favorites.toggle');

    // Book Reviews
    Route::post('/student/library/{book}/review', [BookReviewController::class, 'store'])->name('student.library.review');
    Route::delete('/student/reviews/{review}', [BookReviewController::class, 'destroy'])->name('student.reviews.destroy');
});


Route::prefix('admin')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('login', [AuthController::class, 'login'])->name('admin.login.submit');
    });

    Route::middleware('admin')->group(function () {
        Route::get('/', [AuthController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');

        Route::resource('books', BookController::class)->names('admin.books')->except('show');
        Route::resource('tables', TableController::class)->names('admin.tables')->except('show');
        Route::post('tables/restore/{id}', [TableController::class, 'restore'])->name('admin.tables.restore');
        Route::get('bookings', [AdminBookingController::class, 'index'])->name('admin.bookings.index');
        Route::post('tables/{table}/end-booking', [TableController::class, 'endBooking'])->name('admin.tables.endBooking');
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('admin.analytics.index');
        Route::get('calendar', [AdminCalendarController::class, 'index'])->name('admin.calendar.index');
        Route::get('calendar/events', [AdminCalendarController::class, 'events'])->name('admin.calendar.events');
        Route::resource('closures', CalendarClosureController::class)->names('admin.closures')->except('show');
        Route::resource('events', CalendarEventController::class)->names('admin.events')->except('show');
        Route::get('settings', [SettingController::class, 'edit'])->name('admin.settings.edit');
        Route::post('settings', [SettingController::class, 'update'])->name('admin.settings.update');
        Route::get('borrows', [AdminBorrowRequestController::class, 'index'])->name('admin.borrows.index');
        Route::post('borrows/{borrow}/approve', [AdminBorrowRequestController::class, 'approve'])->name('admin.borrows.approve');
        Route::post('borrows/{borrow}/decline', [AdminBorrowRequestController::class, 'decline'])->name('admin.borrows.decline');
        Route::post('borrows/{borrow}/borrowed', [AdminBorrowRequestController::class, 'markBorrowed'])->name('admin.borrows.borrowed');
        Route::post('borrows/{borrow}/returned', [AdminBorrowRequestController::class, 'markReturned'])->name('admin.borrows.returned');
        Route::post('borrows/{borrow}/make-available', [AdminBorrowRequestController::class, 'makeAvailable'])->name('admin.borrows.makeAvailable');
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('admin.audit.index');

        // Book Reviews Management
        Route::get('reviews', [AdminBookReviewController::class, 'index'])->name('admin.reviews.index');
        Route::post('reviews/{review}/approve', [AdminBookReviewController::class, 'approve'])->name('admin.reviews.approve');
        Route::delete('reviews/{review}/reject', [AdminBookReviewController::class, 'reject'])->name('admin.reviews.reject');

        // User Management
        Route::get('users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
        Route::post('users/{user}/toggle-ban', [AdminUserController::class, 'toggleBan'])->name('admin.users.toggleBan');

        // Reports
        Route::get('reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::post('reports/borrowing', [ReportController::class, 'borrowingReport'])->name('admin.reports.borrowing');
        Route::post('reports/hive', [ReportController::class, 'hiveReport'])->name('admin.reports.hive');
        Route::get('reports/overdue', [ReportController::class, 'overdueReport'])->name('admin.reports.overdue');

        // Push Notifications
        Route::get('notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications.index');
        Route::post('notifications', [AdminNotificationController::class, 'store'])->name('admin.notifications.store');
        Route::delete('notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    });
});

// Development preview route for the B5 print template
Route::get('/print/b5', function () {
    return view('print.b5-template');
});
