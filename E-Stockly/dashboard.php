<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard E-Stockly NG-STOCK pour la gestion de stock, ventes, clients et livraisons.">
    <title>Tableau de bord | E-Stockly</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components/components.css">

    <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script type="module" src="assets/js/components/components.js"></script>
    <script type="module" src="assets/js/dashboard.js"></script>
    <script type="module" src="assets/js/charts.js"></script>
</head>
<body>
    <div class="dashboard-shell" id="dashboardShell">
        <?php include 'includes/sidebar.php'; ?>

        <div class="sidebar-backdrop" id="sidebarBackdrop" hidden></div>

        <div class="dashboard-workspace">
            <?php 
            $pageTitle = "Tableau de bord";
            $pageSubTitle = "Vue globale";
            include 'includes/topbar.php'; 
            ?>

            <main class="dashboard-main" id="mainContent">
                <section class="welcome-panel section-reveal" aria-labelledby="welcomeTitle">
                    <div>
                        <p class="eyebrow">Bonjour,</p>
                        <h2 id="welcomeTitle">Administrateur</h2>
                        <span class="current-date" id="currentDate">Chargement de la date...</span>
                    </div>
                    <div class="quick-buttons" aria-label="Boutons rapides">
                        <button class="primary-action" type="button"><i class="fa-solid fa-plus"></i>Ajouter un produit</button>
                        <button class="secondary-action" type="button"><i class="fa-solid fa-receipt"></i>Nouvelle vente</button>
                        <button class="secondary-action" type="button"><i class="fa-solid fa-boxes-packing"></i>Entree de stock</button>
                    </div>
                </section>

                <section class="kpi-grid" aria-label="Indicateurs cles">
                    <article class="kpi-card section-reveal">
                        <div class="kpi-card__top"><span><i class="fa-solid fa-box"></i></span><small>+8.4%</small></div>
                        <h3>Produits</h3>
                        <strong class="counter" data-count="1245">0</strong>
                        <div class="sparkline" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></div>
                    </article>
                    <article class="kpi-card section-reveal">
                        <div class="kpi-card__top"><span><i class="fa-solid fa-triangle-exclamation"></i></span><small class="is-warning">-3.1%</small></div>
                        <h3>Stock faible</h3>
                        <strong class="counter" data-count="12">0</strong>
                        <div class="sparkline is-warning" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></div>
                    </article>
                    <article class="kpi-card section-reveal">
                        <div class="kpi-card__top"><span><i class="fa-solid fa-cart-shopping"></i></span><small>+18%</small></div>
                        <h3>Ventes</h3>
                        <strong class="counter" data-count="28">0</strong>
                        <div class="sparkline" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></div>
                    </article>
                    <article class="kpi-card section-reveal">
                        <div class="kpi-card__top"><span><i class="fa-solid fa-coins"></i></span><small>+21%</small></div>
                        <h3>Chiffre d'affaires</h3>
                        <strong class="counter" data-count="4850000" data-currency="true">0</strong>
                        <div class="sparkline" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></div>
                    </article>
                </section>

                <section class="content-grid">
                    <article class="panel alerts-panel section-reveal">
                        <div class="panel-header">
                            <div>
                                <p class="eyebrow">Priorites</p>
                                <h2>Alertes intelligentes</h2>
                            </div>
                            <span class="status-pill">3 alertes</span>
                        </div>
                        <ul class="alert-list">
                            <li><span class="alert-icon is-danger"><i class="fa-solid fa-arrow-trend-down"></i></span>12 produits sous le seuil critique</li>
                            <li><span class="alert-icon is-success"><i class="fa-solid fa-arrow-trend-up"></i></span>Les ventes augmentent de 18 %</li>
                            <li><span class="alert-icon is-accent"><i class="fa-solid fa-star"></i></span>Le produit le plus vendu est HP EliteBook</li>
                        </ul>
                    </article>

                    <article class="panel chart-panel section-reveal">
                        <div class="panel-header"><h2>Ventes mensuelles</h2><span>2026</span></div>
                        <div class="chart-box"><canvas id="monthlySalesChart"></canvas></div>
                    </article>

                    <article class="panel chart-panel section-reveal">
                        <div class="panel-header"><h2>Chiffre d'affaires</h2><span>Evolution</span></div>
                        <div class="chart-box"><canvas id="revenueChart"></canvas></div>
                    </article>

                    <article class="panel chart-panel section-reveal">
                        <div class="panel-header"><h2>Repartition des produits</h2><span>Categories</span></div>
                        <div class="chart-box"><canvas id="productsChart"></canvas></div>
                    </article>
                </section>

                <section class="data-layout">
                    <div class="data-layout__main-column">
                        <article class="panel table-panel section-reveal">
                            <div class="panel-header"><h2>Stock faible</h2><a href="#">Voir tout</a></div>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr><th>Produit</th><th>Categorie</th><th>Stock</th><th>Seuil</th><th>Etat</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>HP EliteBook</td><td>Ordinateurs</td><td>4</td><td>15</td><td><span class="badge is-danger">Critique</span></td></tr>
                                        <tr><td>Canon LBP 6030</td><td>Imprimantes</td><td>7</td><td>12</td><td><span class="badge is-warning">Faible</span></td></tr>
                                        <tr><td>SSD Kingston 1To</td><td>Stockage</td><td>8</td><td>20</td><td><span class="badge is-warning">Faible</span></td></tr>
                                        <tr><td>Routeur TP-Link</td><td>Reseau</td><td>3</td><td>10</td><td><span class="badge is-danger">Critique</span></td></tr>
                                        <tr><td>Souris Logitech</td><td>Accessoires</td><td>11</td><td>25</td><td><span class="badge is-warning">Faible</span></td></tr>
                                        <tr><td>Onduleur APC</td><td>Energie</td><td>2</td><td>8</td><td><span class="badge is-danger">Critique</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </article>

                        <article class="panel table-panel section-reveal" style="margin-top: 18px;">
                            <div class="panel-header"><h2>Produits populaires</h2><span>Top ventes</span></div>
                            <div class="table-wrap">
                                <table>
                                    <thead><tr><th>Nom</th><th>Ventes</th><th>Revenu</th><th>Evolution</th></tr></thead>
                                    <tbody>
                                        <tr><td>HP EliteBook</td><td>184</td><td>92 000 000 FCFA</td><td><span class="badge is-success">+24%</span></td></tr>
                                        <tr><td>SSD Kingston 1To</td><td>142</td><td>14 200 000 FCFA</td><td><span class="badge is-success">+18%</span></td></tr>
                                        <tr><td>Canon LBP 6030</td><td>89</td><td>13 350 000 FCFA</td><td><span class="badge is-neutral">+4%</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </article>
                    </div>

                    <article class="panel timeline-panel section-reveal">
                        <div class="panel-header"><h2>Activites recentes</h2><span>Aujourd'hui</span></div>
                        <ol class="timeline">
                            <li><span><i class="fa-solid fa-plus"></i></span><div><time>09:15</time><p>Ajout produit Dell Latitude 5440</p></div></li>
                            <li><span><i class="fa-solid fa-cart-shopping"></i></span><div><time>10:05</time><p>Nouvelle vente validee</p></div></li>
                            <li><span><i class="fa-solid fa-inbox"></i></span><div><time>11:30</time><p>Entree de stock : 40 accessoires</p></div></li>
                        </ol>
                    </article>
                </section>

                <section class="quick-grid section-reveal" aria-label="Actions rapides">
                    <a href="#"><i class="fa-solid fa-plus"></i><span>Ajouter un produit</span></a>
                    <a href="#"><i class="fa-solid fa-inbox"></i><span>Entree de stock</span></a>
                    <a href="#"><i class="fa-solid fa-cart-plus"></i><span>Nouvelle vente</span></a>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
