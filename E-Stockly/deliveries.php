<?php
$pageTitle = "Livraisons";
$pageSubTitle = "Suivi des fournisseurs";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestion des livraisons et approvisionnements E-Stockly.">
    <title><?php echo $pageTitle; ?> | E-Stockly</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components/components.css">

    <script type="module" src="assets/js/components/components.js"></script>
    <script type="module" src="assets/js/deliveries.js"></script>
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
                            <li><span>Livraisons</span></li>
                        </ul>
                        <h1>Gestion des livraisons</h1>
                    </div>
                    <button class="primary-action btn btn-primary" id="addDeliveryBtn" type="button">
                        <i class="fa-solid fa-truck"></i>Nouvelle commande
                    </button>
                </div>

                <!-- Metrics Row -->
                <section class="metrics-row" aria-label="Indicateurs livraisons">
                    <div class="metric-card">
                        <div class="metric-icon is-primary"><i class="fa-solid fa-truck"></i></div>
                        <div class="metric-details">
                            <span>Total Commandes</span>
                            <strong id="metricTotalDeliveries">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-warning"><i class="fa-solid fa-hourglass-half"></i></div>
                        <div class="metric-details">
                            <span>En attente</span>
                            <strong id="metricPendingDeliveries">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-success"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="metric-details">
                            <span>Reçues</span>
                            <strong id="metricReceivedDeliveries">0</strong>
                        </div>
                    </div>
                </section>

                <!-- Toolbar & DataTable -->
                <section class="panel" style="padding: 0; border-radius: var(--radius-lg);">
                    <div class="toolbar-wrap" style="border: 0; box-shadow: none; border-radius: 0; margin-bottom: 0; border-bottom: 1px solid var(--border);">
                        <div class="toolbar-controls">
                            <div class="search-field">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="search" id="deliverySearch" placeholder="Rechercher par fournisseur ou ID...">
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-wrap">
                        <table id="deliveriesTable">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">N° Commande</th>
                                    <th>Fournisseur</th>
                                    <th>Date attendue</th>
                                    <th>Nombre d'articles</th>
                                    <th>Statut</th>
                                    <th style="width: 150px; text-align: right;">Actions</th>
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
                            Affichage de <strong id="pageStart">0</strong> à <strong id="pageEnd">0</strong> sur <strong id="totalCount">0</strong> livraisons
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

    <!-- Delivery Form Modal -->
    <div class="modal-overlay" id="deliveryModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-card">
            <header class="modal-header">
                <h2 id="modalTitle">Nouvelle commande fournisseur</h2>
                <button class="modal-close" aria-label="Fermer la modal"><i class="fa-solid fa-xmark"></i></button>
            </header>
            <form id="deliveryForm">
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-field form-field--full">
                            <label for="dSupplier">Fournisseur <span class="required">*</span></label>
                            <select id="dSupplier" name="supplierId" required>
                                <option value="">Choisir un fournisseur</option>
                                <!-- Dynamic suppliers -->
                            </select>
                            <p class="field-error" id="error_supplierId"></p>
                        </div>
                        <div class="form-field form-field--full">
                            <label for="dDate">Date de réception attendue <span class="required">*</span></label>
                            <input type="date" id="dDate" name="dateExpected" required>
                            <p class="field-error" id="error_dateExpected"></p>
                        </div>
                        <div class="form-field form-field--full">
                            <label>Sélectionner les articles <span class="required">*</span></label>
                            <div id="itemsSelectionList" style="display: grid; gap: 10px; margin-top: 6px;">
                                <!-- Dynamic list of products with qty inputs -->
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="modal-footer">
                    <button class="secondary-action" type="button" data-close-modal>Annuler</button>
                    <button class="primary-action" type="submit">Commander</button>
                </footer>
            </form>
        </div>
    </div>

    <!-- Confirm Delivery Receipt Modal -->
    <div class="modal-overlay" id="confirmReceiptModal" role="dialog" aria-modal="true" aria-labelledby="receiptTitle">
        <div class="modal-card modal-card--small">
            <div class="dialog-content">
                <span class="dialog-icon is-warning"><i class="fa-solid fa-truck-ramp-box"></i></span>
                <div class="dialog-text">
                    <h3 id="receiptTitle">Confirmer la réception</h3>
                    <p>Voulez-vous marquer cette commande comme reçue ? Cela incrémentera automatiquement les stocks des articles correspondants.</p>
                </div>
            </div>
            <footer class="modal-footer">
                <button class="secondary-action" type="button" data-close-modal>Annuler</button>
                <button class="btn btn-success" id="btnConfirmReceipt" type="button">Confirmer la réception</button>
            </footer>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
