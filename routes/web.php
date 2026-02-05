<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

    // Historial de Ventas (solo usuario normal; admin no usa esta ruta)
    Route::get('/user/historial-ventas', function () {
        if (Auth::user()->esAdmin()) {
            return redirect()->route('dashboard');
        }
        return view('user.sales.index');
    })->name('user.sales.index');

    // Perfil (compartido)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Empresas, Contactos y Seguimientos (usuario: captura, consulta, seguimiento; admin: además aprobaciones)
    Route::resource('companies', CompanyController::class);
    Route::post('/companies/check-duplicates', [CompanyController::class, 'checkDuplicates'])->name('companies.check-duplicates');

    Route::resource('contacts', ContactController::class);

    Route::resource('follow-ups', FollowUpController::class);
    Route::post('/follow-ups/{followUp}/complete', [FollowUpController::class, 'complete'])->name('follow-ups.complete');
});

// Rutas exclusivas de Administrador (dashboard global, aprobaciones, descargas)
Route::middleware(['auth', 'verified', 'ensure.role', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/contacts/{contact}/pdf', [ContactController::class, 'generatePdf'])->name('contacts.pdf');

    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/companies', [ApprovalController::class, 'companies'])->name('companies');
        Route::post('/companies/{company}/approve', [ApprovalController::class, 'approveCompany'])->name('companies.approve');
        Route::get('/users', [ApprovalController::class, 'users'])->name('users');
        Route::post('/users/{user}/approve', [ApprovalController::class, 'approveUser'])->name('users.approve');
    });
});

require __DIR__.'/auth.php';
