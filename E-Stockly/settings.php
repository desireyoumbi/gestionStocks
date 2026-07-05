<?php
$pageTitle = "Paramètres";
$pageSubTitle = "Configuration globale du système";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Paramètres de configuration globale E-Stockly.">
    <title><?php echo $pageTitle; ?> | E-Stockly</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components/components.css">

    <script type="module" src="assets/js/components/components.js"></script>
    <script type="module" src="assets/js/settings.js"></script>
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
                            <li><span>Paramètres</span></li>
                        </ul>
                        <h1>Configuration globale</h1>
                    </div>
                </div>

                <div class="content-grid" style="grid-template-columns: 1.4fr 1fr;">
                    <!-- Company Settings Form -->
                    <section class="panel">
                        <div class="panel-header" style="border-bottom: 1px solid var(--border); padding-bottom: 12px; margin-bottom: 20px;">
                            <h2>Informations de l'entreprise</h2>
                        </div>
                        <form id="settingsForm">
                            <div class="form-grid">
                                <div class="form-field form-field--full">
                                    <label for="companyName">Nom de l'entreprise <span class="required">*</span></label>
                                    <input type="text" id="companyName" name="companyName" required>
                                </div>
                                <div class="form-field form-field--full">
                                    <label for="companyAddress">Adresse physique <span class="required">*</span></label>
                                    <input type="text" id="companyAddress" name="address" required>
                                </div>
                                <div class="form-field">
                                    <label for="companyPhone">Téléphone <span class="required">*</span></label>
                                    <input type="text" id="companyPhone" name="phone" required>
                                </div>
                                <div class="form-field">
                                    <label for="companyEmail">Email de contact <span class="required">*</span></label>
                                    <input type="email" id="companyEmail" name="email" required>
                                </div>
                                <div class="form-field">
                                    <label for="companyCurrency">Devise principale <span class="required">*</span></label>
                                    <select id="companyCurrency" name="currency" required>
                                        <option value="FCFA">Franc CFA (FCFA)</option>
                                        <option value="EUR">Euro (€)</option>
                                        <option value="USD">Dollar ($)</option>
                                    </select>
                                </div>
                                <div class="form-field">
                                    <label for="backupInterval">Sauvegarde automatique <span class="required">*</span></label>
                                    <select id="backupInterval" name="backupInterval" required>
                                        <option value="daily">Quotidienne</option>
                                        <option value="weekly">Hebdomadaire</option>
                                        <option value="monthly">Mensuelle</option>
                                    </select>
                                </div>
                            </div>
                            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                                <button class="primary-action" type="submit"><i class="fa-solid fa-floppy-disk"></i>Enregistrer</button>
                            </div>
                        </form>
                    </section>

                    <!-- Backups and Database Tools -->
                    <div style="display: flex; flex-direction: column; gap: 18px;">
                        <section class="panel">
                            <div class="panel-header" style="border-bottom: 1px solid var(--border); padding-bottom: 12px; margin-bottom: 20px;">
                                <h2>Sauvegardes de données</h2>
                            </div>
                            <p style="font-size: 0.88rem; color: var(--muted); line-height: 1.5; margin-bottom: 20px;">
                                Générez un instantané de la base de données SQL pour restaurer vos informations en cas de panne de serveur.
                            </p>
                            <div style="display: grid; gap: 12px;">
                                <button class="secondary-action" id="downloadBackupBtn" style="justify-content: flex-start; gap: 12px; font-weight: 700;" type="button">
                                    <i class="fa-solid fa-download" style="color: var(--primary);"></i>Sauvegarder la base de données
                                </button>
                                <div style="border-top: 1px solid var(--border); margin: 8px 0;"></div>
                                <label class="secondary-action" style="justify-content: flex-start; gap: 12px; cursor: pointer; font-weight: 700; display: inline-flex; align-items: center;">
                                    <i class="fa-solid fa-upload" style="color: var(--warning);"></i>Restaurer une sauvegarde...
                                    <input type="file" id="uploadBackupInput" style="display: none;" accept=".sql">
                                </label>
                            </div>
                        </section>

                        <section class="panel">
                            <div class="panel-header" style="border-bottom: 1px solid var(--border); padding-bottom: 12px; margin-bottom: 20px;">
                                <h2>Version & Versioning</h2>
                            </div>
                            <ul style="padding: 0; margin: 0; list-style: none; font-size: 0.88rem; display: grid; gap: 10px;">
                                <li style="display: flex; justify-content: space-between;"><strong>Nom de l'application:</strong> <span class="badge is-info">E-Stockly Pro</span></li>
                                <li style="display: flex; justify-content: space-between;"><strong>Version build:</strong> <span>v1.0.4 - Stable</span></li>
                                <li style="display: flex; justify-content: space-between;"><strong>Base de données:</strong> <span>MySQL 8.0</span></li>
                                <li style="display: flex; justify-content: space-between;"><strong>Moteur d'exécution:</strong> <span>PHP 8.2.0</span></li>
                            </ul>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
