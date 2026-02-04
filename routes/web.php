<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ApprovalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Empresas
    Route::resource('companies', CompanyController::class);
    Route::post('/companies/check-duplicates', [CompanyController::class, 'checkDuplicates'])->name('companies.check-duplicates');

    // Contactos
    Route::resource('contacts', ContactController::class);
    Route::get('/contacts/{contact}/pdf', [ContactController::class, 'generatePdf'])->name('contacts.pdf');

    // Seguimientos
    Route::resource('follow-ups', FollowUpController::class);
    Route::post('/follow-ups/{followUp}/complete', [FollowUpController::class, 'complete'])->name('follow-ups.complete');

    // Aprobaciones (solo admin)
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/companies', [ApprovalController::class, 'companies'])->name('companies');
        Route::post('/companies/{company}/approve', [ApprovalController::class, 'approveCompany'])->name('companies.approve');
        Route::get('/users', [ApprovalController::class, 'users'])->name('users');
        Route::post('/users/{user}/approve', [ApprovalController::class, 'approveUser'])->name('users.approve');
    });

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
