# DESIGN SYSTEM - E-STOCKLY

Version : 1.0

Ce document définit les règles de conception de l'application E-Stockly.

Toutes les nouvelles pages doivent respecter ces règles.

Aucune exception.

---

# Philosophie

E-Stockly est un logiciel de gestion de stock.

Le design doit inspirer :

- professionnalisme
- simplicité
- rapidité
- modernité
- confiance

Le style est inspiré de :

- Stripe
- Linear
- Supabase
- Vercel
- Notion
- GitHub
- Clerk

Le design doit rester sobre.

Ne jamais surcharger l'interface.

Les informations importantes doivent être visibles immédiatement.

---

# Palette

## Couleurs principales

Primary

#6D5DFC

Secondary

#0F6FFF

Accent

#FF9F1C

---

## Couleurs neutres

White

#FFFFFF

Background

#F7F8FB

Surface

#FFFFFF

Border

#E4E8F0

Text

#172033

Muted

#67718A

---

## Etats

Success

#039855

Warning

#F79009

Danger

#D92D20

Info

#0F6FFF

---

# Typographie

Police

Inter

Fallback

system-ui

Segoe UI

Arial

---

Tailles

Titre principal

40px

Titre section

28px

Sous titre

22px

Titre carte

18px

Texte

16px

Petit texte

14px

Légende

12px

Utiliser clamp() lorsque nécessaire.

---

# Espacements

4

8

12

16

20

24

32

40

48

64

Ne jamais utiliser des valeurs aléatoires.

---

# Border Radius

Petit

10px

Moyen

18px

Grand

28px

Boutons

14px

Badges

999px

---

# Ombres

Cartes

0 20px 60px rgba(0,0,0,.08)

Modales

0 30px 90px rgba(0,0,0,.12)

Dropdown

0 14px 40px rgba(0,0,0,.08)

---

# Glassmorphism

background

rgba(255,255,255,.14)

backdrop-filter

blur(18px)

border

1px solid rgba(255,255,255,.20)

Ne jamais exagérer le blur.

---

# Boutons

Primaire

Dégradé violet

Texte blanc

Secondaire

Fond blanc

Contour gris

Danger

Fond rouge

Succès

Fond vert

Tous les boutons possèdent

transition

hover

focus

disabled

loading

---

# Icônes

Utiliser Font Awesome.

Toujours placer une icône avant le texte.

Les tailles :

16

18

20

24

Ne jamais dépasser 24px.

---

# Cartes

Toutes les cartes utilisent :

background blanc

coins arrondis

ombre légère

padding constant

titre

contenu

hover subtil

---

# Tableaux

Entête fixe

Lignes alternées

Hover

Responsive

overflow-x:auto

Badges colorés

---

# Formulaires

Labels obligatoires.

Placeholder secondaire.

Focus violet.

Erreur rouge.

Succès vert.

Validation JS.

Validation PHP plus tard.

---

# Graphiques

Utiliser uniquement Chart.js.

Types autorisés :

Bar

Line

Doughnut

Pie

Radar

Les couleurs doivent respecter la palette.

---

# KPI

Chaque KPI contient

Icône

Titre

Valeur

Evolution

Mini graphique

Hover

Animation compteur

---

# Sidebar

Toujours fixe sur Desktop.

Rétractable.

Mobile

Menu burger.

Le menu actif est mis en évidence.

---

# Navbar

Titre

Recherche

Notifications

Messages

Profil

Date

---

# Modales

Fond sombre

Blur

Animation fade

Fermeture ESC

Fermeture clic extérieur

---

# Notifications

Toast

Coin supérieur droit

4 secondes

Animations

Fade

Slide

---

# Responsive

Desktop

>=1440

4 colonnes

Laptop

1200

3 colonnes

Tablette

992

2 colonnes

Mobile

768

1 colonne

Petit mobile

480

Tout doit rester lisible.

Aucun scroll horizontal.

---

# Animations

Durée

150ms à 250ms

Ease

ease

Utiliser :

opacity

translateY

scale léger

Jamais des animations agressives.

Respecter

prefers-reduced-motion

---

# Accessibilité

Labels

ARIA

Focus visible

Navigation clavier

Contrastes WCAG

---

# Performance

Images optimisées.

Lazy loading.

Aucune bibliothèque inutile.

CSS modulaire.

JavaScript modulaire.

---

# Qualité

Chaque nouvelle fonctionnalité doit :

respecter la palette

respecter les composants

être responsive

être documentée

être facilement connectable au backend

ne jamais casser le code existant

ne jamais renommer les classes

ne jamais modifier l'architecture sans justification

---

# Convention CSS

Utiliser BEM.

Exemple

card

card__header

card__title

card--warning

---

# Convention JavaScript

ES6

Fonctions courtes.

Variables explicites.

Modules.

Commentaires :

// TODO PHP

// TODO API

// TODO SQL

---

# Convention PHP

PDO

MVC

Requêtes préparées

password_hash()

password_verify()

Sessions sécurisées

CSRF

Validation serveur

---

# Objectif final

Le résultat doit être comparable à un ERP professionnel moderne.

L'utilisateur doit pouvoir utiliser l'application sans formation particulière.

L'interface doit rester cohérente sur toutes les pages.

Chaque nouvelle page doit donner l'impression d'avoir été conçue par la même équipe.