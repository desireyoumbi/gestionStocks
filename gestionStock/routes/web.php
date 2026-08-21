<?php

use App\Http\Controllers\AdjustmentController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForcedPasswordController;
use App\Http\Controllers\BoutiqueSettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


// ========  La page d'accueil affiche la vidéo d'introduction =========

Route::get('/', function () {
    return view('Auth.welcome_intro');
})->name('welcome.intro');

// ================= fin de acceuil ==================

// =========== debut des routes libre (connexion et inscription) ============
Route::middleware('guest')->group(function () {
    // Inscription
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    // Connexion
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// ================== fin des routes libres ================================

// ================== debut des routes securisees ===========================
Route::middleware(['auth', 'check.password'])->group(function () {
    
    // Ton tableau de bord
     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // La gestion des utilisateurs (accessible uniquement si l'admin a le droit ET a changé son mdp)
    Route::get('/utilisateurs', [UserController::class, 'index'])->name('user.index');
    Route::get('/utilisateurs/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/utilisateurs', [UserController::class, 'store'])->name('user.store');
    Route::get('/utilisateurs/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/utilisateurs/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/utilisateurs/{user}', [UserController::class, 'destroy'])->name('user.destroy');

    // ... Toutes tes autres pages de la boutique (stocks, ventes, etc.) iront ici aussi !

    // Routes Resource pour les fournisseurs (Génère automatiquement index, create, store, edit, update, destroy)
    Route::resource('suppliers', SupplierController::class);
    // Route Resource pour les produits (Génère automatiquement index, create, store, edit, update, destroy)
    Route::resource('products', ProductController::class);

    //route pour categorie
    Route::resource('categories', CategoryController::class); // ← à ajouter

    //commende futures 
    Route::get('/commandes', [PurchaseOrderController::class, 'index'])->name('purchase_orders.index');
    Route::get('/commandes/create', [PurchaseOrderController::class, 'create'])->name('purchase_orders.create');
    Route::post('/commandes', [PurchaseOrderController::class, 'store'])->name('purchase_orders.store');
    Route::get('/commandes/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase_orders.show');
    Route::get('/commandes/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('purchase_orders.edit');
    Route::put('/commandes/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('purchase_orders.update');
    Route::delete('/commandes/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('purchase_orders.destroy');
    Route::patch('/commandes/{purchaseOrder}/recevoir', [PurchaseOrderController::class, 'receive'])->name('purchase_orders.receive');
    Route::post('/commandes/{purchaseOrder}/annuler', [PurchaseOrderController::class, 'cancel'])->name('purchase_orders.cancel');


    // mouvements 
    // // Liste des mouvements (avec recherche et filtres)
    // Route::get('/movements', [MovementController::class, 'index'])->name('movements.index');
    // // Formulaire de création d'un mouvement
    // Route::get('/movements/create', [MovementController::class, 'create'])->name('movements.create');
    // // Enregistrement du nouveau mouvement//
    // Route::post('/movements', [MovementController::class, 'store'])->name('movements.store');
    // // Affichage des détails d'un mouvement spécifique
    // Route::get('/movements/{movement}', [MovementController::class, 'show'])->name('movements.show');
    // // Formulaire d'édition (modification du motif/date)
    // Route::get('/movements/{movement}/edit', [MovementController::class, 'edit'])->name('movements.edit');
    // // Mise à jour du mouvement
    // Route::put('/movements/{movement}', [MovementController::class, 'update'])->name('movements.update');
    // // Suppression / Annulation d'un mouvement (avec réajustement du stock)
    // Route::delete('/movements/{movement}', [MovementController::class, 'destroy'])->name('movements.destroy');

    // Journal des mouvements (affichage du jour)
// Journal des mouvements global
Route::get('/movements', [MovementController::class, 'index'])->name('movements.index');

// Page dédiée aux Ventes (Liste, Formulaire, Enregistrement, Suppression)
Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
Route::delete('/sales/{id}', [SaleController::class, 'destroy'])->name('sales.destroy');
// Page dédiée aux Approvisionnements
Route::get('/supplies/create', [SupplyController::class, 'create'])->name('supplies.create');
Route::post('/supplies', [SupplyController::class, 'store'])->name('supplies.store');

// approvissionnement 
Route::get('/supplies', [SupplyController::class, 'index'])->name('supplies.index');
Route::get('/supplies/create', [SupplyController::class, 'create'])->name('supplies.create');
Route::post('/supplies', [SupplyController::class, 'store'])->name('supplies.store');
Route::delete('/supplies/{id}', [SupplyController::class, 'destroy'])->name('supplies.destroy');

    //restaurer ou supprimer 
    Route::get('/corbeille', [TrashController::class, 'index'])->name('trash.index');
    Route::post('/corbeille/{type}/{id}/restaurer', [TrashController::class, 'restore'])->name('trash.restore');
    Route::delete('/corbeille/{type}/{id}', [TrashController::class, 'forceDelete'])->name('trash.forceDelete');
});

// ================= fin des routes securisees =======================

// ==========  LE GROUPE DE SECOURS (Connecté, mais SANS le middleware check.password)
Route::middleware(['auth'])->group(function () {
    Route::get('/changer-mot-de-passe-force', [ForcedPasswordController::class, 'showForm'])->name('password.change');
    
    // Remets bien le -force ici pour que l'URL corresponde à ton formulaire
    Route::post('/changer-mot-de-passe-force', [ForcedPasswordController::class, 'update'])->name('password.update.forced');

    //profile 
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    //deconnexion 
    Route::post('/logout' ,[AuthController::class , 'logout'])->name('logout');

    // Gestion paramètres boutique
    Route::get('/settings/boutique', [BoutiqueSettingsController::class, 'index'])->name('settings.boutique');
    Route::patch('/settings/boutique/update', [BoutiqueSettingsController::class, 'update'])->name('boutique.update');
    Route::post('/settings/boutique/transfer', [BoutiqueSettingsController::class, 'transferOwnership'])->name('boutique.transfer');
    Route::delete('/settings/boutique/destroy', [BoutiqueSettingsController::class, 'destroy'])->name('boutique.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    //ajustements
    Route::get('/adjustments', [AdjustmentController::class, 'index'])->name('adjustments.index');
    Route::get('/adjustments/create', [AdjustmentController::class, 'create'])->name('adjustments.create');
    Route::post('/adjustments', [AdjustmentController::class, 'store'])->name('adjustments.store');
});

