<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\Admin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdoptionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AppointmentController;

// User Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/adoption/form/{listingId}', [AdoptionController::class, 'create'])->name('adoption.form');
    Route::post('/adoption/submit', [AdoptionController::class, 'store'])->name('adoption.submit');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'updateInfo'])->name('profile.info');
    Route::put('/profile', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/notifications/read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/status', [AdoptionController::class, 'status'])->name('status');
    Route::get('/adoption/overview/{application}', [AdoptionController::class, 'overview'])->name('adoption.overview');
    Route::get('/applications/{application}', [AdoptionController::class, 'show'])->name('applications.show');
    
    

    // Appointment Routes
    Route::get('/appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])
        ->name('appointments.available-slots');
    Route::post('/applications/{application}/schedule', [AppointmentController::class, 'schedule'])
        ->name('applications.schedule');
         Route::post('/applications/{application}/cancel-appointment', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
         
         
});

// Listings Routes
Route::get('/', [ListingController::class, 'index'])->name('home');
Route::get('/explore', [ListingController::class, 'explore'])->name('explore');
Route::resource('listing', ListingController::class)->except('index');

// Admin Routes
Route::middleware(['auth', 'verified', Admin::class])
    ->group(function () {
        Route::get('/users', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
         Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/adoption-applications', [AdminController::class, 'adoptionApplications'])->name('admin.adoption.applications');
        Route::get('/users/{user}', [AdminController::class, 'show'])->name('user.show');
        Route::put('/admin/{user}/role', [AdminController::class, 'role'])->name('admin.role');
        Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.destroy');
       

        // Adoption application routes
        Route::get('/admin/adoption-applications/{application}', [AdminController::class, 'reviewApplication'])->name('admin.adoption.review');
        Route::put('/admin/adoption-applications/{application}/approve', [AdminController::class, 'approveApplication'])->name('admin.adoption.approve');
        Route::put('/admin/adoption-applications/{application}/disapprove', [AdminController::class, 'disapproveApplication'])->name('admin.adoption.disapprove');
        Route::put('/listing/{listing}/approve', [AdminController::class, 'approve'])->name('admin.approve');

        // Appointment routes
        Route::get('/admin/appointments', [AdminController::class, 'appointments'])->name('admin.appointments');
        Route::post('/admin/appointments/{id}/approve', [AdminController::class, 'approveAppointment'])->name('admin.appointments.approve');
        Route::post('/admin/appointments/{id}/disapprove', [AdminController::class, 'disapproveAppointment'])->name('admin.appointments.disapprove');
         Route::post('/admin/appointments/{appointment}/cancel', [AppointmentController::class, 'adminCancel'])->name('admin.appointments.cancel');


    });

require __DIR__ . '/auth.php';