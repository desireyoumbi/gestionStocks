<?php
$pageTitle = "Clients";
$pageSubTitle = "Gestion du portefeuille clients";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestion des clients E-Stockly.">
    <title><?php echo $pageTitle; ?> | E-Stockly</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components/components.css">

    <script type="module" src="assets/js/components/components.js"></script>
    <script type="module" src="assets/js/clients.js"></script>
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
                            <li><span>Clients</span></li>
                        </ul>
                        <h1>Gestion des clients</h1>
                    </div>
                    <button class="primary-action" id="addClientBtn" type="button">
                        <i class="fa-solid fa-plus"></i>Nouveau client
                    </button>
                </div>

                <!-- Metrics Row -->
                <section class="metrics-row" aria-label="Indicateurs clients">
                    <div class="metric-card">
                        <div class="metric-icon is-primary"><i class="fa-solid fa-users"></i></div>
                        <div class="metric-details">
                            <span>Clients Enregistrés</span>
                            <strong id="metricTotalClients">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-success"><i class="fa-solid fa-cart-shopping"></i></div>
                        <div class="metric-details">
                            <span>Ventes réalisées</span>
                            <strong id="metricSalesCount">18</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-secondary"><i class="fa-solid fa-coins"></i></div>
                        <div class="metric-details">
                            <span>Montant Facturé</span>
                            <strong id="metricInvoicedAmount">4 850 000 FCFA</strong>
                        </div>
                    </div>
                </section>

                <!-- Toolbar & DataTable -->
                <section class="panel" style="padding: 0; border-radius: var(--radius-lg);">
                    <div class="toolbar-wrap" style="border: 0; box-shadow: none; border-radius: 0; margin-bottom: 0; border-bottom: 1px solid var(--border);">
                        <div class="toolbar-controls">
                            <div class="search-field">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="search" id="clientSearch" placeholder="Rechercher un client...">
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-wrap">
                        <table id="clientsTable">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">ID</th>
                                    <th>Nom / Entreprise</th>
                                    <th>Téléphone</th>
                                    <th>Email</th>
                                    <th>Adresse</th>
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
                            Affichage de <strong id="pageStart">0</strong> à <strong id="pageEnd">0</strong> sur <strong id="totalCount">0</strong> clients
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

    <!-- Client Form Modal -->
    <div class="modal-overlay" id="clientModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-card modal-card--small">
            <header class="modal-header">
                <h2 id="modalTitle">Ajouter un client</h2>
                <button class="modal-close" aria-label="Fermer la modal"><i class="fa-solid fa-xmark"></i></button>
            </header>
            <form id="clientForm">
                <input type="hidden" id="clientId" name="id">
                <div class="modal-body">
                    <div class="form-field">
                        <label for="cName">Nom complet / Raison sociale <span class="required">*</span></label>
                        <input type="text" id="cName" name="name" required placeholder="ex: Sonatel SA">
                        <p class="field-error" id="error_name"></p>
                    </div>
                    <div class="form-field">
                        <label for="cPhone">Téléphone <span class="required">*</span></label>
                        <input type="text" id="cPhone" name="phone" required placeholder="ex: +221 33 839 00 00">
                        <p class="field-error" id="error_phone"></p>
                    </div>
                    <div class="form-field">
                        <label for="cEmail">Email <span class="required">*</span></label>
                        <input type="email" id="cEmail" name="email" required placeholder="ex: contact@sonatel.sn">
                        <p class="field-error" id="error_email"></p>
                    </div>
                    <div class="form-field">
                        <label for="cAddress">Adresse</label>
                        <input type="text" id="cAddress" name="address" placeholder="ex: Dakar, Sénégal">
                        <p class="field-error" id="error_address"></p>
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
                    <h3 id="deleteTitle">Supprimer le client</h3>
                    <p>Êtes-vous sûr de vouloir supprimer ce client ? Cette action est irréversible.</p>
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
