<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Connexion securisee a NG-STOCK, application de gestion de stock et d'entrepots.">
    <title>Connexion | NG-STOCK</title>

    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="assets/css/login.css">
    <script defer src="assets/js/login.js"></script>
</head>
<body>
    <main class="login-page" aria-label="Page de connexion NG-STOCK">
        <section class="brand-panel" aria-label="Presentation de NG-STOCK">
            <div class="brand-panel__overlay"></div>

            <div class="brand-panel__content">
                <a class="brand" href="login.php" aria-label="Accueil NG-STOCK">
                    <img class="brand__logo" src="assets/images/logo.png" alt="Logo NG-STOCK" width="48" height="48">
                    <span class="brand__name">NG-STOCK</span>
                </a>

                <div class="brand-hero">
                    <p class="brand-hero__eyebrow">Plateforme de gestion intelligente</p>
                    <h1 class="brand-hero__title">
                        Gerez votre entreprise
                        <span>en toute simplicite</span>
                    </h1>
                    <p class="brand-hero__text">
                        Centralisez vos stocks, vos ventes et vos approvisionnements dans un espace clair,
                        rapide et concu pour les equipes qui avancent.
                    </p>
                </div>

                <div class="feature-grid" aria-label="Fonctionnalites principales">
                    <article class="feature-card">
                        <span class="feature-card__icon" aria-hidden="true"><i class="fa-solid fa-boxes-stacked"></i></span>
                        <h2>Gestion des stocks</h2>
                        <p>Suivi en temps reel</p>
                    </article>

                    <article class="feature-card">
                        <span class="feature-card__icon" aria-hidden="true"><i class="fa-solid fa-chart-line"></i></span>
                        <h2>Analytics</h2>
                        <p>Tableaux de bord intelligents</p>
                    </article>

                    <article class="feature-card">
                        <span class="feature-card__icon" aria-hidden="true"><i class="fa-solid fa-warehouse"></i></span>
                        <h2>Multi-entrepots</h2>
                        <p>Gestion centralisee</p>
                    </article>

                    <article class="feature-card">
                        <span class="feature-card__icon" aria-hidden="true"><i class="fa-solid fa-truck-ramp-box"></i></span>
                        <h2>Ventes &amp; Approvisionnement</h2>
                        <p>Processus optimises</p>
                    </article>
                </div>

                <dl class="brand-stats" aria-label="Indicateurs NG-STOCK">
                    <div>
                        <dt>600+</dt>
                        <dd>Entreprises</dd>
                    </div>
                    <div>
                        <dt>99.9%</dt>
                        <dd>Disponibilite</dd>
                    </div>
                    <div>
                        <dt>24/7</dt>
                        <dd>Support</dd>
                    </div>
                </dl>
            </div>
        </section>

        <section class="auth-panel" aria-label="Connexion a votre espace">
            <div class="auth-card">
                <div class="auth-header">
                    <img class="auth-header__logo" src="assets/images/logo.png" alt="" width="42" height="42" aria-hidden="true">
                    <p class="auth-header__product">NG-STOCK</p>
                    <h2>Bon retour ! 👋</h2>
                    <p>Connectez-vous pour acceder a votre espace de gestion</p>
                </div>

                <form class="login-form" id="loginForm" novalidate>
                    <div class="form-field">
                        <label for="email">Email</label>
                        <div class="input-shell">
                            <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                            <input type="email" id="email" name="email" autocomplete="email" placeholder="admin@ngstock.com" aria-describedby="emailError" required>
                        </div>
                        <p class="field-error" id="emailError" aria-live="polite"></p>
                    </div>

                    <div class="form-field">
                        <label for="password">Mot de passe</label>
                        <div class="input-shell">
                            <i class="fa-solid fa-lock" aria-hidden="true"></i>
                            <input type="password" id="password" name="password" autocomplete="current-password" placeholder="Votre mot de passe" aria-describedby="passwordError" required>
                            <button class="password-toggle" id="togglePassword" type="button" aria-label="Afficher le mot de passe" aria-pressed="false">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        <p class="field-error" id="passwordError" aria-live="polite"></p>
                    </div>

                    <div class="form-options">
                        <label class="remember-option" for="remember">
                            <input type="checkbox" id="remember" name="remember">
                            <span>Se souvenir de moi</span>
                        </label>
                        <a href="#" class="forgot-link">Mot de passe oublie</a>
                    </div>

                    <div class="form-alert" id="formAlert" role="status" aria-live="polite"></div>

                    <button class="submit-button" id="submitButton" type="submit">
                        <span class="submit-button__label">Se connecter</span>
                        <span class="submit-button__loader" aria-hidden="true"></span>
                    </button>
                </form>

                <div class="security-row" aria-label="Informations de securite">
                    <span>Securise par</span>
                    <span class="security-badge"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> SSL 256-bit</span>
                    <span class="security-badge"><i class="fa-solid fa-user-lock" aria-hidden="true"></i> RGPD</span>
                </div>

                <footer class="auth-footer">
                    <p>&copy; NG-STOCK</p>
                </footer>
            </div>
        </section>
    </main>
</body>
</html>
