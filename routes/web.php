<?php

use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\CommissionController as AdminCommissionController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DealController as AdminDealController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\ExportController as AdminExportController;
use App\Http\Controllers\Admin\FeatureController as AdminFeatureController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\Admin\ListingAgreementController as AdminListingAgreementController;
use App\Http\Controllers\Admin\OpportunityController as AdminOpportunityController;
use App\Http\Controllers\Admin\PrivacyRequestController as AdminPrivacyRequestController;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\Admin\PropertyImageController as AdminPropertyImageController;
use App\Http\Controllers\Admin\ProposalController as AdminProposalController;
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
| Painel administrativo / CRM (ALTIUS)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin|corretor|financeiro|captador|compliance'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Contatos (registo mestre de pessoas/empresas)
        Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
        Route::get('contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
        Route::get('contacts/{contact}/edit', [AdminContactController::class, 'edit'])->name('contacts.edit');
        Route::put('contacts/{contact}', [AdminContactController::class, 'update'])->name('contacts.update');

        // Imóveis
        Route::resource('properties', AdminPropertyController::class)->except(['show']);
        Route::delete('property-images/{image}', [AdminPropertyImageController::class, 'destroy'])->name('property-images.destroy');
        Route::post('property-images/{image}/cover', [AdminPropertyImageController::class, 'setCover'])->name('property-images.cover');

        // Captação (listing agreements)
        Route::resource('listings', AdminListingAgreementController::class)->except(['show']);

        // Leads
        Route::resource('leads', AdminLeadController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::post('leads/{lead}/convert', [AdminLeadController::class, 'convertToOpportunity'])->name('leads.convert');
        Route::post('leads/{lead}/documents', [AdminDocumentController::class, 'storeForLead'])->name('leads.documents.store');
        Route::get('documents/{document}/download', [AdminDocumentController::class, 'download'])->name('documents.download');
        Route::delete('documents/{document}', [AdminDocumentController::class, 'destroy'])->name('documents.destroy');

        // Oportunidades e propostas
        Route::get('opportunities', [AdminOpportunityController::class, 'index'])->name('opportunities.index');
        Route::get('opportunities/{opportunity}', [AdminOpportunityController::class, 'show'])->name('opportunities.show');
        Route::put('opportunities/{opportunity}', [AdminOpportunityController::class, 'update'])->name('opportunities.update');

        Route::post('opportunities/{opportunity}/proposals', [AdminProposalController::class, 'store'])->name('proposals.store');
        Route::post('proposals/{proposal}/counter', [AdminProposalController::class, 'counter'])->name('proposals.counter');
        Route::post('proposals/{proposal}/accept', [AdminProposalController::class, 'accept'])->name('proposals.accept');
        Route::post('proposals/{proposal}/reject', [AdminProposalController::class, 'reject'])->name('proposals.reject');
        Route::post('proposals/{proposal}/deal', [AdminDealController::class, 'createFromProposal'])->name('proposals.create-deal');

        // Negócios / Deal Room
        Route::get('deals', [AdminDealController::class, 'index'])->name('deals.index');
        Route::get('deals/{deal}', [AdminDealController::class, 'show'])->name('deals.show');
        Route::patch('deals/{deal}/status', [AdminDealController::class, 'updateStatus'])->name('deals.update-status');
        Route::post('deal-checklists/{checklist}/toggle', [AdminDealController::class, 'toggleChecklist'])->name('deal-checklists.toggle');

        // Visitas
        Route::resource('visits', AdminVisitController::class)->except(['show']);

        // Comissões
        Route::get('comissoes', [AdminCommissionController::class, 'index'])->name('commissions.index');
        Route::get('comissoes/export', [AdminCommissionController::class, 'export'])->name('commissions.export');
        Route::post('comissoes/{event}/aprovar', [AdminCommissionController::class, 'approve'])->name('commissions.approve');
        Route::post('comissoes/splits/{split}/pagar', [AdminCommissionController::class, 'markSplitPaid'])->name('commissions.mark-paid');

        // Exportação para portais
        Route::get('exportar', [AdminExportController::class, 'index'])->name('export.index');
        Route::get('exportar/zap-vivareal.xml', [AdminExportController::class, 'zapVivaReal'])->name('export.zap-vivareal');
        Route::get('exportar/olx.xml', [AdminExportController::class, 'olx'])->name('export.olx');

        // Auditoria e privacidade (LGPD)
        Route::get('auditoria', [AdminAuditLogController::class, 'index'])->name('audit.index');
        Route::get('privacidade', [AdminPrivacyRequestController::class, 'index'])->name('privacy.index');
        Route::patch('privacidade/{privacyRequest}', [AdminPrivacyRequestController::class, 'update'])->name('privacy.update');

        Route::get('features', [AdminFeatureController::class, 'index'])->name('features.index');
        Route::post('features', [AdminFeatureController::class, 'store'])->name('features.store');
        Route::delete('features/{feature}', [AdminFeatureController::class, 'destroy'])->name('features.destroy');

        Route::resource('users', AdminUserController::class)->except(['show']);

        Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
        Route::patch('settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });

require __DIR__.'/auth.php';
