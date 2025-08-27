<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AgendaDetailController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MasterDinasController;

// Public Routes (No Auth Required)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Public Agenda Registration (No Auth Required)
Route::get('/agenda', [AgendaController::class, 'showPublic'])->name('agenda.public');
Route::get('/agenda/{agenda}/register', [AgendaController::class, 'showPublicAgenda'])->name('agenda.public.register');
Route::post('/agenda/register', [AgendaController::class, 'registerParticipant'])->name('agenda.register');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/admin/participants/export-pdf', [AdminController::class, 'exportParticipantsPdf'])->name('participants.export-pdf');
Route::get('/admin/participants/export-excel', [AdminController::class, 'exportParticipantsExcel'])->name('participants.export-excel');

// Admin Routes (Auth Required)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Agenda Management
    Route::resource('agenda', AgendaController::class);
    Route::get('agenda/{agenda}/export-excel', [AgendaController::class, 'exportExcel'])->name('agenda.export-excel');
    Route::get('agenda/{agenda}/export-pdf', [AgendaController::class, 'exportPdf'])->name('agenda.export-pdf');
    
    // Agenda Detail Management
    Route::resource('agenda-detail', AgendaDetailController::class);
    
    // Participants Management (AdminController)
    Route::get('/participants', [AdminController::class, 'participantIndex'])->name('participants.index');
    Route::get('/participants/{participant}', [AdminController::class, 'participantShow'])->name('participants.show');
    Route::get('/participants/{participant}/edit', [AdminController::class, 'participantEdit'])->name('participants.edit');
    Route::put('/participants/{participant}', [AdminController::class, 'participantUpdate'])->name('participants.update');
    Route::delete('/participants/{participant}', [AdminController::class, 'participantDestroy'])->name('participants.destroy');
    
    // Master Dinas Management
    Route::resource('master-dinas', MasterDinasController::class);
    
    // User Management
    Route::resource('users', AdminController::class);
});


