<?php
$pageTitle = "Notifications";
$pageSubTitle = "Alertes système et notifications d'activité";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Centre de notifications E-Stockly.">
    <title><?php echo $pageTitle; ?> | E-Stockly</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components/components.css">

    <style>
        .notification-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            transition: background var(--transition);
        }
        .notification-item.is-unread {
            background: rgba(109, 93, 252, 0.04);
        }
        .notification-item:hover {
            background: rgba(23, 32, 51, 0.02);
        }
        .notification-item__left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .notification-item__text h3 {
            margin: 0;
            font-size: 0.93rem;
            font-weight: 700;
            color: var(--text);
        }
        .notification-item__text p {
            margin: 4px 0 0;
            font-size: 0.8rem;
            color: var(--muted);
        }
    </style>

    <script type="module" src="assets/js/components/components.js"></script>
    <script type="module" src="assets/js/notifications.js"></script>
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
                            <li><span>Notifications</span></li>
                        </ul>
                        <h1>Centre de notifications</h1>
                    </div>
                    <button class="secondary-action" id="markAllReadBtn" type="button">
                        <i class="fa-solid fa-check-double"></i>Tout marquer comme lu
                    </button>
                </div>

                <!-- Metrics Row -->
                <section class="metrics-row" aria-label="Indicateurs de notifications" style="grid-template-columns: repeat(2, 1fr);">
                    <div class="metric-card">
                        <div class="metric-icon is-primary"><i class="fa-solid fa-bell"></i></div>
                        <div class="metric-details">
                            <span>Total Notifications</span>
                            <strong id="metricTotalNotifs">0</strong>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon is-warning"><i class="fa-solid fa-envelope-open"></i></div>
                        <div class="metric-details">
                            <span>Messages non lus</span>
                            <strong id="metricUnreadNotifs">0</strong>
                        </div>
                    </div>
                </section>

                <!-- Notifications List Panel -->
                <section class="panel" style="padding: 0; border-radius: var(--radius-lg);">
                    <div id="notificationsListContainer">
                        <!-- Dynamic list of items -->
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        <span class="pagination-info">
                            Affichage de <strong id="pageStart">0</strong> à <strong id="pageEnd">0</strong> sur <strong id="totalCount">0</strong> notifications
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
