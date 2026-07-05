# Base de données

Nom

estockly

Tables

utilisateurs

roles

permissions

produits

categories

fournisseurs

clients

ventes

lignes_vente

mouvements_stock

livraisons

notifications

journal_actions

parametres

Chaque table devra contenir :

id

created_at

updated_at

Le moteur sera InnoDB.

Toutes les relations utiliseront des clés étrangères.

Toutes les requêtes utiliseront PDO.

Toutes les requêtes seront préparées.

Les mots de passe seront hashés avec password_hash().