<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ContactFunctionController;
use App\Http\Controllers\VatRateController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\LogController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// API Routes for AJAX calls
Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/entities/check-nif/{nif}', [EntityController::class, 'checkNifExists'])->name('api.entities.check-nif');
    Route::get('/entities/vies-lookup/{country}/{nif}', [EntityController::class, 'viesLookup'])->name('api.entities.vies-lookup');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas de Clientes (usando EntityController com filtro 'client')
    Route::get('/clients', [EntityController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [EntityController::class, 'create'])->name('clients.create');
    Route::post('/clients', [EntityController::class, 'store'])->name('clients.store');
    Route::get('/clients/{entity}', [EntityController::class, 'show'])->name('clients.show');
    Route::get('/clients/{entity}/edit', [EntityController::class, 'edit'])->name('clients.edit');
    Route::patch('/clients/{entity}', [EntityController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{entity}', [EntityController::class, 'destroy'])->name('clients.destroy');
    Route::post('/clients/{entity}/revalidate-vat', [EntityController::class, 'revalidateVat'])->name('clients.revalidate-vat');

    // Rotas de Fornecedores (usando EntityController com filtro 'supplier')  
    Route::get('/suppliers', [EntityController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [EntityController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [EntityController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{entity}', [EntityController::class, 'show'])->name('suppliers.show');
    Route::get('/suppliers/{entity}/edit', [EntityController::class, 'edit'])->name('suppliers.edit');
    Route::patch('/suppliers/{entity}', [EntityController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{entity}', [EntityController::class, 'destroy'])->name('suppliers.destroy');
    Route::post('/suppliers/{entity}/revalidate-vat', [EntityController::class, 'revalidateVat'])->name('suppliers.revalidate-vat');

    // Rotas das Entidades (Admin - todas as entidades)
    Route::resource('entities', EntityController::class);
    Route::post('/entities/{entity}/revalidate-vat', [EntityController::class, 'revalidateVat'])->name('entities.revalidate-vat');

    // Rotas dos Contactos
    Route::resource('contacts', ContactController::class);

    // Rotas dos Artigos
    Route::resource('articles', ArticleController::class);

    // Rotas dos Países (Configurações)
    Route::resource('countries', CountryController::class);

    // Rotas das Funções de Contactos (Configurações)
    Route::resource('contact-functions', ContactFunctionController::class);

    // Rotas das Taxas de IVA (Configurações - Financeiro)
    Route::resource('vat-rates', VatRateController::class);

    // Rotas de Gestão de Acessos
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserManagementController::class);

    // Rotas de Logs
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

    Route::get('/proposals', function () {
        return redirect()->route('dashboard')->with('info', 'Módulo Propostas em desenvolvimento');
    })->name('proposals.index');
    Route::get('/orders', function () {
        return redirect()->route('dashboard')->with('info', 'Módulo Encomendas em desenvolvimento');
    })->name('orders.index');
    Route::get('/financial', function () {
        return redirect()->route('dashboard')->with('info', 'Módulo Financeiro em desenvolvimento');
    })->name('financial.index');
    Route::get('/calendar', function () {
        return redirect()->route('dashboard')->with('info', 'Módulo Calendário em desenvolvimento');
    })->name('calendar.index');
    Route::get('/settings', function () {
        return redirect()->route('dashboard')->with('info', 'Módulo Configurações em desenvolvimento');
    })->name('settings.index');
    Route::get('/access', function () {
        return redirect()->route('dashboard')->with('info', 'Módulo Gestão de Acessos em desenvolvimento');
    })->name('access.index');
});

require __DIR__ . '/auth.php';
