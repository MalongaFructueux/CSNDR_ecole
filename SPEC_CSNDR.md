# CSNDR — Cahier des charges complet (Backend Laravel + Front React)

## Sommaire
- [1) Contexte & objectifs](#1-contexte--objectifs)
- [2) Périmètre fonctionnel (MVP+)](#2-périmètre-fonctionnel-mvp)
- [3) Architecture & stacks](#3-architecture--stacks)
- [4) Directives MVC (Laravel)](#4-directives-mvc-laravel)
- [5) Base de données (critique)](#5-base-de-données-critique)
- [6) API REST (endpoints & conventions)](#6-api-rest-endpoints--conventions)
- [7) Frontend React (structure & composants)](#7-frontend-react-structure--composants)
- [8) Charte graphique (guidelines)](#8-charte-graphique-guidelines)
- [9) Qualité, commentaires & conventions](#9-qualité-commentaires--conventions)
- [10) Sécurité & performances](#10-sécurité--performances)
- [11) Environnements & déploiement](#11-environnements--déploiement)
- [12) Livrables attendus](#12-livrables-attendus)
- [13) Check-list qualité (à la livraison)](#13-check-list-qualité-à-la-livraison)
- [14) Planning de réalisation (suggestion)](#14-planning-de-réalisation-suggestion)
- [15) Points hérités à corriger impérativement](#15-points-hérités-à-corriger-impérativement)

---

## 1) Contexte & objectifs
- **But**: Plateforme école (gestion élèves, classes, devoirs, notes, évènements, messagerie, menus cantine) avec accès par rôles.
- **Public**: Administration, enseignants, parents, élèves.
- **Objectifs clés**:
  - Portail web responsive, fiable, sécurisé, déployable IONOS.
  - API REST Laravel propre, DB normalisée InnoDB, contraintes FK.
  - Front React modulaire, bonne UX, sans URLs hardcodées.
  - Code commenté, testé, documenté.

## 2) Périmètre fonctionnel (MVP+)
- **Authentification & rôles**: inscription/activation (par admin), login, reset. Rôles: `admin`, `enseignant`, `parent`, `eleve`.
- **Utilisateurs**: 
  - Admin: CRUD users, lier parent↔enfant.
  - Enseignant: gère ses classes, saisit devoirs/notes.
  - Parent: consulte enfant(s), devoirs, notes, messages, évènements, menus.
  - Élève: voit ses devoirs, notes, messages, évènements, menus.
- **Classes**: CRUD, affectations profs/élèves.
- **Devoirs**: CRUD, pièces jointes, date de rendu, remises par élèves (upload).
- **Notes**: saisie par prof, consultation, export CSV.
- **Évènements**: agenda, visibilité selon rôles.
- **Messagerie**: messages simples, fils de discussion, état lu/non-lu.
- **Menus**: menus cantine par jour/semaine.
- **Tableau de bord**: cartes synthèse par rôle.
- **Recherche / filtres** et **journalisation** des actions sensibles.

## 3) Architecture & stacks
- **Backend**: Laravel 10+, PHP 8.2+, MySQL 8+ (InnoDB).
- **Frontend**: React 18+, Vite/CRA, React Router, Axios.
- **Sécurité**: Laravel Sanctum (ou Passport), validation forte, rate limit, CORS.
- **Déploiement**: IONOS mutualisé, `.env` dédiés, gestion de caches Laravel.

## 4) Directives MVC (Laravel)
- **Routes** (`routes/api.php`) minimalistes: aucune logique métier.
- **Contrôleurs** (`app/Http/Controllers/*`) par domaine:
  - `AuthController`, `UserController`, `ClassController`, `DevoirController`, `NoteController`, `EvenementController`, `MessageController`, `MenuController`, `UploadController`.
- **Services** (`app/Services/*`) pour la logique métier réutilisable.
- **Requests** (`app/Http/Requests/*`) pour validations.
- **Resources** (`app/Http/Resources/*`) pour sérialiser les réponses.
- **Policies** pour autorisations par rôle.
- **Mails/Notifications** (`app/Mail`, `app/Notifications`) pour resets et alertes.

## 5) Base de données (critique)
- **Moteur**: InnoDB uniquement. Aucune table MyISAM.
- **Utilisateurs**: champ `password` (hash bcrypt/argon2id), jamais `mot_de_passe`.
- **Schéma recommandé**:
  - `users` (id, name, email unique, password, role enum[admin,enseignant,parent,eleve], timestamps)
  - `eleves_parents` (parent_id FK→users, eleve_id FK→users)
  - `classes` (id, nom, niveau, annee_scolaire, professeur_id FK→users)
  - `classes_eleves` (classe_id FK, eleve_id FK, unique pair)
  - `devoirs` (id, classe_id, titre, description, date_rendu, fichier_path?, created_by FK)
  - `notes` (id, eleve_id, devoir_id?, matiere, note decimal, coef, commentaire)
  - `evenements` (id, titre, description, date, visible_pour enum[all,parents,eleves,enseignants])
  - `messages` (id, sujet, contenu, from_user_id, to_user_id, lu bool, parent_id? pour fil)
  - `menus` (id, date, entree, plat, dessert, allergenes json?)
  - `personal_access_tokens` (Sanctum)
- **Contraintes & index**: FK avec `on delete cascade`, index sur clés d’accès fréquentes.
- **Seeders**: rôles + comptes de démo.
- **Migrations**: propres, idempotentes.

## 6) API REST (endpoints & conventions)
- **Base URL**: fournie par env front (`REACT_APP_API_URL`), jamais hardcodée.
- **Auth**:
  - `POST /api/auth/login` → token
  - `POST /api/auth/logout`
  - `GET /api/auth/me`
- **Users**:
  - `GET /api/users?role=`
  - `POST /api/users` (admin)
  - `PATCH /api/users/{id}`
  - `DELETE /api/users/{id}`
- **Classes**: `GET /api/classes`, `POST /api/classes`, `POST /api/classes/{id}/eleves`
- **Devoirs**: `GET /api/devoirs?...`, `POST /api/devoirs`, `GET /api/devoirs/{id}`, `POST /api/devoirs/{id}/remises`
- **Notes**: `GET /api/notes?...`, `POST /api/notes`
- **Évènements**: `GET /api/evenements?...`, `POST /api/evenements`
- **Messages**: `GET /api/messages?...`, `POST /api/messages`, `POST /api/messages/{id}/reply`, `POST /api/messages/{id}/read`
- **Menus**: `GET /api/menus?week=YYYY-Www`, `POST /api/menus`
- **Conventions**:
  - Réponses JSON: `data`, `meta`, `errors`.
  - Pagination standard `?page=N`.
  - Codes HTTP corrects (200/201/204/400/401/403/404/422/500).
  - Validation via FormRequest.
  - Policies/middlewares par rôle.
  - Uploads: `storage/app/public/...` avec `Storage::url` (symlink `public/storage`).

## 7) Frontend React (structure & composants)
- **Arborescence `src/`**:
  - `components/` (UI génériques: Button, Card, Modal, Table, FormField, Badge)
  - `features/`
    - `auth/` (Login, ForgotPassword, hooks `useAuth`)
    - `users/` (liste, fiche, rôles)
    - `classes/` (liste, affectations)
    - `devoirs/` (liste, détail, création, remise)
    - `notes/` (consultation, saisie)
    - `evenements/` (agenda)
    - `messages/` (inbox, thread)
    - `menus/` (semaine)
  - `pages/` (DashboardAdmin, DashboardProf, DashboardParent, DashboardEleve)
  - `routes/` (guarded routes par rôle)
  - `services/` (`api.js` Axios + interceptors 401)
  - `store/` (Redux/Zustand si nécessaire)
  - `styles/` (variables thème)
- **Composants clés**:
  - Auth: `LoginForm`, `ResetPasswordForm`, `RoleGuard`
  - Layout: `AppLayout`, `Sidebar`, `Topbar`, `Breadcrumbs`
  - Tableaux: `DataTable` (tri, filtre, pagination)
  - Formulaires: `ClassForm`, `DevoirForm`, `NoteForm`, `EventForm`, `MenuForm`, `UserForm`
  - Messages: `ThreadList`, `MessageComposer`, `MessageItem`
  - Dashboard: `KpiCard`, `UpcomingList`, `QuickActions`
- **UX**: loaders/erreurs unifiés, toasts, confirmations, accessibilité (ARIA, focus).

## 8) Charte graphique (guidelines)
- **Couleurs**: Primaire `#1E88E5`, Secondaire `#00ACC1`, Accent `#FDD835`, Fond `#F5F7FB`, Texte `#263238`, Succès `#2E7D32`, Alerte `#F9A825`, Danger `#C62828`.
- **Typo**: Inter ou Roboto (400/500/700).
- **Espacements**: échelle 4/8/12/16/24/32 px.
- **Rayons**: 8px.
- **Boutons**: primaires (fond primaire/texte blanc), secondaires (bord 1px primaire), hover +8% luminosité.
- **Champs**: h=40px, placeholder gris 500, focus anneau primaire 2px.
- **Tables**: zebra rows, header sticky, padding 12px.
- **Dark mode**: optionnel ultérieur. Logo/Favicon en `public/`.

## 9) Qualité, commentaires & conventions
- **Commentaires**:
  - PHP: DocBlocks classes/méthodes publiques, expliquer règles métiers/algorithmes.
  - JS: JSDoc sur fonctions exportées, commentaires au-dessus des blocs complexes.
- **Lint/Format**: PHP-CS-Fixer (PSR-12), ESLint + Prettier.
- **Tests**: PHPUnit (Auth, Devoirs, Notes, Policies), Vitest/Jest + RTL côté front.
- **Commits**: Conventional Commits.
- **CI**: linters + tests.

## 10) Sécurité & performances
- **Mots de passe**: `password` hashé, reset tokens, throttle login.
- **CORS**: restreint au domaine front.
- **Rate limiting**: `/api/*` 60/min (ajuster).
- **Validation**: stricte côté serveur.
- **Logs**: `storage/logs/laravel.log`, rotation.
- **Cache**: config/routes, invalidation post-deploy.
- **Uploads**: taille max 10MB, extensions whitelist, antivirus (optionnel).
- **Headers**: `X-Content-Type-Options`, `X-Frame-Options`, `X-XSS-Protection`, CSP si possible.

## 11) Environnements & déploiement
- **.env**:
  - `APP_KEY` obligatoire, `APP_ENV=production` en prod, `APP_DEBUG=false`.
  - Connexion DB prod IONOS.
- **Permissions**: `storage/` et `bootstrap/cache/` en écriture.
- **Post-deploy**:
  - `php artisan migrate --force`
  - `php artisan storage:link`
  - `php artisan config:clear && php artisan cache:clear && php artisan route:clear`
  - Vérifier `storage/logs/laravel.log`.
- **Build front**:
  - `.env` front: `REACT_APP_API_URL=https://votre-domaine/api`
  - Bundle minifié, gzip/brotli si possible.

## 12) Livrables attendus
- **Code source** structuré:
  - Backend `csndr-laravel-backend/`
  - Front `csndr-react-frontend/`
- **Docs**:
  - `README.md` (setup, scripts, déploiement)
  - `API_SPEC.md` (routes, payloads, exemples)
  - `DB_SCHEMA.sql` (DDL InnoDB + contraintes)
  - `STYLEGUIDE.md` (charte graphique, composants UI)
- **Tests**: couverture min 60% domaines critiques.
- **Seed**: jeu de données de démo.

## 13) Check-list qualité (à la livraison)
- **DB**: InnoDB, FK actives, `users.password` OK.
- **Auth**: login/logout/me OK, policies OK par rôle.
- **API**: pas d’URL hardcodée, erreurs 422/401/403/404 cohérentes.
- **Front**: pas de boucles de redirection, interceptors 401 gérés.
- **Perf**: Lighthouse > 80, pas d’erreurs console.
- **Prod**: `.env` avec `APP_KEY`, caches nettoyés.
- **Permissions**: `storage/`, `bootstrap/cache/` OK.

## 14) Planning de réalisation (suggestion)
- **S1**: DB + migrations + seeders, Auth + Users + Roles.
- **S2**: Classes + Devoirs + Uploads + Notes.
- **S3**: Évènements + Messagerie + Menus.
- **S4**: Front UI/UX, tests, docs, déploiement.

## 15) Points hérités à corriger impérativement
- Ne pas laisser de contrôleurs vides (`ClassController.php`, `UseController.php`).
- Remplacer tout `mot_de_passe` par `password` côté DB et code.
- Supprimer toute dépendance MyISAM; utiliser InnoDB + contraintes FK.
- Déporter la logique des routes vers des contrôleurs dédiés.
- Retirer toute URL API hardcodée (`localhost:8000`) dans `src/services/api.js` au profit d’une variable d’environnement.
- Éviter les redirections forcées vers `/login` non contrôlées (boucles).
