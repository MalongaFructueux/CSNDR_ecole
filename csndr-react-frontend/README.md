# Centre Scolaire Notre Dame du Rosaire - Système de Gestion

## Description

Système de gestion complet pour le Centre Scolaire Notre Dame du Rosaire de Brazzaville, Congo. Cette application permet la gestion des utilisateurs, des messages, des événements, des devoirs et des notes selon les rôles définis.

## Fonctionnalités

### 🔐 Système d'authentification
- Connexion sécurisée avec tokens JWT
- Gestion des rôles utilisateurs (Admin, Professeur, Parent, Élève)
- Interface adaptée selon le rôle de l'utilisateur

### 💬 Système de messagerie
- Envoi et réception de messages entre utilisateurs
- Conversations en temps réel
- Restrictions selon les rôles :
  - **Admin** : Peut discuter avec tout le monde
  - **Professeur** : Peut discuter avec admin, autres professeurs, parents et élèves de sa classe
  - **Parent** : Peut discuter avec admin, professeurs et ses enfants
  - **Élève** : Accès limité aux messages

### 📅 Gestion des événements
- **Admin uniquement** : Création, modification et suppression d'événements
- Consultation des événements par tous les utilisateurs
- Interface moderne avec design responsive

### 📚 Gestion des devoirs
- **Admin et Professeurs** : Création et gestion des devoirs
- **Parents** : Consultation des devoirs de leurs enfants
- **Élèves** : Consultation des devoirs de leur classe
- Dates limites et descriptions détaillées

### 📊 Gestion des notes
- **Admin et Professeurs** : Création et gestion des notes
- **Parents** : Consultation des notes de leurs enfants
- **Élèves** : Consultation de leurs propres notes
- Système de notation sur 20 avec commentaires

### 👥 Gestion des utilisateurs
- **Admin uniquement** : Création et gestion des comptes utilisateurs
- Attribution des rôles et classes
- Interface d'administration complète

## Architecture Technique

### Frontend (React.js)
- **Framework** : React 18 avec Hooks
- **Styling** : Tailwind CSS avec design system personnalisé
- **Routing** : React Router v6
- **State Management** : useState et useEffect
- **UI Components** : Composants modulaires réutilisables

### Backend (Laravel)
- **Framework** : Laravel 10
- **Authentication** : Laravel Sanctum (JWT)
- **Database** : MySQL avec migrations
- **API** : RESTful API avec validation
- **CORS** : Configuration pour développement

## Structure des Rôles

### 🎯 Admin
- Accès complet à toutes les fonctionnalités
- Gestion des utilisateurs, classes, événements
- Création de devoirs et notes
- Messagerie avec tous les utilisateurs

### 👨‍🏫 Professeur
- Gestion des devoirs pour ses classes
- Création et modification de notes
- Messagerie avec admin, collègues, parents et élèves
- Consultation des événements

### 👨‍👩‍👧‍👦 Parent
- Consultation des devoirs de ses enfants
- Consultation des notes de ses enfants
- Messagerie avec admin, professeurs et ses enfants
- Consultation des événements

### 👨‍🎓 Élève
- Consultation de ses devoirs
- Consultation de ses notes
- Messagerie limitée
- Consultation des événements

## Installation

### Prérequis
- Node.js 16+ et npm
- PHP 8.1+ et Composer
- MySQL 8.0+
- Laravel CLI

### Frontend
```bash
cd csndr-react-frontend
npm install
npm start
```

### Backend
```bash
cd csndr-react-frontend/csndr-laravel-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Configuration

### Variables d'environnement Backend
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csndr_db
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

### Configuration Frontend
Le frontend est configuré pour se connecter à `http://127.0.0.1:8000/api` par défaut.

## Design System

### Couleurs
- **Primary Blue** : #1D4ED8 (Bleu principal)
- **Secondary Blue** : #3B82F6 (Bleu secondaire)
- **Success Green** : #10B981 (Vert succès)
- **Warning Yellow** : #F59E0B (Jaune avertissement)
- **Error Red** : #EF4444 (Rouge erreur)

### Rôles et Couleurs
- **Admin** : Rouge (#DC2626)
- **Professeur** : Vert (#059669)
- **Parent** : Bleu (#2563EB)
- **Élève** : Violet (#7C3AED)

## API Endpoints

### Authentication
- `POST /api/auth/login` - Connexion
- `POST /api/auth/logout` - Déconnexion

### Messages
- `GET /api/messages` - Liste des messages
- `GET /api/messages/conversations` - Conversations
- `GET /api/messages/available-users` - Utilisateurs disponibles
- `POST /api/messages` - Envoyer un message
- `GET /api/messages/{id}` - Messages d'une conversation

### Événements
- `GET /api/events` - Liste des événements
- `POST /api/events` - Créer un événement (Admin)
- `PUT /api/events/{id}` - Modifier un événement (Admin)
- `DELETE /api/events/{id}` - Supprimer un événement (Admin)

### Devoirs
- `GET /api/homework` - Liste des devoirs
- `POST /api/homework` - Créer un devoir (Admin/Prof)
- `PUT /api/homework/{id}` - Modifier un devoir (Admin/Prof)
- `DELETE /api/homework/{id}` - Supprimer un devoir (Admin/Prof)

### Notes
- `GET /api/grades` - Liste des notes
- `POST /api/grades` - Créer une note (Admin/Prof)
- `PUT /api/grades/{id}` - Modifier une note (Admin/Prof)
- `DELETE /api/grades/{id}` - Supprimer une note (Admin/Prof)

## Sécurité

- Authentification JWT avec Laravel Sanctum
- Validation des données côté serveur
- Protection CSRF
- Gestion des permissions par rôle
- Validation des accès aux ressources

## Déploiement

### Production
1. Configurer les variables d'environnement
2. Optimiser Laravel (`php artisan config:cache`, `php artisan route:cache`)
3. Build du frontend (`npm run build`)
4. Configurer le serveur web (Apache/Nginx)
5. Configurer SSL/TLS

## Support

Pour toute question ou problème, contactez l'équipe de développement.

## Licence

Projet privé pour le Centre Scolaire Notre Dame du Rosaire de Brazzaville.
