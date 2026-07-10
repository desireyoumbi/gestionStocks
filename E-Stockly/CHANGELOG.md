# CHANGELOG

Toutes les modifications notables du projet E-Stockly sont documentées dans ce fichier.

## [Non publié] — 2026-07-05

### Ajouté

- **Module Ventes** (`sales.php`, `assets/js/sales.js`) : CRUD complet, recherche, tri, filtres (statut, paiement, vendeur), pagination, export (TODO).
- Chaque vente déclenche automatiquement : mouvement de stock (sortie), entrée historique, notification si seuil critique.
- Lien **Ventes** dans la sidebar (entre Stock et Livraisons).
- Règle métier centralisée dans `mockDb.js` : un seul Administrateur autorisé (`// TODO Validation PHP`).
- Classe CSS unifiée `.form-control` pour tous les champs de formulaire.
- Famille de boutons unifiée : `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-danger`, `.btn-success`, `.btn-warning`, `.btn-outline`, `.btn-icon`.
- Hiérarchie z-index cohérente (topbar → dropdown → sidebar → modal → toast).
- Fichier `CHANGELOG.md`.

### Corrigé

- Bouton **Ajouter un produit** du Dashboard : réutilise la modale produits via `products.js` et `product_modal.php` (sans duplication).
- Module **Livraisons** : boutons conformes au Design System ; confirmation de réception fonctionnelle (statut Reçue, stock incrémenté, mouvement, notification, historique).
- KPI Dashboard : données calculées depuis `mockDb.js` (ventes et chiffre d'affaires réels).
- Migration automatique des rôles legacy (Manager, Réceptionniste, etc.) vers **Admin** / **Vendeur** uniquement.

### Supprimé

- Module **Clients** (`clients.php`, `assets/js/clients.js`) : page et lien sidebar retirés.
- Permission `clients` retirée des rôles.
- Données et méthodes clients retirées de `mockDb.js`.
- Les ventes ne sont plus associées à un client (vendeur + produits + quantités uniquement).

### Modifié

- `mockDb.receiveDelivery()` : écriture directe en storage, notification de réception.
- `mockDb.saveRole()` : création de nouveaux rôles interdite (2 rôles fixes).
- Affichage des rôles : **Admin** affiché comme **Administrateur** dans l'interface.
