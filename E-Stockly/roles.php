<?php
$pageTitle = "Rôles";
$pageSubTitle = "Gestion des rôles et des autorisations d'accès";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestion des rôles et autorisations E-Stockly.">
    <title><?php echo $pageTitle; ?> | E-Stockly</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components/components.css">

    <script type="module" src="assets/js/components/components.js"></script>
    <script type="module" src="assets/js/roles.js"></script>
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
                            <li><span>Rôles</span></li>
                        </ul>
                        <h1>Gestion des rôles</h1>
                    </div>
                    <button class="primary-action" id="addRoleBtn" type="button">
                        <i class="fa-solid fa-user-shield"></i>Nouveau rôle
                    </button>
                </div>

                <!-- Metrics Row -->
                <section class="metrics-row" aria-label="Indicateurs de rôles" style="grid-template-columns: repeat(2, 1fr);">
                    <div class="metric-card">
                        <div class="metric-icon is-primary"><i class="fa-solid fa-shield-halved"></i></div>
                        <div class="metric-details">
                            <span>Rôles configurés</span>
                            <strong id="metricRolesCount">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-success"><i class="fa-solid fa-key"></i></div>
                        <div class="metric-details">
                            <span>Total des permissions</span>
                            <strong id="metricPermissionsCount">11</strong>
                        </div>
                    </div>
                </section>

                <!-- DataTable -->
                <section class="panel" style="padding: 0; border-radius: var(--radius-lg);">
                    <div class="table-wrap">
                        <table id="rolesTable">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">ID</th>
                                    <th style="width: 180px;">Nom du rôle</th>
                                    <th>Description</th>
                                    <th>Permissions accordées</th>
                                    <th style="width: 100px; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Role Form Modal -->
    <div class="modal-overlay" id="roleModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-card">
            <header class="modal-header">
                <h2 id="modalTitle">Ajouter un rôle</h2>
                <button class="modal-close" aria-label="Fermer la modal"><i class="fa-solid fa-xmark"></i></button>
            </header>
            <form id="roleForm">
                <input type="hidden" id="roleId" name="id">
                <div class="modal-body">
                    <div class="form-field">
                        <label for="rName">Nom du rôle <span class="required">*</span></label>
                        <input type="text" id="rName" name="name" required placeholder="ex: Magasinier">
                        <p class="field-error" id="error_name"></p>
                    </div>
                    <div class="form-field">
                        <label for="rDescription">Description <span class="required">*</span></label>
                        <textarea id="rDescription" name="description" required placeholder="ex: Responsable de la gestion des stocks..."></textarea>
                        <p class="field-error" id="error_description"></p>
                    </div>
                    
                    <div class="form-field form-field--full" style="margin-top: 12px;">
                        <label>Permissions du rôle <span class="required">*</span></label>
                        <div id="permissionsGrid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 8px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="products" style="width: auto;">
                                <span>Produits (Voir/Editer)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="categories" style="width: auto;">
                                <span>Catégories (Voir/Editer)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="stock" style="width: auto;">
                                <span>Stock (Ajustements)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="movements" style="width: auto;">
                                <span>Mouvements (Logs)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="suppliers" style="width: auto;">
                                <span>Fournisseurs (Partenaires)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="clients" style="width: auto;">
                                <span>Clients (Dossiers)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="deliveries" style="width: auto;">
                                <span>Livraisons (Suivi)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="users" style="width: auto;">
                                <span>Utilisateurs (Comptes)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="roles" style="width: auto;">
                                <span>Rôles (Droits)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="history" style="width: auto;">
                                <span>Historique (Audit)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="settings" style="width: auto;">
                                <span>Paramètres (Système)</span>
                            </label>
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

    <?php include 'includes/footer.php'; ?>
</body>
</html>
