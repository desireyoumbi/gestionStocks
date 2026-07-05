<header class="topbar">
    <div class="topbar-title">
        <button class="icon-button menu-toggle" id="menuToggle" type="button" aria-label="Ouvrir le menu" aria-controls="sidebar" aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div>
            <p><?php echo isset($pageSubTitle) ? $pageSubTitle : 'Vue globale'; ?></p>
            <h1><?php echo isset($pageTitle) ? $pageTitle : 'Tableau de bord'; ?></h1>
        </div>
    </div>

    <form class="search-box" role="search" onsubmit="event.preventDefault();">
        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
        <label class="sr-only" for="dashboardSearch">Rechercher</label>
        <input id="dashboardSearch" type="search" placeholder="Rechercher...">
    </form>

    <div class="topbar-actions">
        <a class="icon-button has-dot" href="notifications.php" aria-label="Notifications">
            <i class="fa-solid fa-bell"></i>
        </a>
        <button class="icon-button" type="button" aria-label="Messages">
            <i class="fa-solid fa-message"></i>
        </button>
        <div class="user-menu-wrapper">
            <button class="user-menu" id="userMenuBtn" type="button" aria-label="Menu utilisateur" aria-haspopup="true" aria-expanded="false">
                <span class="avatar" aria-hidden="true">AD</span>
                <span class="user-menu__meta">
                    <strong>Administrateur</strong>
                    <small>Admin</small>
                </span>
                <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
            </button>
            <div class="user-dropdown" id="userDropdown" aria-label="Menu déroulant utilisateur">
                <a href="profile.php"><i class="fa-solid fa-user"></i>Mon Profil</a>
                <a href="settings.php"><i class="fa-solid fa-gear"></i>Paramètres</a>
                <div class="dropdown-divider"></div>
                <a href="login.php" class="logout-link"><i class="fa-solid fa-door-open"></i>Déconnexion</a>
            </div>
        </div>
    </div>
</header>
