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
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Employee\AnnouncementController as EmployeeAnnouncementController;
use App\Http\Controllers\Employee\DocumentController as EmployeeDocumentController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\PresenceController as EmployeePresenceController;
use App\Http\Controllers\Employee\TaskController as EmployeeTaskController;
use App\Http\Controllers\Employee\LeaveController as EmployeeLeaveController;
use App\Http\Controllers\Employee\PayrollController as EmployeePayrollController;
use App\Http\Controllers\Employee\SurveyController as EmployeeSurveyController;
use Illuminate\Support\Facades\Route;

// Temporary Test Route
Route::get('/test-payroll-civ', function () {
    $user = \App\Models\User::where('role', 'employee')->first();
    if (!$user) return 'No employee found. Create an employee first.';

    // Ensure user has contract
    if (!$user->currentContract) {
         // Create dummy contract
         \App\Models\Contract::create([
            'user_id' => $user->id,
            'base_salary' => 500000,
            'start_date' => now()->subYear(),
            'contract_type' => 'cdi',
            'is_current' => true
         ]);
         $user->refresh();
    }
    
    // Create service
    $service = new \App\Services\Payroll\PayrollService();
    
    // Generate Payroll (will save to DB, which is fine for a test)
    try {
        $payroll = $service->calculatePayroll($user, now()->month, now()->year, [
            'transport_allowance' => 30000,
            'housing_allowance' => 50000,
            'bonuses' => 15000
        ]);
        
        $payroll->load(['user', 'items']);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.payroll-civ', [
                'payroll' => $payroll,
                'user' => $payroll->user,
                'contract' => $payroll->user->currentContract,
                'generatedAt' => now(),
        ]);
        
        return $pdf->stream('bulletin-test.pdf');
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

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
    Route::post('/employees/{employee}/contract/upload', [EmployeeController::class, 'uploadContract'])->name('employees.contract.upload');
    Route::get('/employees/{employee}/contract/download', [EmployeeController::class, 'downloadContract'])->name('employees.contract.download');
    Route::delete('/employees/{employee}/contract', [EmployeeController::class, 'deleteContract'])->name('employees.contract.delete');

    // Gestion des présences - Master View (fusion présences + global-view)
    Route::get('/presences', [AdminPresenceController::class, 'masterView'])->name('presences.index');
    Route::get('/presences/data', [AdminPresenceController::class, 'masterViewData'])->name('presences.master-data');
    Route::get('/presences/employee/{userId}/details', [AdminPresenceController::class, 'employeeDetails'])->name('presences.employee-details');
    Route::get('/presences/employee/{userId}', [AdminPresenceController::class, 'showEmployeePresence'])->name('presences.employee-show');
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
    Route::post('/payrolls/bulk-generate', [AdminPayrollController::class, 'bulkGenerate'])->name('payrolls.bulk-generate');
    Route::post('/payrolls/calculate-preview', [AdminPayrollController::class, 'calculatePreview'])->name('payrolls.calculate-preview');
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
    // Analytics
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/kpis', [AnalyticsController::class, 'getKpiData'])->name('kpis');
        Route::get('/charts', [AnalyticsController::class, 'getChartsData'])->name('charts');
        Route::get('/activities', [AnalyticsController::class, 'getRecentActivities'])->name('activities');
        Route::get('/pending', [AnalyticsController::class, 'getPendingRequests'])->name('pending');
        Route::get('/alerts', [AnalyticsController::class, 'getHrAlerts'])->name('alerts');
        Route::get('/latecomers', [AnalyticsController::class, 'getTopLatecomers'])->name('latecomers');
    });

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
    Route::get('/messaging-chat', [\App\Http\Controllers\Admin\MessagingController::class, 'chat'])->name('messaging.chat');
    Route::post('/messaging', [\App\Http\Controllers\Admin\MessagingController::class, 'store'])->name('messaging.store');
    Route::get('/messaging/{conversation}', [\App\Http\Controllers\Admin\MessagingController::class, 'show'])->name('messaging.show');
    Route::put('/messaging/{conversation}', [\App\Http\Controllers\Admin\MessagingController::class, 'update'])->name('messaging.update');
    Route::delete('/messaging/{conversation}', [\App\Http\Controllers\Admin\MessagingController::class, 'destroy'])->name('messaging.destroy');
    Route::post('/messaging/{conversation}/participants', [\App\Http\Controllers\Admin\MessagingController::class, 'addParticipant'])->name('messaging.participants.add');
    Route::delete('/messaging/{conversation}/participants/{user}', [\App\Http\Controllers\Admin\MessagingController::class, 'removeParticipant'])->name('messaging.participants.remove');
    Route::delete('/messaging/messages/{message}', [\App\Http\Controllers\Admin\MessagingController::class, 'deleteMessage'])->name('messaging.message.delete');
    Route::get('/messaging/{conversation}/messages', [\App\Http\Controllers\Admin\MessagingController::class, 'getMessages'])->name('messaging.messages.index');
    Route::post('/messaging/{conversation}/messages', [\App\Http\Controllers\Admin\MessagingController::class, 'storeMessage'])->name('messaging.messages.store');

    // Notifications admin
    Route::post('/notifications/{id}/read', [AdminDashboardController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [AdminDashboardController::class, 'markAllNotificationsAsRead'])->name('notifications.read-all');

    // Annonces (Announcements)
    Route::resource('announcements', AdminAnnouncementController::class);
    Route::post('announcements/{announcement}/toggle', [AdminAnnouncementController::class, 'toggle'])->name('announcements.toggle');
    Route::post('announcements/{announcement}/pin', [AdminAnnouncementController::class, 'pin'])->name('announcements.pin');

    // Documents
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [AdminDocumentController::class, 'index'])->name('index');
        Route::get('/pending', [AdminDocumentController::class, 'pending'])->name('pending');
        Route::get('/expiring', [AdminDocumentController::class, 'expiring'])->name('expiring');
        Route::get('/{document}', [AdminDocumentController::class, 'show'])->name('show');
        Route::get('/{document}/download', [AdminDocumentController::class, 'download'])->name('download');
        Route::post('/{document}/validate', [AdminDocumentController::class, 'validate'])->name('validate');
        Route::post('/bulk-validate', [AdminDocumentController::class, 'bulkValidate'])->name('bulk-validate');
        Route::delete('/{document}', [AdminDocumentController::class, 'destroy'])->name('destroy');
    });

    // Demandes de documents (côté admin)
    Route::prefix('document-requests')->name('document-requests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'index'])->name('index');
        Route::get('/{documentRequest}', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'show'])->name('show');
        Route::post('/{documentRequest}/respond', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'respond'])->name('respond');
        Route::post('/{documentRequest}/reject', [\App\Http\Controllers\Admin\DocumentRequestController::class, 'reject'])->name('reject');
    });

    // Documents Globaux (Règlement intérieur, Charte, etc.)
    Route::prefix('global-documents')->name('global-documents.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\GlobalDocumentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\GlobalDocumentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\GlobalDocumentController::class, 'store'])->name('store');
        Route::get('/{globalDocument}', [\App\Http\Controllers\Admin\GlobalDocumentController::class, 'show'])->name('show');
        Route::get('/{globalDocument}/edit', [\App\Http\Controllers\Admin\GlobalDocumentController::class, 'edit'])->name('edit');
        Route::put('/{globalDocument}', [\App\Http\Controllers\Admin\GlobalDocumentController::class, 'update'])->name('update');
        Route::get('/{globalDocument}/download', [\App\Http\Controllers\Admin\GlobalDocumentController::class, 'download'])->name('download');
        Route::delete('/{globalDocument}', [\App\Http\Controllers\Admin\GlobalDocumentController::class, 'destroy'])->name('destroy');
    });

    // Paramètres de paie multi-pays
    Route::prefix('payroll-settings')->name('payroll-settings.')->group(function () {
        // Pays
        Route::get('/countries', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'countries'])->name('countries');
        Route::get('/countries/create', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'createCountry'])->name('countries.create');
        Route::post('/countries', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'storeCountry'])->name('countries.store');
        Route::get('/countries/{country}/edit', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'editCountry'])->name('countries.edit');
        Route::put('/countries/{country}', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'updateCountry'])->name('countries.update');
        Route::delete('/countries/{country}', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'destroyCountry'])->name('countries.destroy');

        // Règles d'un pays
        Route::get('/countries/{country}/rules', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'rules'])->name('rules');
        Route::get('/countries/{country}/rules/create', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'createRule'])->name('rules.create');
        Route::post('/countries/{country}/rules', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'storeRule'])->name('rules.store');
        Route::get('/countries/{country}/rules/{rule}/edit', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'editRule'])->name('rules.edit');
        Route::put('/countries/{country}/rules/{rule}', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'updateRule'])->name('rules.update');
        Route::delete('/countries/{country}/rules/{rule}', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'destroyRule'])->name('rules.destroy');

        // Champs d'un pays
        Route::get('/countries/{country}/fields', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'fields'])->name('fields');
        Route::post('/countries/{country}/fields', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'storeField'])->name('fields.store');
        Route::delete('/countries/{country}/fields/{field}', [\App\Http\Controllers\Admin\PayrollSettingsController::class, 'destroyField'])->name('fields.destroy');
    });
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

    // Messagerie
    Route::get('/messaging', [\App\Http\Controllers\Messaging\ConversationController::class, 'index'])->name('messaging.index');

    // Annonces
    Route::get('/announcements', [EmployeeAnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/{announcement}', [EmployeeAnnouncementController::class, 'show'])->name('announcements.show');
    Route::post('/announcements/{announcement}/read', [EmployeeAnnouncementController::class, 'markAsRead'])->name('announcements.read');
    Route::post('/announcements/{announcement}/acknowledge', [EmployeeAnnouncementController::class, 'acknowledge'])->name('announcements.acknowledge');

    // Documents
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [EmployeeDocumentController::class, 'index'])->name('index');
        Route::get('/upload/{type}', [EmployeeDocumentController::class, 'create'])->name('create');
        Route::post('/', [EmployeeDocumentController::class, 'store'])->name('store');
        Route::get('/{document}', [EmployeeDocumentController::class, 'show'])->name('show');
        Route::get('/{document}/download', [EmployeeDocumentController::class, 'download'])->name('download');
        Route::get('/contract/download', [EmployeeDocumentController::class, 'downloadContract'])->name('download-contract');
        Route::delete('/{document}', [EmployeeDocumentController::class, 'destroy'])->name('destroy');
        Route::post('/{document}/acknowledge', [EmployeeDocumentController::class, 'acknowledge'])->name('acknowledge');
    });

    // Global Documents (Règlement intérieur côté employé)
    Route::prefix('global-documents')->name('global-documents.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Employee\GlobalDocumentController::class, 'index'])->name('index');
        Route::get('/{globalDocument}', [\App\Http\Controllers\Employee\GlobalDocumentController::class, 'show'])->name('show');
        Route::get('/{globalDocument}/download', [\App\Http\Controllers\Employee\GlobalDocumentController::class, 'download'])->name('download');
        Route::post('/{globalDocument}/acknowledge', [\App\Http\Controllers\Employee\GlobalDocumentController::class, 'acknowledge'])->name('acknowledge');
    });

    // Demandes de documents
    Route::prefix('document-requests')->name('document-requests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Employee\DocumentRequestController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Employee\DocumentRequestController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Employee\DocumentRequestController::class, 'store'])->name('store');
        Route::get('/{documentRequest}/download', [\App\Http\Controllers\Employee\DocumentRequestController::class, 'download'])->name('download');
    });
});

// Routes profil (accessibles à tous les utilisateurs authentifiés)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/messaging.php';
