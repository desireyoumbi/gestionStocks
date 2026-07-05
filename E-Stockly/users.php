<?php
$pageTitle = "Utilisateurs";
$pageSubTitle = "Gestion des comptes du personnel";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestion des utilisateurs E-Stockly.">
    <title><?php echo $pageTitle; ?> | E-Stockly</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components/components.css">

    <script type="module" src="assets/js/components/components.js"></script>
    <script type="module" src="assets/js/users.js"></script>
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
                            <li><span>Utilisateurs</span></li>
                        </ul>
                        <h1>Gestion des utilisateurs</h1>
                    </div>
                    <button class="primary-action" id="addUserBtn" type="button">
                        <i class="fa-solid fa-user-plus"></i>Nouvel utilisateur
                    </button>
                </div>

                <!-- Metrics Row -->
                <section class="metrics-row" aria-label="Indicateurs utilisateurs" style="grid-template-columns: repeat(2, 1fr);">
                    <div class="metric-card">
                        <div class="metric-icon is-primary"><i class="fa-solid fa-users"></i></div>
                        <div class="metric-details">
                            <span>Utilisateurs actifs</span>
                            <strong id="metricActiveUsers">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-success"><i class="fa-solid fa-user-shield"></i></div>
                        <div class="metric-details">
                            <span>Total rôles configurés</span>
                            <strong id="metricRolesCount">0</strong>
                        </div>
                    </div>
                </section>

                <!-- Toolbar & DataTable -->
                <section class="panel" style="padding: 0; border-radius: var(--radius-lg);">
                    <div class="toolbar-wrap" style="border: 0; box-shadow: none; border-radius: 0; margin-bottom: 0; border-bottom: 1px solid var(--border);">
                        <div class="toolbar-controls">
                            <div class="search-field">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="search" id="userSearch" placeholder="Rechercher un utilisateur...">
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-wrap">
                        <table id="usersTable">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">ID</th>
                                    <th>Nom complet</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
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
                            Affichage de <strong id="pageStart">0</strong> à <strong id="pageEnd">0</strong> sur <strong id="totalCount">0</strong> utilisateurs
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

    <!-- User Form Modal -->
    <div class="modal-overlay" id="userModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-card modal-card--small">
            <header class="modal-header">
                <h2 id="modalTitle">Ajouter un utilisateur</h2>
                <button class="modal-close" aria-label="Fermer la modal"><i class="fa-solid fa-xmark"></i></button>
            </header>
            <form id="userForm">
                <input type="hidden" id="userId" name="id">
                <div class="modal-body">
                    <div class="form-field">
                        <label for="uName">Nom complet <span class="required">*</span></label>
                        <input type="text" id="uName" name="name" required placeholder="ex: Charles Ndiaye">
                        <p class="field-error" id="error_name"></p>
                    </div>
                    <div class="form-field">
                        <label for="uEmail">Email <span class="required">*</span></label>
                        <input type="email" id="uEmail" name="email" autocomplete="email" required placeholder="ex: charles@ngstock.com">
                        <p class="field-error" id="error_email"></p>
                    </div>
                    <div class="form-field" id="passwordFieldContainer">
                        <label for="uPassword">Mot de passe <span class="required">*</span></label>
                        <input type="password" id="uPassword" name="password" placeholder="Mot de passe">
                        <p class="field-error" id="error_password"></p>
                    </div>
                    <div class="form-field">
                        <label for="uRole">Rôle <span class="required">*</span></label>
                        <select id="uRole" name="role" required>
                            <option value="">Choisir un rôle</option>
                            <!-- Dynamic roles -->
                        </select>
                        <p class="field-error" id="error_role"></p>
                    </div>
                    <div class="form-field">
                        <label for="uStatus">Statut <span class="required">*</span></label>
                        <select id="uStatus" name="status" required>
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
                        </select>
                        <p class="field-error" id="error_status"></p>
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
                    <h3 id="deleteTitle">Supprimer l'utilisateur</h3>
                    <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est définitive.</p>
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
