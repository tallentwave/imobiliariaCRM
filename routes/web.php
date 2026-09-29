<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FeatureController as AdminFeatureController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Admin\PropertyImageController as AdminPropertyImageController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VisitController as AdminVisitController;
use App\Http\Controllers\ClientAreaController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SavedSearchController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Site público
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/imoveis', [PropertyController::class, 'index'])->name('imoveis.index');
Route::get('/imoveis/{property}', [PropertyController::class, 'show'])->name('imoveis.show');
Route::get('/sobre', [PageController::class, 'about'])->name('sobre');
Route::get('/contato', [PageController::class, 'contact'])->name('contato');
Route::post('/contato', [LeadController::class, 'store'])->name('leads.store');

/*
|--------------------------------------------------------------------------
| Área do cliente (autenticado)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        return $user->isStaff()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('minha-conta');
    })->name('dashboard');

    Route::get('/minha-conta', [ClientAreaController::class, 'index'])->name('minha-conta');
    Route::post('/favoritos/{property}', [FavoriteController::class, 'toggle'])->name('favoritos.toggle');
    Route::post('/buscas-salvas', [SavedSearchController::class, 'store'])->name('buscas-salvas.store');
    Route::delete('/buscas-salvas/{savedSearch}', [SavedSearchController::class, 'destroy'])->name('buscas-salvas.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Painel administrativo / CRM
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin|corretor|financeiro'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('properties', AdminPropertyController::class)->except(['show']);
        Route::delete('property-images/{image}', [AdminPropertyImageController::class, 'destroy'])->name('property-images.destroy');
        Route::post('property-images/{image}/cover', [AdminPropertyImageController::class, 'setCover'])->name('property-images.cover');

        Route::resource('leads', AdminLeadController::class)->only(['index', 'show', 'update', 'destroy']);

        Route::resource('visits', AdminVisitController::class)->except(['show']);

        Route::get('features', [AdminFeatureController::class, 'index'])->name('features.index');
        Route::post('features', [AdminFeatureController::class, 'store'])->name('features.store');
        Route::delete('features/{feature}', [AdminFeatureController::class, 'destroy'])->name('features.destroy');

        Route::resource('users', AdminUserController::class)->except(['show']);

        Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
        Route::patch('settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });

require __DIR__.'/auth.php';
