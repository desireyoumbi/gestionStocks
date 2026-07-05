<?php
$pageTitle = "Produits";
$pageSubTitle = "Gestion des articles";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestion des produits E-Stockly NG-STOCK.">
    <title><?php echo $pageTitle; ?> | E-Stockly</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components/components.css">

    <script type="module" src="assets/js/components/components.js"></script>
    <script type="module" src="assets/js/products.js"></script>
</head>
<body>
    <div class="dashboard-shell" id="dashboardShell">
        <?php include 'includes/sidebar.php'; ?>

        <div class="sidebar-backdrop" id="sidebarBackdrop" hidden></div>

        <div class="dashboard-workspace">
            <?php include 'includes/topbar.php'; ?>

            <main class="dashboard-main" id="mainContent">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="page-header-title">
                        <ul class="breadcrumb">
                            <li><a href="dashboard.php">E-Stockly</a></li>
                            <li><span>Produits</span></li>
                        </ul>
                        <h1>Gestion des produits</h1>
                    </div>
                    <button class="primary-action" id="addProductBtn" type="button">
                        <i class="fa-solid fa-plus"></i>Ajouter un produit
                    </button>
                </div>

                <!-- Metrics Row -->
                <section class="metrics-row" aria-label="Indicateurs produits">
                    <div class="metric-card">
                        <div class="metric-icon is-primary"><i class="fa-solid fa-box"></i></div>
                        <div class="metric-details">
                            <span>Total Produits</span>
                            <strong id="metricTotalProducts">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-success"><i class="fa-solid fa-coins"></i></div>
                        <div class="metric-details">
                            <span>Valeur du stock</span>
                            <strong id="metricStockValue">0 FCFA</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-warning"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div class="metric-details">
                            <span>Stock faible</span>
                            <strong id="metricLowStock">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-danger"><i class="fa-solid fa-circle-xmark"></i></div>
                        <div class="metric-details">
                            <span>Rupture de stock</span>
                            <strong id="metricOutOfStock">0</strong>
                        </div>
                    </div>
                </section>

                <!-- Toolbar & DataTable -->
                <section class="panel" style="padding: 0; border-radius: var(--radius-lg);">
                    <div class="toolbar-wrap" style="border: 0; box-shadow: none; border-radius: 0; margin-bottom: 0; border-bottom: 1px solid var(--border);">
                        <div class="toolbar-controls">
                            <div class="search-field">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="search" id="productSearch" placeholder="Rechercher un produit ou code-barres...">
                            </div>
                            <div class="filter-row">
                                <select id="filterCategory" class="filter-select" aria-label="Filtrer par catégorie">
                                    <option value="">Toutes les catégories</option>
                                    <!-- Dynamic options -->
                                </select>
                                <select id="filterStatus" class="filter-select" aria-label="Filtrer par état de stock">
                                    <option value="">Tous les états</option>
                                    <option value="normal">Normal</option>
                                    <option value="low">Stock faible</option>
                                    <option value="critical">Critique</option>
                                    <option value="empty">Rupture</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-wrap">
                        <table id="productsTable">
                            <thead>
                                <tr>
                                    <th>Code-barres</th>
                                    <th>Nom du produit</th>
                                    <th>Catégorie</th>
                                    <th>Prix Achat</th>
                                    <th>Prix Vente</th>
                                    <th>Stock</th>
                                    <th>État</th>
                                    <th style="width: 100px; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        <span class="pagination-info">
                            Affichage de <strong id="pageStart">0</strong> à <strong id="pageEnd">0</strong> sur <strong id="totalCount">0</strong> produits
                        </span>
                        <div class="pagination-buttons">
                            <button class="pagination-button" id="prevPageBtn" disabled><i class="fa-solid fa-chevron-left" style="margin-right: 6px;"></i>Précédent</button>
                            <button class="pagination-button" id="nextPageBtn" disabled>Suivant<i class="fa-solid fa-chevron-right" style="margin-left: 6px;"></i></button>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Product Form Modal -->
    <div class="modal-overlay" id="productModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-card">
            <header class="modal-header">
                <h2 id="modalTitle">Ajouter un produit</h2>
                <button class="modal-close" aria-label="Fermer la modal"><i class="fa-solid fa-xmark"></i></button>
            </header>
            <form id="productForm">
                <input type="hidden" id="productId" name="id">
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-field form-field--full">
                            <label for="pName">Nom du produit <span class="required">*</span></label>
                            <input type="text" id="pName" name="name" required placeholder="ex: Laptop Dell Latitude 5440">
                            <p class="field-error" id="error_name"></p>
                        </div>
                        <div class="form-field">
                            <label for="pCategory">Catégorie <span class="required">*</span></label>
                            <select id="pCategory" name="categoryId" required>
                                <option value="">Choisir une catégorie</option>
                                <!-- Dynamic categories -->
                            </select>
                            <p class="field-error" id="error_categoryId"></p>
                        </div>
                        <div class="form-field">
                            <label for="pBarcode">Code-barres</label>
                            <input type="text" id="pBarcode" name="barcode" placeholder="ex: 19485038291">
                            <p class="field-error" id="error_barcode"></p>
                        </div>
                        <div class="form-field">
                            <label for="pPurchasePrice">Prix d'achat (FCFA) <span class="required">*</span></label>
                            <input type="number" id="pPurchasePrice" name="purchasePrice" min="0" required placeholder="0">
                            <p class="field-error" id="error_purchasePrice"></p>
                        </div>
                        <div class="form-field">
                            <label for="pPrice">Prix de vente (FCFA) <span class="required">*</span></label>
                            <input type="number" id="pPrice" name="price" min="0" required placeholder="0">
                            <p class="field-error" id="error_price"></p>
                        </div>
                        <div class="form-field">
                            <label for="pStock">Stock initial <span class="required">*</span></label>
                            <input type="number" id="pStock" name="stock" min="0" required placeholder="0">
                            <p class="field-error" id="error_stock"></p>
                        </div>
                        <div class="form-field">
                            <label for="pMinStock">Seuil critique <span class="required">*</span></label>
                            <input type="number" id="pMinStock" name="minStock" min="1" required placeholder="5">
                            <p class="field-error" id="error_minStock"></p>
                        </div>
                        <div class="form-field form-field--full">
                            <label for="pSupplier">Fournisseur <span class="required">*</span></label>
                            <select id="pSupplier" name="supplierId" required>
                                <option value="">Choisir un fournisseur</option>
                                <!-- Dynamic suppliers -->
                            </select>
                            <p class="field-error" id="error_supplierId"></p>
                        </div>
                    </div>
                </div>
                <footer class="modal-footer">
                    <button class="secondary-action" type="button" data-close-modal>Annuler</button>
                    <button class="primary-action" type="submit">Enregistrer</button>
                </footer>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteModal" role="dialog" aria-modal="true" aria-labelledby="deleteTitle">
        <div class="modal-card modal-card--small">
            <div class="dialog-content">
                <span class="dialog-icon is-danger"><i class="fa-solid fa-triangle-exclamation"></i></span>
                <div class="dialog-text">
                    <h3 id="deleteTitle">Supprimer le produit</h3>
                    <p>Êtes-vous sûr de vouloir supprimer ce produit ? Cette action enregistrera une suppression définitive et ne pourra pas être annulée.</p>
                </div>
            </div>
            <footer class="modal-footer">
                <button class="secondary-action" type="button" data-close-modal>Annuler</button>
                <button class="primary-action" id="confirmDeleteBtn" style="background: var(--danger); box-shadow: 0 14px 28px rgba(217, 45, 32, 0.24);" type="button">Supprimer</button>
            </footer>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
