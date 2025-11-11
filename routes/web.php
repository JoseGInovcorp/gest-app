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
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ClientAccountController;
use App\Http\Controllers\SupplierInvoiceController;
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
    Route::get('/clients/create', [EntityController::class, 'create'])->name('clients.create')->middleware('permission:clients.create');
    Route::middleware('permission:clients.read')->group(function () {
        Route::get('/clients', [EntityController::class, 'index'])->name('clients.index');
        Route::get('/clients/{entity}', [EntityController::class, 'show'])->name('clients.show');
    });
    Route::post('/clients', [EntityController::class, 'store'])->name('clients.store')->middleware('permission:clients.create');
    Route::get('/clients/{entity}/edit', [EntityController::class, 'edit'])->name('clients.edit')->middleware('permission:clients.update');
    Route::patch('/clients/{entity}', [EntityController::class, 'update'])->name('clients.update')->middleware('permission:clients.update');
    Route::delete('/clients/{entity}', [EntityController::class, 'destroy'])->name('clients.destroy')->middleware('permission:clients.delete');
    Route::post('/clients/{entity}/revalidate-vat', [EntityController::class, 'revalidateVat'])->name('clients.revalidate-vat')->middleware('permission:clients.update');

    // Rotas de Fornecedores (usando EntityController com filtro 'supplier')
    Route::get('/suppliers/create', [EntityController::class, 'create'])->name('suppliers.create')->middleware('permission:suppliers.create');
    Route::middleware('permission:suppliers.read')->group(function () {
        Route::get('/suppliers', [EntityController::class, 'index'])->name('suppliers.index');
        Route::get('/suppliers/{entity}', [EntityController::class, 'show'])->name('suppliers.show');
    });
    Route::post('/suppliers', [EntityController::class, 'store'])->name('suppliers.store')->middleware('permission:suppliers.create');
    Route::get('/suppliers/{entity}/edit', [EntityController::class, 'edit'])->name('suppliers.edit')->middleware('permission:suppliers.update');
    Route::patch('/suppliers/{entity}', [EntityController::class, 'update'])->name('suppliers.update')->middleware('permission:suppliers.update');
    Route::delete('/suppliers/{entity}', [EntityController::class, 'destroy'])->name('suppliers.destroy')->middleware('permission:suppliers.delete');
    Route::post('/suppliers/{entity}/revalidate-vat', [EntityController::class, 'revalidateVat'])->name('suppliers.revalidate-vat')->middleware('permission:suppliers.update');

    // Rotas dos Contactos
    Route::get('/contacts/create', [ContactController::class, 'create'])->name('contacts.create')->middleware('permission:contacts.create');
    Route::middleware('permission:contacts.read')->group(function () {
        Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
        Route::get('/contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    });
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store')->middleware('permission:contacts.create');
    Route::get('/contacts/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit')->middleware('permission:contacts.update');
    Route::patch('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update')->middleware('permission:contacts.update');
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy')->middleware('permission:contacts.delete');

    // Rotas dos Artigos
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create')->middleware('permission:articles.create');
    Route::middleware('permission:articles.read')->group(function () {
        Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
    });
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store')->middleware('permission:articles.create');
    Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit')->middleware('permission:articles.update');
    Route::patch('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update')->middleware('permission:articles.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy')->middleware('permission:articles.delete');

    // Rotas dos Países (Configurações)
    Route::get('/countries/create', [CountryController::class, 'create'])->name('countries.create')->middleware('permission:countries.create');
    Route::middleware('permission:countries.read')->group(function () {
        Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
        Route::get('/countries/{country}', [CountryController::class, 'show'])->name('countries.show');
    });
    Route::post('/countries', [CountryController::class, 'store'])->name('countries.store')->middleware('permission:countries.create');
    Route::get('/countries/{country}/edit', [CountryController::class, 'edit'])->name('countries.edit')->middleware('permission:countries.update');
    Route::patch('/countries/{country}', [CountryController::class, 'update'])->name('countries.update')->middleware('permission:countries.update');
    Route::delete('/countries/{country}', [CountryController::class, 'destroy'])->name('countries.destroy')->middleware('permission:countries.delete');

    // Rotas das Funções de Contactos (Configurações)
    Route::get('/contact-functions/create', [ContactFunctionController::class, 'create'])->name('contact-functions.create')->middleware('permission:contact-functions.create');
    Route::middleware('permission:contact-functions.read')->group(function () {
        Route::get('/contact-functions', [ContactFunctionController::class, 'index'])->name('contact-functions.index');
        Route::get('/contact-functions/{contactFunction}', [ContactFunctionController::class, 'show'])->name('contact-functions.show');
    });
    Route::post('/contact-functions', [ContactFunctionController::class, 'store'])->name('contact-functions.store')->middleware('permission:contact-functions.create');
    Route::get('/contact-functions/{contactFunction}/edit', [ContactFunctionController::class, 'edit'])->name('contact-functions.edit')->middleware('permission:contact-functions.update');
    Route::patch('/contact-functions/{contactFunction}', [ContactFunctionController::class, 'update'])->name('contact-functions.update')->middleware('permission:contact-functions.update');
    Route::delete('/contact-functions/{contactFunction}', [ContactFunctionController::class, 'destroy'])->name('contact-functions.destroy')->middleware('permission:contact-functions.delete');

    // Rotas das Taxas de IVA (Configurações - Financeiro)
    Route::get('/vat-rates/create', [VatRateController::class, 'create'])->name('vat-rates.create')->middleware('permission:vat-rates.create');
    Route::middleware('permission:vat-rates.read')->group(function () {
        Route::get('/vat-rates', [VatRateController::class, 'index'])->name('vat-rates.index');
        Route::get('/vat-rates/{vatRate}', [VatRateController::class, 'show'])->name('vat-rates.show');
    });
    Route::post('/vat-rates', [VatRateController::class, 'store'])->name('vat-rates.store')->middleware('permission:vat-rates.create');
    Route::get('/vat-rates/{vatRate}/edit', [VatRateController::class, 'edit'])->name('vat-rates.edit')->middleware('permission:vat-rates.update');
    Route::patch('/vat-rates/{vatRate}', [VatRateController::class, 'update'])->name('vat-rates.update')->middleware('permission:vat-rates.update');
    Route::delete('/vat-rates/{vatRate}', [VatRateController::class, 'destroy'])->name('vat-rates.destroy')->middleware('permission:vat-rates.delete');

    // Rotas das Contas Bancárias (Financeiro)
    Route::get('/bank-accounts/create', [BankAccountController::class, 'create'])->name('bank-accounts.create')->middleware('permission:bank-accounts.create');
    Route::middleware('permission:bank-accounts.read')->group(function () {
        Route::get('/bank-accounts', [BankAccountController::class, 'index'])->name('bank-accounts.index');
        Route::get('/bank-accounts/{bankAccount}', [BankAccountController::class, 'show'])->name('bank-accounts.show');
    });
    Route::post('/bank-accounts', [BankAccountController::class, 'store'])->name('bank-accounts.store')->middleware('permission:bank-accounts.create');
    Route::get('/bank-accounts/{bankAccount}/edit', [BankAccountController::class, 'edit'])->name('bank-accounts.edit')->middleware('permission:bank-accounts.update');
    Route::patch('/bank-accounts/{bankAccount}', [BankAccountController::class, 'update'])->name('bank-accounts.update')->middleware('permission:bank-accounts.update');
    Route::delete('/bank-accounts/{bankAccount}', [BankAccountController::class, 'destroy'])->name('bank-accounts.destroy')->middleware('permission:bank-accounts.delete');

    // Rotas da Conta Corrente de Clientes (Financeiro)
    Route::get('/client-accounts/create', [ClientAccountController::class, 'create'])->name('client-accounts.create')->middleware('permission:client-accounts.create');
    Route::middleware('permission:client-accounts.read')->group(function () {
        Route::get('/client-accounts', [ClientAccountController::class, 'index'])->name('client-accounts.index');
        Route::get('/client-accounts/{clientAccount}', [ClientAccountController::class, 'show'])->name('client-accounts.show');
    });
    Route::post('/client-accounts', [ClientAccountController::class, 'store'])->name('client-accounts.store')->middleware('permission:client-accounts.create');
    Route::get('/client-accounts/{clientAccount}/edit', [ClientAccountController::class, 'edit'])->name('client-accounts.edit')->middleware('permission:client-accounts.update');
    Route::patch('/client-accounts/{clientAccount}', [ClientAccountController::class, 'update'])->name('client-accounts.update')->middleware('permission:client-accounts.update');
    Route::delete('/client-accounts/{clientAccount}', [ClientAccountController::class, 'destroy'])->name('client-accounts.destroy')->middleware('permission:client-accounts.delete');

    // Rotas de Faturas de Fornecedores (Financeiro)
    Route::get('/supplier-invoices/create', [SupplierInvoiceController::class, 'create'])->name('supplier-invoices.create')->middleware('permission:supplier-invoices.create');
    Route::middleware('permission:supplier-invoices.read')->group(function () {
        Route::get('/supplier-invoices', [SupplierInvoiceController::class, 'index'])->name('supplier-invoices.index');
        Route::get('/supplier-invoices/{supplierInvoice}', [SupplierInvoiceController::class, 'show'])->name('supplier-invoices.show');
    });
    Route::post('/supplier-invoices', [SupplierInvoiceController::class, 'store'])->name('supplier-invoices.store')->middleware('permission:supplier-invoices.create');
    Route::get('/supplier-invoices/{supplierInvoice}/edit', [SupplierInvoiceController::class, 'edit'])->name('supplier-invoices.edit')->middleware('permission:supplier-invoices.update');
    Route::patch('/supplier-invoices/{supplierInvoice}', [SupplierInvoiceController::class, 'update'])->name('supplier-invoices.update')->middleware('permission:supplier-invoices.update');
    Route::delete('/supplier-invoices/{supplierInvoice}', [SupplierInvoiceController::class, 'destroy'])->name('supplier-invoices.destroy')->middleware('permission:supplier-invoices.delete');
    Route::post('/supplier-invoices/{supplierInvoice}/send-payment-proof', [SupplierInvoiceController::class, 'sendPaymentProof'])->name('supplier-invoices.send-payment-proof')->middleware('permission:supplier-invoices.update');

    // Rotas de Configurações - Empresa
    Route::middleware('permission:company.read')->group(function () {
        Route::get('/company/settings', [CompanyController::class, 'edit'])->name('company.edit');
    });
    Route::patch('/company/settings', [CompanyController::class, 'update'])->name('company.update')->middleware('permission:company.update');

    // Rotas de Gestão de Acessos
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:roles.create');
    Route::middleware('permission:roles.read')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    });
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles.create');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:roles.update');
    Route::patch('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.delete');

    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create')->middleware('permission:users.create');
    Route::middleware('permission:users.read')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    });
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store')->middleware('permission:users.create');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit')->middleware('permission:users.update');
    Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update')->middleware('permission:users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');

    // Rotas de Logs
    Route::middleware('permission:logs.read')->group(function () {
        Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    });

    // Rotas de Encomendas de Clientes
    Route::get('/customer-orders/create', [CustomerOrderController::class, 'create'])->name('customer-orders.create')->middleware('permission:customer-orders.create');
    Route::middleware('permission:customer-orders.read')->group(function () {
        Route::get('/customer-orders', [CustomerOrderController::class, 'index'])->name('customer-orders.index');
        Route::get('/customer-orders/{customerOrder}', [CustomerOrderController::class, 'show'])->name('customer-orders.show');
    });
    Route::post('/customer-orders', [CustomerOrderController::class, 'store'])->name('customer-orders.store')->middleware('permission:customer-orders.create');
    Route::get('/customer-orders/{customerOrder}/edit', [CustomerOrderController::class, 'edit'])->name('customer-orders.edit')->middleware('permission:customer-orders.update');
    Route::patch('/customer-orders/{customerOrder}', [CustomerOrderController::class, 'update'])->name('customer-orders.update')->middleware('permission:customer-orders.update');
    Route::delete('/customer-orders/{customerOrder}', [CustomerOrderController::class, 'destroy'])->name('customer-orders.destroy')->middleware('permission:customer-orders.delete');
    Route::post('/customer-orders/{customerOrder}/convert-to-supplier-orders', [CustomerOrderController::class, 'convertToSupplierOrders'])->name('customer-orders.convert')->middleware('permission:customer-orders.update');
    Route::get('/customer-orders/{customerOrder}/pdf', [CustomerOrderController::class, 'generatePDF'])->name('customer-orders.pdf')->middleware('permission:customer-orders.read');

    // Rotas de Encomendas de Fornecedores
    Route::get('/supplier-orders/create', [\App\Http\Controllers\SupplierOrderController::class, 'create'])->name('supplier-orders.create')->middleware('permission:supplier-orders.create');
    Route::middleware('permission:supplier-orders.read')->group(function () {
        Route::get('/supplier-orders', [\App\Http\Controllers\SupplierOrderController::class, 'index'])->name('supplier-orders.index');
        Route::get('/supplier-orders/{supplierOrder}', [\App\Http\Controllers\SupplierOrderController::class, 'show'])->name('supplier-orders.show');
    });
    Route::post('/supplier-orders', [\App\Http\Controllers\SupplierOrderController::class, 'store'])->name('supplier-orders.store')->middleware('permission:supplier-orders.create');
    Route::get('/supplier-orders/{supplierOrder}/edit', [\App\Http\Controllers\SupplierOrderController::class, 'edit'])->name('supplier-orders.edit')->middleware('permission:supplier-orders.update');
    Route::patch('/supplier-orders/{supplierOrder}', [\App\Http\Controllers\SupplierOrderController::class, 'update'])->name('supplier-orders.update')->middleware('permission:supplier-orders.update');
    Route::delete('/supplier-orders/{supplierOrder}', [\App\Http\Controllers\SupplierOrderController::class, 'destroy'])->name('supplier-orders.destroy')->middleware('permission:supplier-orders.delete');
    Route::get('/supplier-orders/{supplierOrder}/pdf', [\App\Http\Controllers\SupplierOrderController::class, 'generatePDF'])->name('supplier-orders.pdf')->middleware('permission:supplier-orders.read');

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
