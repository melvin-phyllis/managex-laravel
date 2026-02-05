<?php

use App\Http\Controllers\Admin\AIAssistantController as AdminAIAssistantController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EmployeeEvaluationController;
use App\Http\Controllers\Admin\GeolocationZoneController;
use App\Http\Controllers\Admin\InternEvaluationController as AdminInternEvaluationController;
use App\Http\Controllers\Admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\Admin\PayrollController as AdminPayrollController;
use App\Http\Controllers\Admin\PresenceController as AdminPresenceController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SurveyController as AdminSurveyController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Employee\AIAssistantController;
use App\Http\Controllers\Employee\AnnouncementController as EmployeeAnnouncementController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\DocumentController as EmployeeDocumentController;
use App\Http\Controllers\Employee\InternEvaluationController as EmployeeInternEvaluationController;
use App\Http\Controllers\Employee\LeaveController as EmployeeLeaveController;
use App\Http\Controllers\Employee\PayrollController as EmployeePayrollController;
use App\Http\Controllers\Employee\PresenceController as EmployeePresenceController;
use App\Http\Controllers\Employee\SurveyController as EmployeeSurveyController;
use App\Http\Controllers\Employee\TaskController as EmployeeTaskController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tutor\InternEvaluationController as TutorInternEvaluationController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// ROUTES DE DÉVELOPPEMENT - PROTÉGÉES PAR ENVIRONNEMENT + AUTHENTIFICATION
// Ces routes sont UNIQUEMENT accessibles en environnement local par un admin
// ============================================================================
if (app()->environment('local')) {
    Route::middleware(['auth', 'role:admin'])->prefix('dev')->name('dev.')->group(function () {
        // Seed Interns Route (DEV ONLY)
        Route::get('/seed-interns', function () {
            try {
                $department = \App\Models\Department::first();
                $tutor = \App\Models\User::where('role', 'admin')->first();
                if (! $department || ! $tutor) {
                    return response()->json(['error' => 'Missing department or tutor']);
                }

                $internPosition = \App\Models\Position::firstOrCreate(
                    ['name' => 'Stagiaire'],
                    ['description' => 'Poste de stagiaire', 'department_id' => $department->id]
                );

                $interns = [
                    ['name' => 'Koné Aminata', 'email' => 'aminata.kone@stagiaire.managex.com'],
                    ['name' => 'Traoré Ibrahim', 'email' => 'ibrahim.traore@stagiaire.managex.com'],
                    ['name' => 'Kouassi Marie', 'email' => 'marie.kouassi@stagiaire.managex.com'],
                    ['name' => 'Diallo Moussa', 'email' => 'moussa.diallo@stagiaire.managex.com'],
                    ['name' => 'Bamba Fatou', 'email' => 'fatou.bamba@stagiaire.managex.com'],
                ];

                $created = [];
                foreach ($interns as $i => $data) {
                    $intern = \App\Models\User::updateOrCreate(['email' => $data['email']], [
                        'name' => $data['name'],
                        'password' => bcrypt(\Illuminate\Support\Str::random(16)), // Mot de passe aléatoire sécurisé
                        'status' => 'active',
                        'department_id' => $department->id,
                        'position_id' => $internPosition->id,
                        'hire_date' => now()->subMonths(rand(1, 3)),
                        'employee_id' => 'STG-'.str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                        'contract_type' => 'stage',
                        'supervisor_id' => $tutor->id,
                    ]);
                    // Définir le rôle de manière sécurisée
                    $intern->setRole('employee')->save();
                    $created[] = $intern;
                }

                $evalCount = 0;
                foreach ($created as $i => $intern) {
                    for ($w = 0; $w < rand(4, 8); $w++) {
                        $weekStart = \Carbon\Carbon::now()->subWeeks($w)->startOfWeek();
                        if (\App\Models\InternEvaluation::where('intern_id', $intern->id)->where('week_start', $weekStart)->exists()) {
                            continue;
                        }
                        \App\Models\InternEvaluation::create([
                            'intern_id' => $intern->id, 'tutor_id' => $tutor->id, 'week_start' => $weekStart,
                            'discipline_score' => min(2.5, max(0.5, 1.5 + $i * 0.15 + rand(-5, 5) / 10)),
                            'behavior_score' => min(2.5, max(0.5, 1.5 + $i * 0.15 + rand(-5, 5) / 10)),
                            'skills_score' => min(2.5, max(0.5, 1.5 + $i * 0.15 + rand(-3, 7) / 10)),
                            'communication_score' => min(2.5, max(0.5, 1.5 + $i * 0.15 + rand(-5, 5) / 10)),
                            'discipline_comment' => 'Bon respect des horaires.', 'behavior_comment' => 'Attitude professionnelle.',
                            'skills_comment' => 'Bonne progression.', 'communication_comment' => 'Communication claire.',
                            'general_comment' => 'Semaine positive.', 'objectives_next_week' => 'Continuer les efforts.',
                            'status' => 'submitted', 'submitted_at' => $weekStart->copy()->addDays(5),
                        ]);
                        $evalCount++;
                    }
                }

                return response()->json(['success' => true, 'interns' => count($created), 'evaluations' => $evalCount]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage(), 'line' => $e->getLine()], 500);
            }
        })->name('seed-interns');

        // Test Payroll Route (DEV ONLY)
        Route::get('/test-payroll-civ', function () {
            $user = \App\Models\User::where('role', 'employee')->first();
            if (! $user) {
                return 'No employee found. Create an employee first.';
            }

            if (! $user->currentContract) {
                \App\Models\Contract::create([
                    'user_id' => $user->id,
                    'base_salary' => 500000,
                    'start_date' => now()->subYear(),
                    'contract_type' => 'cdi',
                    'is_current' => true,
                ]);
                $user->refresh();
            }

            $service = new \App\Services\Payroll\PayrollService;

            try {
                $payroll = $service->calculatePayroll($user, now()->month, now()->year, [
                    'transport_allowance' => 30000,
                    'housing_allowance' => 50000,
                    'bonuses' => 15000,
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
                return 'Error: '.$e->getMessage();
            }
        })->name('test-payroll');
    });
}
// ============================================================================

// Health check endpoint for Railway/Docker
Route::get('/health', [PageController::class, 'health'])->name('health');

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/demo', [PageController::class, 'demoRequest'])->name('demo-request');
Route::post('/demo', [PageController::class, 'storeDemoRequest'])->name('demo-request.store');

// Redirection après login selon le rôle
Route::get('/dashboard', [PageController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

// Routes Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [AdminDashboardController::class, 'getStats'])->name('stats');
    Route::get('/dashboard/activity', [AdminDashboardController::class, 'getRecentActivity'])->name('dashboard.activity');
    Route::get('/dashboard/alerts', [AdminDashboardController::class, 'getAlertsData'])->name('dashboard.alerts');
    Route::get('/dashboard/calendar', [AdminDashboardController::class, 'getCalendarEventsData'])->name('dashboard.calendar');

    // Gestion des employés (export avant resource pour éviter que "export" soit pris comme {employee})
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::resource('employees', EmployeeController::class);
    Route::post('/employees/{employee}/contract/upload', [EmployeeController::class, 'uploadContract'])->name('employees.contract.upload');
    Route::get('/employees/{employee}/contract/download', [EmployeeController::class, 'downloadContract'])->name('employees.contract.download');
    Route::delete('/employees/{employee}/contract', [EmployeeController::class, 'deleteContract'])->name('employees.contract.delete');
    // Gestion du statut des comptes
    Route::post('/employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
    Route::post('/employees/{employee}/suspend', [EmployeeController::class, 'suspend'])->name('employees.suspend');
    Route::post('/employees/{employee}/activate', [EmployeeController::class, 'activate'])->name('employees.activate');

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
    Route::post('/tasks/{task}/update-status', [AdminTaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::delete('/tasks/{task}', [AdminTaskController::class, 'destroy'])->name('tasks.destroy');

    // Gestion des congés
    Route::get('/leaves', [AdminLeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/{leave}', [AdminLeaveController::class, 'show'])->name('leaves.show');
    Route::post('/leaves/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('leaves.reject');
    Route::delete('/leaves/{leave}', [AdminLeaveController::class, 'destroy'])->name('leaves.destroy');

    // Gestion des fiches de paie (SECURITE: rate limiting sur les opérations bulk)
    Route::post('/payrolls/bulk-generate', [AdminPayrollController::class, 'bulkGenerate'])
        ->middleware('throttle:sensitive')
        ->name('payrolls.bulk-generate');
    Route::post('/payrolls/calculate-preview', [AdminPayrollController::class, 'calculatePreview'])->name('payrolls.calculate-preview');
    Route::resource('payrolls', AdminPayrollController::class);
    Route::get('/payrolls/{payroll}/download', [AdminPayrollController::class, 'downloadPdf'])->name('payrolls.download');
    Route::post('/payrolls/{payroll}/mark-paid', [AdminPayrollController::class, 'markAsPaid'])->name('payrolls.mark-paid');

    // Évaluations des performances employés (CDI/CDD) - SECURITE: rate limiting sur bulk
    Route::get('/employee-evaluations/bulk-create', [EmployeeEvaluationController::class, 'bulkCreate'])->name('employee-evaluations.bulk-create');
    Route::post('/employee-evaluations/bulk-store', [EmployeeEvaluationController::class, 'bulkStore'])
        ->middleware('throttle:sensitive')
        ->name('employee-evaluations.bulk-store');
    Route::post('/employee-evaluations/calculate-salary', [EmployeeEvaluationController::class, 'calculateSalary'])->name('employee-evaluations.calculate-salary');
    Route::post('/employee-evaluations/{employeeEvaluation}/validate', [EmployeeEvaluationController::class, 'validate'])->name('employee-evaluations.validate');
    Route::resource('employee-evaluations', EmployeeEvaluationController::class);

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
        Route::get('/top-performers', [AnalyticsController::class, 'getTopPerformers'])->name('top-performers');
        Route::get('/best-attendance', [AnalyticsController::class, 'getBestAttendance'])->name('best-attendance');
        Route::get('/evaluation-stats', [AnalyticsController::class, 'getEvaluationStats'])->name('evaluation-stats');
        // Insights IA
        Route::get('/ai-insights', [AnalyticsController::class, 'getAiInsights'])
            ->middleware('throttle:ai')
            ->name('ai-insights');
        // SECURITE: Rate limiting sur les exports (opérations coûteuses)
        Route::get('/export/pdf', [AnalyticsController::class, 'exportPdf'])
            ->middleware('throttle:exports')
            ->name('export.pdf');
        Route::get('/export/excel', [AnalyticsController::class, 'exportExcel'])
            ->middleware('throttle:exports')
            ->name('export.excel');
    });

    // Paramètres
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::put('/settings/email', [SettingsController::class, 'updateEmail'])->name('settings.update-email');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.update-password');

    // Départements (dans settings)
    Route::post('/settings/departments', [SettingsController::class, 'storeDepartment'])->name('settings.departments.store');
    Route::put('/settings/departments/{department}', [SettingsController::class, 'updateDepartment'])->name('settings.departments.update');
    Route::delete('/settings/departments/{department}', [SettingsController::class, 'destroyDepartment'])->name('settings.departments.destroy');

    // Postes (dans settings)
    Route::post('/settings/positions', [SettingsController::class, 'storePosition'])->name('settings.positions.store');
    Route::put('/settings/positions/{position}', [SettingsController::class, 'updatePosition'])->name('settings.positions.update');
    Route::delete('/settings/positions/{position}', [SettingsController::class, 'destroyPosition'])->name('settings.positions.destroy');

    // Gestion de la messagerie (admin) - SECURITE: rate limiting
    Route::get('/messaging', [\App\Http\Controllers\Admin\MessagingController::class, 'index'])->name('messaging.index');
    Route::get('/messaging-chat', [\App\Http\Controllers\Admin\MessagingController::class, 'chat'])->name('messaging.chat');
    Route::post('/messaging', [\App\Http\Controllers\Admin\MessagingController::class, 'store'])
        ->middleware('throttle:messaging')
        ->name('messaging.store');
    Route::get('/messaging/{conversation}', [\App\Http\Controllers\Admin\MessagingController::class, 'show'])->name('messaging.show');
    Route::put('/messaging/{conversation}', [\App\Http\Controllers\Admin\MessagingController::class, 'update'])
        ->middleware('throttle:messaging')
        ->name('messaging.update');
    Route::delete('/messaging/{conversation}', [\App\Http\Controllers\Admin\MessagingController::class, 'destroy'])
        ->middleware('throttle:sensitive')
        ->name('messaging.destroy');
    Route::post('/messaging/{conversation}/participants', [\App\Http\Controllers\Admin\MessagingController::class, 'addParticipant'])->name('messaging.participants.add');
    Route::delete('/messaging/{conversation}/participants/{user}', [\App\Http\Controllers\Admin\MessagingController::class, 'removeParticipant'])->name('messaging.participants.remove');
    Route::delete('/messaging/messages/{message}', [\App\Http\Controllers\Admin\MessagingController::class, 'deleteMessage'])->name('messaging.message.delete');
    Route::get('/messaging/{conversation}/messages', [\App\Http\Controllers\Admin\MessagingController::class, 'getMessages'])->name('messaging.messages.index');
    Route::post('/messaging/{conversation}/messages', [\App\Http\Controllers\Admin\MessagingController::class, 'storeMessage'])->name('messaging.messages.store');
    Route::post('/messaging/{conversation}/read', [\App\Http\Controllers\Admin\MessagingController::class, 'markAsRead'])->name('messaging.read');

    // Notifications admin
    Route::get('/notifications/unread-count', [AdminDashboardController::class, 'getUnreadNotificationsCount'])->name('notifications.unread-count');
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
        // SECURITE: Rate limiting sur les opérations bulk
        Route::post('/bulk-validate', [AdminDocumentController::class, 'bulkValidate'])
            ->middleware('throttle:sensitive')
            ->name('bulk-validate');
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

    // Évaluations des stagiaires (Admin)
    Route::prefix('intern-evaluations')->name('intern-evaluations.')->group(function () {
        Route::get('/', [AdminInternEvaluationController::class, 'index'])->name('index');
        Route::get('/report', [AdminInternEvaluationController::class, 'report'])->name('report');
        Route::get('/missing', [AdminInternEvaluationController::class, 'missingEvaluations'])->name('missing');
        Route::get('/export-pdf', [AdminInternEvaluationController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/intern/{intern}', [AdminInternEvaluationController::class, 'show'])->name('show');
        Route::post('/intern/{intern}/assign-supervisor', [AdminInternEvaluationController::class, 'assignSupervisor'])->name('assign-supervisor');
        Route::delete('/intern/{intern}/remove-supervisor', [AdminInternEvaluationController::class, 'removeSupervisor'])->name('remove-supervisor');
    });

    // Tuteur - Évaluations des stagiaires (pour les admins qui supervisent des stagiaires)
    Route::prefix('tutor/evaluations')->name('tutor.evaluations.')->group(function () {
        Route::get('/', [TutorInternEvaluationController::class, 'index'])->name('index');
        Route::get('/intern/{intern}/create', [TutorInternEvaluationController::class, 'create'])->name('create');
        Route::post('/intern/{intern}', [TutorInternEvaluationController::class, 'store'])->name('store');
        Route::get('/{evaluation}', [TutorInternEvaluationController::class, 'show'])->name('show');
        Route::get('/{evaluation}/edit', [TutorInternEvaluationController::class, 'edit'])->name('edit');
        Route::put('/{evaluation}', [TutorInternEvaluationController::class, 'update'])->name('update');
        Route::get('/intern/{intern}/history', [TutorInternEvaluationController::class, 'history'])->name('history');
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

    // Assistant IA Admin
    Route::post('/ai/chat', [AdminAIAssistantController::class, 'chat'])
        ->middleware('throttle:ai')
        ->name('ai.chat');
});

// Routes Employee
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [EmployeeDashboardController::class, 'getChartDataApi'])->name('dashboard.chart-data');
    Route::get('/dashboard/events', [EmployeeDashboardController::class, 'getUpcomingEventsApi'])->name('dashboard.events');
    Route::get('/notifications/unread-count', [EmployeeDashboardController::class, 'getUnreadNotificationsCount'])->name('notifications.unread-count');
    Route::post('/notifications/{id}/read', [EmployeeDashboardController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [EmployeeDashboardController::class, 'markAllNotificationsAsRead'])->name('notifications.read-all');

    // Présences (rate limiting pour éviter les abus)
    Route::get('/presences', [EmployeePresenceController::class, 'index'])->name('presences.index');
    Route::post('/presences/check-in', [EmployeePresenceController::class, 'checkIn'])
        ->middleware('throttle:5,1') // 5 tentatives par minute
        ->name('presences.check-in');
    Route::post('/presences/check-out', [EmployeePresenceController::class, 'checkOut'])
        ->middleware('throttle:5,1')
        ->name('presences.check-out');
    // Sessions de rattrapage (jours non travaillés)
    Route::post('/presences/recovery/start', [EmployeePresenceController::class, 'startRecoverySession'])
        ->middleware('throttle:5,1')
        ->name('presences.recovery.start');
    Route::post('/presences/recovery/end', [EmployeePresenceController::class, 'endRecoverySession'])
        ->middleware('throttle:5,1')
        ->name('presences.recovery.end');

    // Tâches (assignées par l'admin, l'employé peut seulement voir et mettre à jour la progression)
    Route::get('/tasks', [EmployeeTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [EmployeeTaskController::class, 'show'])->name('tasks.show');
    Route::patch('/tasks/{task}/progress', [EmployeeTaskController::class, 'updateProgress'])->name('tasks.progress');

    // Congés (rate limiting sur la création)
    Route::get('/leaves', [EmployeeLeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [EmployeeLeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [EmployeeLeaveController::class, 'store'])
        ->middleware('throttle:10,1') // 10 demandes par minute
        ->name('leaves.store');
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

    // Mes évaluations (pour les stagiaires)
    Route::prefix('my-evaluations')->name('evaluations.')->group(function () {
        Route::get('/', [EmployeeInternEvaluationController::class, 'index'])->name('index');
        Route::get('/{evaluation}', [EmployeeInternEvaluationController::class, 'show'])->name('show');
    });

    // Tuteur - Évaluations des stagiaires (pour les employés qui supervisent des stagiaires)
    Route::prefix('tutor/evaluations')->name('tutor.evaluations.')->group(function () {
        Route::get('/', [TutorInternEvaluationController::class, 'index'])->name('index');
        Route::get('/intern/{intern}/create', [TutorInternEvaluationController::class, 'create'])->name('create');
        Route::post('/intern/{intern}', [TutorInternEvaluationController::class, 'store'])->name('store');
        Route::get('/{evaluation}', [TutorInternEvaluationController::class, 'show'])->name('show');
        Route::get('/{evaluation}/edit', [TutorInternEvaluationController::class, 'edit'])->name('edit');
        Route::put('/{evaluation}', [TutorInternEvaluationController::class, 'update'])->name('update');
        Route::get('/intern/{intern}/history', [TutorInternEvaluationController::class, 'history'])->name('history');
    });

    // Assistant IA RH
    Route::post('/ai/chat', [AIAssistantController::class, 'chat'])
        ->middleware('throttle:ai')
        ->name('ai.chat');
});

// Routes profil (accessibles à tous les utilisateurs authentifiés)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/messaging.php';
