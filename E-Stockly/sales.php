<?php
$pageTitle = "Ventes";
$pageSubTitle = "Gestion des sorties de stock";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestion des ventes et sorties de stock E-Stockly.">
    <title><?php echo $pageTitle; ?> | E-Stockly</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components/components.css">

    <script type="module" src="assets/js/components/components.js"></script>
    <script type="module" src="assets/js/sales.js"></script>
</head>
<body>
    <div class="dashboard-shell" id="dashboardShell">
        <?php include 'includes/sidebar.php'; ?>

        <div class="sidebar-backdrop" id="sidebarBackdrop" hidden></div>

        <div class="dashboard-workspace">
            <?php include 'includes/topbar.php'; ?>

            <main class="dashboard-main" id="mainContent">
                <div class="page-header">
                    <div class="page-header-title">
                        <ul class="breadcrumb">
                            <li><a href="dashboard.php">E-Stockly</a></li>
                            <li><span>Ventes</span></li>
                        </ul>
                        <h1>Gestion des ventes</h1>
                    </div>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button class="secondary-action" id="exportSalesBtn" type="button" title="Export à venir">
                            <i class="fa-solid fa-file-export"></i>Export
                        </button>
                        <button class="primary-action" id="addSaleBtn" type="button">
                            <i class="fa-solid fa-cart-plus"></i>Nouvelle vente
                        </button>
                    </div>
                </div>

                <section class="metrics-row" aria-label="Indicateurs ventes">
                    <div class="metric-card">
                        <div class="metric-icon is-primary"><i class="fa-solid fa-receipt"></i></div>
                        <div class="metric-details">
                            <span>Total ventes</span>
                            <strong id="metricTotalSales">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-success"><i class="fa-solid fa-coins"></i></div>
                        <div class="metric-details">
                            <span>Chiffre d'affaires</span>
                            <strong id="metricTotalRevenue">0 FCFA</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-warning"><i class="fa-solid fa-hourglass-half"></i></div>
                        <div class="metric-details">
                            <span>En attente</span>
                            <strong id="metricPendingSales">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-success"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="metric-details">
                            <span>Validées</span>
                            <strong id="metricCompletedSales">0</strong>
                        </div>
                    </div>
                </section>

                <section class="panel" style="padding: 0; border-radius: var(--radius-lg);">
                    <div class="toolbar-wrap" style="border: 0; box-shadow: none; border-radius: 0; margin-bottom: 0; border-bottom: 1px solid var(--border);">
                        <div class="toolbar-controls">
                            <div class="search-field">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="search" id="saleSearch" placeholder="Rechercher par numéro, vendeur ou produit...">
                            </div>
                            <div class="filter-row">
                                <select id="filterStatus" class="filter-select" aria-label="Filtrer par statut">
                                    <option value="">Tous les statuts</option>
                                    <option value="completed">Validée</option>
                                    <option value="pending">En attente</option>
                                    <option value="cancelled">Annulée</option>
                                </select>
                                <select id="filterPayment" class="filter-select" aria-label="Filtrer par paiement">
                                    <option value="">Tous les paiements</option>
                                    <option value="Espèces">Espèces</option>
                                    <option value="Carte Bancaire">Carte Bancaire</option>
                                    <option value="Mobile Money">Mobile Money</option>
                                    <option value="Virement">Virement</option>
                                    <option value="Mock">Mock</option>
                                </select>
                                <select id="filterSeller" class="filter-select" aria-label="Filtrer par vendeur">
                                    <option value="">Tous les vendeurs</option>
                                </select>
                                <select id="sortSales" class="filter-select" aria-label="Trier les ventes">
                                    <option value="date-desc">Date (récent)</option>
                                    <option value="date-asc">Date (ancien)</option>
                                    <option value="total-desc">Montant (décroissant)</option>
                                    <option value="total-asc">Montant (croissant)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table id="salesTable">
                            <thead>
                                <tr>
                                    <th style="width: 90px;">N°</th>
                                    <th>Date</th>
                                    <th>Vendeur</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Montant total</th>
                                    <th>Paiement</th>
                                    <th>Motif</th>
                                    <th>Statut</th>
                                    <th style="width: 100px; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="pagination-container">
                        <span class="pagination-info">
                            Affichage de <strong id="pageStart">0</strong> à <strong id="pageEnd">0</strong> sur <strong id="totalCount">0</strong> ventes
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

    <!-- Sale Form Modal -->
    <div class="modal-overlay" id="saleModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-card modal-card--large">
            <header class="modal-header">
                <h2 id="modalTitle">Nouvelle vente</h2>
                <button class="modal-close" aria-label="Fermer la modal"><i class="fa-solid fa-xmark"></i></button>
            </header>
            <form id="saleForm">
                <input type="hidden" id="saleId" name="id">
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="sProduct">Produit <span class="required">*</span></label>
                            <select id="sProduct" name="productId" required>
                                <option value="">Choisir un produit</option>
                            </select>
                            <p class="field-error"></p>
                        </div>
                        <div class="form-field">
                            <label for="sQty">Quantité <span class="required">*</span></label>
                            <input type="number" id="sQty" name="qty" min="1" required placeholder="1">
                            <p class="field-error"></p>
                        </div>
                        <div class="form-field">
                            <label for="sUnitPrice">Prix unitaire (FCFA) <span class="required">*</span></label>
                            <input type="number" id="sUnitPrice" name="unitPrice" min="0" required placeholder="0">
                            <p class="field-error"></p>
                        </div>
                        <div class="form-field">
                            <label for="sTotal">Montant total (FCFA)</label>
                            <input type="number" id="sTotal" name="totalPrice" readonly placeholder="0">
                        </div>
                        <div class="form-field">
                            <label for="sSeller">Vendeur <span class="required">*</span></label>
                            <select id="sSeller" name="seller" required>
                                <option value="">Choisir un vendeur</option>
                            </select>
                            <p class="field-error"></p>
                        </div>
                        <div class="form-field">
                            <label for="sPayment">Mode de paiement <span class="required">*</span></label>
                            <select id="sPayment" name="paymentMethod" required>
                                <option value="">Choisir un mode</option>
                                <option value="Espèces">Espèces</option>
                                <option value="Carte Bancaire">Carte Bancaire</option>
                                <option value="Mobile Money">Mobile Money</option>
                                <option value="Virement">Virement</option>
                                <option value="Mock">Mock</option>
                            </select>
                            <p class="field-error"></p>
                        </div>
                        <div class="form-field">
                            <label for="sStatus">Statut <span class="required">*</span></label>
                            <select id="sStatus" name="status" required>
                                <option value="completed">Validée</option>
                                <option value="pending">En attente</option>
                                <option value="cancelled">Annulée</option>
                            </select>
                            <p class="field-error"></p>
                        </div>
                        <div class="form-field form-field--full">
                            <label for="sNote">Motif / Note</label>
                            <textarea id="sNote" name="note" placeholder="Motif de la vente, remarques..."></textarea>
                            <p class="field-error"></p>
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
                    <h3 id="deleteTitle">Supprimer la vente</h3>
                    <p>Êtes-vous sûr de vouloir supprimer cette vente ? Le stock sera réintégré automatiquement.</p>
                </div>
            </div>
            <footer class="modal-footer">
                <button class="secondary-action" type="button" data-close-modal>Annuler</button>
                <button class="btn btn-danger" id="confirmDeleteBtn" type="button">Supprimer</button>
            </footer>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
