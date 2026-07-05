<?php
$pageTitle = "Catégories";
$pageSubTitle = "Gestion des articles";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestion des catégories E-Stockly NG-STOCK.">
    <title><?php echo $pageTitle; ?> | E-Stockly</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components/components.css">

    <script type="module" src="assets/js/components/components.js"></script>
    <script type="module" src="assets/js/categories.js"></script>
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
                            <li><span>Catégories</span></li>
                        </ul>
                        <h1>Gestion des catégories</h1>
                    </div>
                    <button class="primary-action" id="addCategoryBtn" type="button">
                        <i class="fa-solid fa-plus"></i>Nouvelle catégorie
                    </button>
                </div>

                <!-- Metrics Row -->
                <section class="metrics-row" aria-label="Indicateurs catégories">
                    <div class="metric-card">
                        <div class="metric-icon is-primary"><i class="fa-solid fa-folder-open"></i></div>
                        <div class="metric-details">
                            <span>Total Catégories</span>
                            <strong id="metricTotalCategories">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-secondary"><i class="fa-solid fa-box"></i></div>
                        <div class="metric-details">
                            <span>Articles Associés</span>
                            <strong id="metricAssociatedProducts">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-success"><i class="fa-solid fa-star"></i></div>
                        <div class="metric-details">
                            <span>Catégorie Active</span>
                            <strong id="metricTopCategory">Aucune</strong>
                        </div>
                    </div>
                </section>

                <!-- Toolbar & DataTable -->
                <section class="panel" style="padding: 0; border-radius: var(--radius-lg);">
                    <div class="toolbar-wrap"
                        style="border: 0; box-shadow: none; border-radius: 0; margin-bottom: 0; border-bottom: 1px solid var(--border);">
                        <div class="toolbar-controls">
                            <div class="search-field">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="search" id="categorySearch" placeholder="Rechercher une catégorie...">
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-wrap">
                        <table id="categoriesTable">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">ID</th>
                                    <th>Nom de la catégorie</th>
                                    <th>Description</th>
                                    <th>Nombre de produits</th>
                                    <th>Valeur totale stock</th>
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
                            Affichage de <strong id="pageStart">0</strong> à <strong id="pageEnd">0</strong> sur <strong
                                id="totalCount">0</strong> catégories
                        </span>
                        <div class="pagination-buttons">
                            <button class="pagination-button" id="prevPageBtn" disabled><i
                                    class="fa-solid fa-chevron-left" style="margin-right: 6px;"></i>Précédent</button>
                            <button class="pagination-button" id="nextPageBtn" disabled>Suivant<i
                                    class="fa-solid fa-chevron-right" style="margin-left: 6px;"></i></button>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Category Form Modal -->
    <div class="modal-overlay" id="categoryModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-card modal-card--small">
            <header class="modal-header">
                <h2 id="modalTitle">Ajouter une catégorie</h2>
                <button class="modal-close" aria-label="Fermer la modal"><i class="fa-solid fa-xmark"></i></button>
            </header>
            <form id="categoryForm">
                <input type="hidden" id="categoryId" name="id">
                <div class="modal-body">
                    <div class="form-field">
                        <label for="cName">Nom de la catégorie <span class="required">*</span></label>
                        <input type="text" id="cName" name="name" required placeholder="ex: Imprimantes">
                        <p class="field-error" id="error_name"></p>
                    </div>
                    <div class="form-field">
                        <label for="cDescription">Description</label>
                        <textarea id="cDescription" name="description"
                            placeholder="ex: Périphériques de sortie d'encre..."></textarea>
                        <p class="field-error" id="error_description"></p>
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
                    <h3 id="deleteTitle">Supprimer la catégorie</h3>
                    <p id="deleteMessage">Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est
                        irréversible.</p>
                </div>
            </div>
            <footer class="modal-footer">
                <button class="secondary-action" type="button" data-close-modal>Annuler</button>
                <button class="primary-action" id="confirmDeleteBtn"
                    style="background: var(--danger); box-shadow: 0 14px 28px rgba(217, 45, 32, 0.24);"
                    type="button">Supprimer</button>
            </footer>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>