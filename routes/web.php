<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\PresenceController as AdminPresenceController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\Admin\PayrollController as AdminPayrollController;
use App\Http\Controllers\Admin\SurveyController as AdminSurveyController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\GeolocationZoneController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\PresenceController as EmployeePresenceController;
use App\Http\Controllers\Employee\TaskController as EmployeeTaskController;
use App\Http\Controllers\Employee\LeaveController as EmployeeLeaveController;
use App\Http\Controllers\Employee\PayrollController as EmployeePayrollController;
use App\Http\Controllers\Employee\SurveyController as EmployeeSurveyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Redirection après login selon le rôle
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('employee.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [AdminDashboardController::class, 'getStats'])->name('stats');
    Route::get('/dashboard/activity', [AdminDashboardController::class, 'getRecentActivity'])->name('dashboard.activity');
    Route::get('/dashboard/alerts', [AdminDashboardController::class, 'getAlertsData'])->name('dashboard.alerts');
    Route::get('/dashboard/calendar', [AdminDashboardController::class, 'getCalendarEventsData'])->name('dashboard.calendar');

    // Gestion des employés
    Route::resource('employees', EmployeeController::class);

    // Gestion des présences
    Route::get('/presences', [AdminPresenceController::class, 'index'])->name('presences.index');
    Route::get('/presences/export/csv', [AdminPresenceController::class, 'exportCsv'])->name('presences.export.csv');
    Route::get('/presences/export/pdf', [AdminPresenceController::class, 'exportPdf'])->name('presences.export.pdf');
    Route::get('/presences/export/excel', [AdminPresenceController::class, 'exportExcel'])->name('presences.export.excel');

    // Gestion des tâches
    Route::get('/tasks', [AdminTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [AdminTaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [AdminTaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [AdminTaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [AdminTaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [AdminTaskController::class, 'update'])->name('tasks.update');
    Route::post('/tasks/{task}/approve', [AdminTaskController::class, 'approve'])->name('tasks.approve');
    Route::post('/tasks/{task}/reject', [AdminTaskController::class, 'reject'])->name('tasks.reject');
    Route::post('/tasks/{task}/validate', [AdminTaskController::class, 'validate'])->name('tasks.validate');
    Route::delete('/tasks/{task}', [AdminTaskController::class, 'destroy'])->name('tasks.destroy');

    // Gestion des congés
    Route::get('/leaves', [AdminLeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/{leave}', [AdminLeaveController::class, 'show'])->name('leaves.show');
    Route::post('/leaves/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('leaves.reject');
    Route::delete('/leaves/{leave}', [AdminLeaveController::class, 'destroy'])->name('leaves.destroy');

    // Gestion des fiches de paie
    Route::resource('payrolls', AdminPayrollController::class);
    Route::get('/payrolls/{payroll}/download', [AdminPayrollController::class, 'downloadPdf'])->name('payrolls.download');
    Route::post('/payrolls/{payroll}/mark-paid', [AdminPayrollController::class, 'markAsPaid'])->name('payrolls.mark-paid');

    // Gestion des sondages
    Route::resource('surveys', AdminSurveyController::class)->except(['edit', 'update']);
    Route::get('/surveys/{survey}/results', [AdminSurveyController::class, 'results'])->name('surveys.results');
    Route::post('/surveys/{survey}/toggle', [AdminSurveyController::class, 'toggle'])->name('surveys.toggle');

    // Gestion des départements (API pour sélecteurs)
    Route::get('/departments/{department}/positions', [DepartmentController::class, 'getPositions'])->name('departments.positions');

    // Gestion des zones de géolocalisation
    Route::resource('geolocation-zones', GeolocationZoneController::class);
    Route::patch('/geolocation-zones/{geolocation_zone}/set-default', [GeolocationZoneController::class, 'setDefault'])->name('geolocation-zones.set-default');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [AnalyticsController::class, 'getData'])->name('analytics.data');

    // Paramètres
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Départements (dans settings)
    Route::post('/settings/departments', [SettingsController::class, 'storeDepartment'])->name('settings.departments.store');
    Route::put('/settings/departments/{department}', [SettingsController::class, 'updateDepartment'])->name('settings.departments.update');
    Route::delete('/settings/departments/{department}', [SettingsController::class, 'destroyDepartment'])->name('settings.departments.destroy');

    // Postes (dans settings)
    Route::post('/settings/positions', [SettingsController::class, 'storePosition'])->name('settings.positions.store');
    Route::put('/settings/positions/{position}', [SettingsController::class, 'updatePosition'])->name('settings.positions.update');
    Route::delete('/settings/positions/{position}', [SettingsController::class, 'destroyPosition'])->name('settings.positions.destroy');

    // Gestion de la messagerie (admin)
    Route::get('/messaging', [\App\Http\Controllers\Admin\MessagingController::class, 'index'])->name('messaging.index');
    Route::post('/messaging', [\App\Http\Controllers\Admin\MessagingController::class, 'store'])->name('messaging.store');
    Route::get('/messaging/{conversation}', [\App\Http\Controllers\Admin\MessagingController::class, 'show'])->name('messaging.show');
    Route::put('/messaging/{conversation}', [\App\Http\Controllers\Admin\MessagingController::class, 'update'])->name('messaging.update');
    Route::delete('/messaging/{conversation}', [\App\Http\Controllers\Admin\MessagingController::class, 'destroy'])->name('messaging.destroy');
    Route::post('/messaging/{conversation}/participants', [\App\Http\Controllers\Admin\MessagingController::class, 'addParticipant'])->name('messaging.participants.add');
    Route::delete('/messaging/{conversation}/participants/{user}', [\App\Http\Controllers\Admin\MessagingController::class, 'removeParticipant'])->name('messaging.participants.remove');
    Route::delete('/messaging/messages/{message}', [\App\Http\Controllers\Admin\MessagingController::class, 'deleteMessage'])->name('messaging.message.delete');
});

// Routes Employee
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [EmployeeDashboardController::class, 'getChartDataApi'])->name('dashboard.chart-data');
    Route::get('/dashboard/events', [EmployeeDashboardController::class, 'getUpcomingEventsApi'])->name('dashboard.events');
    Route::post('/notifications/{id}/read', [EmployeeDashboardController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [EmployeeDashboardController::class, 'markAllNotificationsAsRead'])->name('notifications.read-all');

    // Présences
    Route::get('/presences', [EmployeePresenceController::class, 'index'])->name('presences.index');
    Route::post('/presences/check-in', [EmployeePresenceController::class, 'checkIn'])->name('presences.check-in');
    Route::post('/presences/check-out', [EmployeePresenceController::class, 'checkOut'])->name('presences.check-out');

    // Tâches (assignées par l'admin, l'employé peut seulement voir et mettre à jour la progression)
    Route::get('/tasks', [EmployeeTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [EmployeeTaskController::class, 'show'])->name('tasks.show');
    Route::patch('/tasks/{task}/progress', [EmployeeTaskController::class, 'updateProgress'])->name('tasks.progress');

    // Congés
    Route::get('/leaves', [EmployeeLeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [EmployeeLeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [EmployeeLeaveController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/{leave}', [EmployeeLeaveController::class, 'show'])->name('leaves.show');
    Route::delete('/leaves/{leave}', [EmployeeLeaveController::class, 'destroy'])->name('leaves.destroy');

    // Fiches de paie
    Route::get('/payrolls', [EmployeePayrollController::class, 'index'])->name('payrolls.index');
    Route::get('/payrolls/{payroll}', [EmployeePayrollController::class, 'show'])->name('payrolls.show');
    Route::get('/payrolls/{payroll}/download', [EmployeePayrollController::class, 'downloadPdf'])->name('payrolls.download');

    // Sondages
    Route::get('/surveys', [EmployeeSurveyController::class, 'index'])->name('surveys.index');
    Route::get('/surveys/{survey}', [EmployeeSurveyController::class, 'show'])->name('surveys.show');
    Route::post('/surveys/{survey}/respond', [EmployeeSurveyController::class, 'respond'])->name('surveys.respond');

    // Profil employé
    Route::get('/profile', [\App\Http\Controllers\Employee\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/personal', [\App\Http\Controllers\Employee\ProfileController::class, 'updatePersonal'])->name('profile.update.personal');
    Route::put('/profile/emergency', [\App\Http\Controllers\Employee\ProfileController::class, 'updateEmergencyContact'])->name('profile.update.emergency');
    Route::put('/profile/avatar', [\App\Http\Controllers\Employee\ProfileController::class, 'updateAvatar'])->name('profile.update.avatar');
    Route::put('/profile/password', [\App\Http\Controllers\Employee\ProfileController::class, 'updatePassword'])->name('profile.update.password');

    // Paramètres employé
    Route::get('/settings', [\App\Http\Controllers\Employee\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/password', [\App\Http\Controllers\Employee\SettingsController::class, 'updatePassword'])->name('settings.password');
});

// Routes profil (accessibles à tous les utilisateurs authentifiés)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/messaging.php';
