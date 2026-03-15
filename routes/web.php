<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\DataManagementController;
use App\Http\Controllers\FiltrosController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\Auth\AutoLoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Entrada automática cuando el admin aprueba a un usuario (enlace firmado, sin auth)
Route::get('/entrar/{user}', AutoLoginController::class)
    ->name('auth.auto-login')
    ->middleware('signed');

// Redirección raíz: invitados → login; autenticados → dashboard según rol
Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }
    return Auth::user()->esAdmin()
        ? redirect()->route('dashboard')
        : redirect()->route('user.dashboard');
});

// Rutas accesibles por cualquier usuario autenticado (admin y usuario normal)
Route::middleware(['auth', 'verified', 'ensure.role'])->group(function () {
    // Vista de Usuario (panel para rol usuario)
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

    // Historial de Ventas (admin y usuario pueden acceder)
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('historial-ventas', [SalesController::class, 'index'])->name('sales.index');
        Route::get('historial-ventas/create', [SalesController::class, 'create'])->name('sales.create');
        Route::post('historial-ventas', [SalesController::class, 'store'])->name('sales.store');
        Route::get('historial-ventas/{sale}', [SalesController::class, 'show'])->name('sales.show');
        Route::get('historial-ventas/{sale}/ficha-pdf', [SalesController::class, 'fichaPdf'])->name('sales.ficha-pdf');
        Route::get('historial-ventas/{sale}/ficha-word', [SalesController::class, 'fichaWord'])->name('sales.ficha-word');
        Route::get('historial-ventas/{sale}/edit', [SalesController::class, 'edit'])->name('sales.edit');
        Route::put('historial-ventas/{sale}', [SalesController::class, 'update'])->name('sales.update');
        Route::delete('historial-ventas/{sale}', [SalesController::class, 'destroy'])->name('sales.destroy');
    });

    // Perfil (compartido)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Empresas, Contactos y Seguimientos (usuario: captura, consulta, seguimiento; admin: además aprobaciones)
    Route::resource('companies', CompanyController::class);
    Route::get('/filtros', [FiltrosController::class, 'index'])->name('filtros.index');
    Route::get('/companies/{company}/edit-form', [CompanyController::class, 'editForm'])->name('companies.edit-form');
    Route::post('/companies/check-duplicates', [CompanyController::class, 'checkDuplicates'])->name('companies.check-duplicates');

    Route::resource('contacts', ContactController::class);
    Route::patch('/contacts/{contact}/email-status', [ContactController::class, 'updateEmailStatus'])->name('contacts.email-status');
    Route::patch('/contacts/{contact}/notes', [ContactController::class, 'updateNotes'])->name('contacts.notes');

    Route::resource('follow-ups', FollowUpController::class);
    Route::post('/follow-ups/{followUp}/complete', [FollowUpController::class, 'complete'])->name('follow-ups.complete');

    // Gestión de Datos (visualización para todos, edición con permisos)
    Route::get('/data-management', [DataManagementController::class, 'index'])->name('data-management.index');
    Route::get('/data-management/contacts/{contact}', [DataManagementController::class, 'getContact'])->name('data-management.contacts.show');
    Route::put('/data-management/contacts/{contact}', [DataManagementController::class, 'updateContact'])->name('data-management.contacts.update');
    Route::delete('/data-management/contacts/{contact}', [DataManagementController::class, 'destroyContact'])->name('data-management.contacts.destroy');
    Route::get('/data-management/companies/{company}', [DataManagementController::class, 'getCompany'])->name('data-management.companies.show');
    Route::put('/data-management/companies/{company}', [DataManagementController::class, 'updateCompany'])->name('data-management.companies.update');
    Route::delete('/data-management/companies/{company}', [DataManagementController::class, 'destroyCompany'])->name('data-management.companies.destroy');
});

// Rutas exclusivas de Administrador (dashboard global, aprobaciones, descargas)
Route::middleware(['auth', 'verified', 'ensure.role', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/reminder-alerts', [NotificationController::class, 'reminderAlerts'])->name('notifications.reminder-alerts');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/star', [NotificationController::class, 'star'])->name('notifications.star');
    Route::post('/notifications/{notification}/unstar', [NotificationController::class, 'unstar'])->name('notifications.unstar');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Recordatorios (sección dentro de notificaciones)
    Route::get('/reminders', fn () => redirect()->route('notifications.index'))->name('reminders.index');
    Route::post('/reminders', [ReminderController::class, 'store'])->name('reminders.store');
    Route::put('/reminders/{reminder}', [ReminderController::class, 'update'])->name('reminders.update');
    Route::patch('/reminders/{reminder}/toggle', [ReminderController::class, 'toggle'])->name('reminders.toggle');
    Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');

    Route::get('/contacts/{contact}/pdf', [ContactController::class, 'generatePdf'])->name('contacts.pdf');
    Route::get('/contacts/{contact}/word', [ContactController::class, 'generateWord'])->name('contacts.word');

    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::get('/companies', [ApprovalController::class, 'companies'])->name('companies');
        Route::post('/companies/{company}/approve', [ApprovalController::class, 'approveCompany'])->name('companies.approve');
        Route::post('/companies/{company}/deny', [ApprovalController::class, 'denyCompany'])->name('companies.deny');
        Route::get('/users', [ApprovalController::class, 'users'])->name('users');
        Route::post('/users/{user}/approve', [ApprovalController::class, 'approveUser'])->name('users.approve');
        Route::post('/users/{user}/deny', [ApprovalController::class, 'denyUser'])->name('users.deny');
    });

    // Gestión de Datos - Funciones exclusivas de Admin (Exportar/Importar)
    Route::prefix('data-management')->name('data-management.')->group(function () {
        Route::get('/tables', [DataManagementController::class, 'getTables'])->name('tables');
        Route::post('/export', [DataManagementController::class, 'export'])->name('export');
        Route::post('/import', [DataManagementController::class, 'import'])->name('import');
    });
});

require __DIR__.'/auth.php';
