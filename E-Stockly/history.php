<?php
$pageTitle = "Historique";
$pageSubTitle = "Journal des actions utilisateurs";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Journal d'activité et historique d'audit E-Stockly.">
    <title><?php echo $pageTitle; ?> | E-Stockly</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components/components.css">

    <script type="module" src="assets/js/components/components.js"></script>
    <script type="module" src="assets/js/history.js"></script>
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
                            <li><span>Historique</span></li>
                        </ul>
                        <h1>Journal d'activités</h1>
                    </div>
                </div>

                <!-- Metrics Row -->
                <section class="metrics-row" aria-label="Indicateurs de logs" style="grid-template-columns: repeat(2, 1fr);">
                    <div class="metric-card">
                        <div class="metric-icon is-primary"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <div class="metric-details">
                            <span>Total Événements</span>
                            <strong id="metricTotalLogs">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-success"><i class="fa-solid fa-user-check"></i></div>
                        <div class="metric-details">
                            <span>Opérateurs actifs</span>
                            <strong id="metricActiveOperators">0</strong>
                        </div>
                    </div>
                </section>

                <!-- Toolbar & DataTable -->
                <section class="panel" style="padding: 0; border-radius: var(--radius-lg);">
                    <div class="toolbar-wrap" style="border: 0; box-shadow: none; border-radius: 0; margin-bottom: 0; border-bottom: 1px solid var(--border);">
                        <div class="toolbar-controls">
                            <div class="search-field">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="search" id="historySearch" placeholder="Rechercher par action, utilisateur...">
                            </div>
                            <div class="filter-row">
                                <select id="filterModule" class="filter-select" aria-label="Filtrer par module">
                                    <option value="">Tous les modules</option>
                                    <option value="Produits">Produits</option>
                                    <option value="Catégories">Catégories</option>
                                    <option value="Stock">Stock</option>
                                    <option value="Ventes">Ventes</option>
                                    <option value="Fournisseurs">Fournisseurs</option>
                                    <option value="Clients">Clients</option>
                                    <option value="Livraisons">Livraisons</option>
                                    <option value="Utilisateurs">Utilisateurs</option>
                                    <option value="Rôles">Rôles</option>
                                    <option value="Paramètres">Paramètres</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-wrap">
                        <table id="historyTable">
                            <thead>
                                <tr>
                                    <th style="width: 180px;">Horodatage</th>
                                    <th>Utilisateur</th>
                                    <th>Action réalisée</th>
                                    <th>Module</th>
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
                            Affichage de <strong id="pageStart">0</strong> à <strong id="pageEnd">0</strong> sur <strong id="totalCount">0</strong> événements
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

    <?php include 'includes/footer.php'; ?>
</body>
</html>
