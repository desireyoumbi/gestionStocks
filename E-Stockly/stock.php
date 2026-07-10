<?php
$pageTitle = "Stocks";
$pageSubTitle = "Gestion des stocks";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestion des stocks et ajustements E-Stockly NG-STOCK.">
    <title><?php echo $pageTitle; ?> | E-Stockly</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components/components.css">

    <script type="module" src="assets/js/components/components.js"></script>
    <script type="module" src="assets/js/stock.js"></script>
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
                            <li><span>Stock</span></li>
                        </ul>
                        <h1>Gestion des stocks</h1>
                    </div>
                    <button class="primary-action" id="adjustStockBtn" type="button">
                        <i class="fa-solid fa-sliders"></i>Ajuster le stock
                    </button>
                </div>

                <!-- Metrics Row -->
                <section class="metrics-row" aria-label="Indicateurs de stock">
                    <div class="metric-card">
                        <div class="metric-icon is-primary"><i class="fa-solid fa-cubes"></i></div>
                        <div class="metric-details">
                            <span>Quantité en stock</span>
                            <strong id="metricTotalStockQty">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-success"><i class="fa-solid fa-calculator"></i></div>
                        <div class="metric-details">
                            <span>Valeur du stock</span>
                            <strong id="metricStockValue">0 FCFA</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-warning"><i class="fa-solid fa-bell"></i></div>
                        <div class="metric-details">
                            <span>Alertes seuil</span>
                            <strong id="metricAlertCount">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div class="metric-details">
                            <span>Articles en rupture</span>
                            <strong id="metricOutCount">0</strong>
                        </div>
                    </div>
                </section>

                <!-- Toolbar & DataTable -->
                <section class="panel" style="padding: 0; border-radius: var(--radius-lg);">
                    <div class="toolbar-wrap" style="border: 0; box-shadow: none; border-radius: 0; margin-bottom: 0; border-bottom: 1px solid var(--border);">
                        <div class="toolbar-controls">
                            <div class="search-field">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="search" id="stockSearch" placeholder="Rechercher un produit ou catégorie...">
                            </div>
                            <div class="filter-row">
                                <select id="filterCategory" class="filter-select" aria-label="Filtrer par catégorie">
                                    <option value="">Toutes les catégories</option>
                                    <!-- Dynamic -->
                                </select>
                                <select id="filterStatus" class="filter-select" aria-label="Filtrer par alerte">
                                    <option value="">Tous les états</option>
                                    <option value="normal">Normal</option>
                                    <option value="warning">Stock faible/critique</option>
                                    <option value="danger">Rupture de stock</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-wrap">
                        <table id="stockTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom du produit</th>
                                    <th>Catégorie</th>
                                    <th>Stock actuel</th>
                                    <th>Seuil minimum</th>
                                    <th>État</th>
                                    <th style="width: 100px; text-align: right;">Ajustement</th>
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
                            Affichage de <strong id="pageStart">0</strong> à <strong id="pageEnd">0</strong> sur <strong id="totalCount">0</strong> articles
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

    <!-- Stock Adjustment Modal -->
    <div class="modal-overlay" id="adjustModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-card modal-card--small">
            <header class="modal-header">
                <h2 id="modalTitle">Ajustement de stock</h2>
                <button class="modal-close" aria-label="Fermer la modal"><i class="fa-solid fa-xmark"></i></button>
            </header>
            <form id="adjustForm">
                <div class="modal-body">
                    <div class="form-field">
                        <label for="adjProduct">Produit <span class="required">*</span></label>
                        <select id="adjProduct" name="productId" required>
                            <option value="">Choisir un produit</option>
                            <!-- Dynamic options -->
                        </select>
                        <p class="field-error" id="error_productId"></p>
                    </div>
                    <div class="form-field">
                        <label for="adjType">Type d'opération <span class="required">*</span></label>
                        <select id="adjType" name="type" required>
                            <option value="in">Entrée de stock (+)</option>
                            <option value="out">Sortie de stock (-)</option>
                            <option value="adj">Correction manuelle (valeur absolue)</option>
                        </select>
                        <p class="field-error" id="error_type"></p>
                    </div>
                    <div class="form-field">
                        <label for="adjQty">Quantité <span class="required">*</span></label>
                        <input type="number" id="adjQty" name="qty" min="1" required placeholder="ex: 10">
                        <p class="field-error" id="error_qty"></p>
                    </div>
                    <div class="form-field">
                        <label for="adjComment">Motif / Commentaire <span class="required">*</span></label>
                        <textarea id="adjComment" name="comment" required placeholder="ex: Inventaire mensuel, produit défectueux..."></textarea>
                        <p class="field-error" id="error_comment"></p>
                    </div>
                </div>
                <footer class="modal-footer">
                    <button class="secondary-action" type="button" data-close-modal>Annuler</button>
                    <button class="primary-action" type="submit">Appliquer</button>
                </footer>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
