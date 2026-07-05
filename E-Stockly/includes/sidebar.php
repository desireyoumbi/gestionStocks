<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="sidebar" aria-label="Navigation principale">
    <a class="sidebar-brand" href="dashboard.php" aria-label="E-Stockly tableau de bord">
        <span class="brand-mark" aria-hidden="true"><i class="fa-solid fa-cubes-stacked"></i></span>
        <span>
            <strong>E-STOCKLY</strong>
            <small>NG-STOCK</small>
        </span>
    </a>

    <nav class="sidebar-nav" aria-label="Menu">
        <a class="nav-link <?php echo $currentPage === 'dashboard.php' ? 'is-active' : ''; ?>" href="dashboard.php" <?php echo $currentPage === 'dashboard.php' ? 'aria-current="page"' : ''; ?>><i class="fa-solid fa-house"></i><span>Tableau de bord</span></a>
        <a class="nav-link <?php echo $currentPage === 'products.php' ? 'is-active' : ''; ?>" href="products.php" <?php echo $currentPage === 'products.php' ? 'aria-current="page"' : ''; ?>><i class="fa-solid fa-box"></i><span>Produits</span></a>
        <a class="nav-link <?php echo $currentPage === 'categories.php' ? 'is-active' : ''; ?>" href="categories.php" <?php echo $currentPage === 'categories.php' ? 'aria-current="page"' : ''; ?>><i class="fa-solid fa-folder-open"></i><span>Catégories</span></a>
        <a class="nav-link <?php echo $currentPage === 'stock.php' ? 'is-active' : ''; ?>" href="stock.php" <?php echo $currentPage === 'stock.php' ? 'aria-current="page"' : ''; ?>><i class="fa-solid fa-warehouse"></i><span>Stock</span></a>
        <a class="nav-link <?php echo $currentPage === 'movements.php' ? 'is-active' : ''; ?>" href="movements.php" <?php echo $currentPage === 'movements.php' ? 'aria-current="page"' : ''; ?>><i class="fa-solid fa-truck-ramp-box"></i><span>Mouvements</span></a>
        <a class="nav-link <?php echo $currentPage === 'suppliers.php' ? 'is-active' : ''; ?>" href="suppliers.php" <?php echo $currentPage === 'suppliers.php' ? 'aria-current="page"' : ''; ?>><i class="fa-solid fa-truck-field"></i><span>Fournisseurs</span></a>
        <a class="nav-link <?php echo $currentPage === 'clients.php' ? 'is-active' : ''; ?>" href="clients.php" <?php echo $currentPage === 'clients.php' ? 'aria-current="page"' : ''; ?>><i class="fa-solid fa-users"></i><span>Clients</span></a>
        <a class="nav-link <?php echo $currentPage === 'deliveries.php' ? 'is-active' : ''; ?>" href="deliveries.php" <?php echo $currentPage === 'deliveries.php' ? 'aria-current="page"' : ''; ?>><i class="fa-solid fa-truck"></i><span>Livraisons</span></a>
        <a class="nav-link <?php echo $currentPage === 'users.php' ? 'is-active' : ''; ?>" href="users.php" <?php echo $currentPage === 'users.php' ? 'aria-current="page"' : ''; ?>><i class="fa-solid fa-user-gear"></i><span>Utilisateurs</span></a>
        <a class="nav-link <?php echo $currentPage === 'roles.php' ? 'is-active' : ''; ?>" href="roles.php" <?php echo $currentPage === 'roles.php' ? 'aria-current="page"' : ''; ?>><i class="fa-solid fa-user-shield"></i><span>Rôles</span></a>
        <a class="nav-link <?php echo $currentPage === 'history.php' ? 'is-active' : ''; ?>" href="history.php" <?php echo $currentPage === 'history.php' ? 'aria-current="page"' : ''; ?>><i class="fa-solid fa-clock-rotate-left"></i><span>Historique</span></a>
        <a class="nav-link <?php echo $currentPage === 'settings.php' ? 'is-active' : ''; ?>" href="settings.php" <?php echo $currentPage === 'settings.php' ? 'aria-current="page"' : ''; ?>><i class="fa-solid fa-gear"></i><span>Paramètres</span></a>
    </nav>

    <a class="nav-link nav-link--logout" href="login.php"><i class="fa-solid fa-door-open"></i><span>Déconnexion</span></a>
</aside>
