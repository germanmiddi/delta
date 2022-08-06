<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Manager\Orders\OrderController;
use App\Http\Controllers\Manager\Dashboard\DashboardController;
use App\Http\Controllers\Manager\Clients\ClientController;
use App\Http\Controllers\Manager\Drivers\DriverController;
use App\Http\Controllers\Manager\Companies\CompanyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return Inertia::render('Dashboard');
//     })->name('dashboard');
// });

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

Route::get('/orders', [OrderController::class, 'index'])
    ->name('orders')
    ->middleware('auth');

Route::post('/orders/store', [OrderController::class, 'store'])
    ->name('orders.store')
    ->middleware('auth');

Route::get('/orders/list', [OrderController::class, 'list'])
    ->name('orders.list')
    ->middleware('auth');

Route::get('/orders/listdashboard', [OrderController::class, 'listdashboard'])
    ->name('orders.listdashboard')
    ->middleware('auth');

Route::get('/orders/create', [OrderController::class, 'create'])
    ->name('orders.create')
    ->middleware('auth');

/*******************************************************************/

Route::get('/clients', [ClientController::class, 'index'])
    ->name('clients')
    ->middleware('auth');

Route::get('/clients/create', [ClientController::class, 'create'])
    ->name('clients.create')
    ->middleware('auth');

Route::get('/clients/list', [ClientController::class, 'list'])
    ->name('clients.list')
    ->middleware('auth');

Route::post('/clients/store', [ClientController::class, 'store'])
    ->name('clients.store')
    ->middleware('auth');

Route::get('/clients/getCityByCp', [ClientController::class, 'getCityByCp'])
    ->name('clients.getCityByCp')
    ->middleware('auth');

Route::get('clients/{client}/edit', [ClientController::class, 'edit'])
    ->name('clients.edit')
    ->middleware('auth');

Route::post('/clients/update', [ClientController::class, 'update'])
    ->name('clients.update')
    ->middleware('auth');


/*******************************************************************/

Route::get('/companies', [CompanyController::class, 'index'])
    ->name('companies')
    ->middleware('auth');

Route::get('/companies/list', [CompanyController::class, 'list'])
    ->name('companies.list')
    ->middleware('auth');

Route::post('/companies/store', [CompanyController::class, 'store'])
    ->name('companies.store')
    ->middleware('auth');

Route::post('/companies/update', [CompanyController::class, 'update'])
    ->name('companies.update')
    ->middleware('auth');
/*******************************************************************/

Route::get('/drivers', [DriverController::class, 'index'])
    ->name('drivers')
    ->middleware('auth');

Route::get('/drivers/list', [DriverController::class, 'list'])
    ->name('drivers.list')
    ->middleware('auth');

Route::post('/drivers/store', [DriverController::class, 'store'])
    ->name('drivers.store')
    ->middleware('auth');

Route::post('/drivers/update', [DriverController::class, 'update'])
    ->name('drivers.update')
    ->middleware('auth');
