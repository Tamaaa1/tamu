<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\PublicAgendaController;
use App\Http\Controllers\AgendaDetailController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MasterDinasController;
use App\Http\Controllers\ParticipantController;

// Public Routes (No Auth Required)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Public Agenda Registration (No Auth Required)
Route::get('/agenda', [PublicAgendaController::class, 'showPublic'])->name('agenda.public');
Route::get('/agenda/{agenda}/register', [PublicAgendaController::class, 'showPublicAgenda'])->name('agenda.public.register');
Route::get('/agenda/register/{token}', [PublicAgendaController::class, 'showPublicAgendaByToken'])->name('agenda.public.register.token');
Route::post('/agenda/register', [PublicAgendaController::class, 'registerParticipant'])
    ->middleware('throttle:10,1') // Rate limiting: 10 requests per minute
    ->name('agenda.register');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Participants Management with AJAX support
    Route::get('/participants', [ParticipantController::class, 'index'])->name('participants.index');
    Route::get('/participants/data', [ParticipantController::class, 'getParticipantsData'])->name('participants.data');
    Route::get('/participants/search-agenda', [ParticipantController::class, 'searchAgenda'])->name('participants.search-agenda');

    Route::get('/participants/create', [ParticipantController::class, 'create'])->name('participants.create');
    Route::post('/participants', [ParticipantController::class, 'store'])->name('participants.store');

    Route::get('/participants/{participant}', [ParticipantController::class, 'show'])->name('participants.show');
    Route::get('/participants/{participant}/edit', [ParticipantController::class, 'edit'])->name('participants.edit');
    Route::put('/participants/{participant}', [ParticipantController::class, 'update'])->name('participants.update');
    Route::delete('/participants/{participant}', [ParticipantController::class, 'destroy'])->name('participants.destroy');

    // PDF Export (handled by AdminController as per existing routes)
    Route::get('/participants/export-pdf', [AdminController::class, 'exportParticipantsPdf'])->name('participants.export-pdf');

    // Other existing routes...
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/agenda/data', [AgendaController::class, 'getAgendaData'])->name('agenda.data');
    Route::resource('agenda', AgendaController::class);
    Route::get('/agenda/{agenda}/qrcode', [AgendaController::class, 'showQrCode'])->name('agenda.qrcode');
    Route::get('/agenda/{agenda}/export-qrcode-pdf', [AgendaController::class, 'exportQrCodePdf'])->name('agenda.export-qrcode-pdf');
    Route::post('/agenda/{agenda}/toggle-link', [AgendaController::class, 'toggleLinkActive'])->name('agenda.toggle-link');
    Route::resource('agenda-detail', AgendaDetailController::class);
    Route::resource('master-dinas', MasterDinasController::class);

    // User Management
    Route::get('/users', [AdminController::class, 'userIndex'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'userCreate'])->name('users.create');
    Route::post('/users', [AdminController::class, 'userStore'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'userEdit'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'userUpdate'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'userDestroy'])->name('users.destroy');

    // Protected file serving for signatures
    Route::get('/signature/{filename}', [AdminController::class, 'serveSignature'])->name('signature.serve');
});
