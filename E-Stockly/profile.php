<?php
$pageTitle = "Profil";
$pageSubTitle = "Gestion du compte Administrateur";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Profil utilisateur E-Stockly.">
    <title><?php echo $pageTitle; ?> | E-Stockly</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components/components.css">

    <script type="module" src="assets/js/components/components.js"></script>
    <script type="module" src="assets/js/profile.js"></script>
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
                            <li><span>Profil</span></li>
                        </ul>
                        <h1>Mon profil</h1>
                    </div>
                </div>

                <div class="content-grid" style="grid-template-columns: 1.4fr 1fr;">
                    <!-- Profile settings -->
                    <section class="panel">
                        <div class="panel-header" style="border-bottom: 1px solid var(--border); padding-bottom: 12px; margin-bottom: 20px;">
                            <h2>Modifier mes informations</h2>
                        </div>
                        <form id="profileForm">
                            <div class="form-grid">
                                <div class="form-field form-field--full">
                                    <label for="uName">Nom complet <span class="required">*</span></label>
                                    <input type="text" id="uName" name="name" required placeholder="Administrateur">
                                    <p class="field-error" id="error_name"></p>
                                </div>
                                <div class="form-field form-field--full">
                                    <label for="uEmail">Email <span class="required">*</span></label>
                                    <input type="email" id="uEmail" name="email" required autocomplete="email" placeholder="admin@ngstock.com">
                                    <p class="field-error" id="error_email"></p>
                                </div>
                                <div class="form-field form-field--full" style="border-top: 1px solid var(--border); padding-top: 18px; margin-top: 8px;">
                                    <h3 style="margin: 0 0 14px; font-size: 1rem; font-weight: 800;">Changement de mot de passe</h3>
                                </div>
                                <div class="form-field form-field--full">
                                    <label for="oldPassword">Mot de passe actuel</label>
                                    <input type="password" id="oldPassword" name="oldPassword" placeholder="Votre mot de passe actuel">
                                    <p class="field-error" id="error_oldPassword"></p>
                                </div>
                                <div class="form-field">
                                    <label for="newPassword">Nouveau mot de passe</label>
                                    <input type="password" id="newPassword" name="newPassword" placeholder="Nouveau mot de passe">
                                    <p class="field-error" id="error_newPassword"></p>
                                </div>
                                <div class="form-field">
                                    <label for="confirmPassword">Confirmer le nouveau mot de passe</label>
                                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmer le mot de passe">
                                    <p class="field-error" id="error_confirmPassword"></p>
                                </div>
                            </div>
                            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                                <button class="primary-action" type="submit"><i class="fa-solid fa-check"></i>Sauvegarder les modifications</button>
                            </div>
                        </form>
                    </section>

                    <!-- Account status and info -->
                    <div style="display: flex; flex-direction: column; gap: 18px;">
                        <section class="panel" style="text-align: center; padding: 24px;">
                            <div class="avatar" style="width: 72px; height: 72px; font-size: 1.6rem; font-weight: 800; margin: 0 auto 16px;">AD</div>
                            <h2 id="metaName" style="margin: 0 0 4px; font-size: 1.15rem; font-weight: 800;">Administrateur</h2>
                            <p id="metaEmail" style="margin: 0 0 14px; font-size: 0.88rem; color: var(--muted);">admin@ngstock.com</p>
                            <span class="badge is-info">Rôle : Admin</span>
                        </section>

                        <section class="panel">
                            <div class="panel-header" style="border-bottom: 1px solid var(--border); padding-bottom: 12px; margin-bottom: 20px;">
                                <h2>Sécurité & Règles</h2>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--muted); line-height: 1.5; margin: 0;">
                                - Le mot de passe doit faire au moins 6 caractères.<br>
                                - Utilisez des lettres majuscules, minuscules et des chiffres.<br>
                                - Le changement de mot de passe déconnectera les autres sessions actives.
                            </p>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
