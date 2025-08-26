# CSNDR - Centre Scolaire Notre Dame du Rosaire

## 🚀 Démarrage Rapide

### Prérequis
- PHP 7.4+ avec extensions : BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- Composer
- MySQL/MariaDB avec base `csndr_db`
- Node.js 16+ et npm

### Configuration Base de Données
1. **Copier le fichier `.env.example` vers `.env`** dans `csndr-laravel-backend/`
2. **Configurer la base de données** dans `.env` :
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=csndr_db
   DB_USERNAME=root
   DB_PASSWORD=votre_mot_de_passe
   ```

### Démarrage Automatique
```bash
# Dans le répertoire racine du projet
.\start-project.bat
```

### Démarrage Manuel
```bash
# Terminal 1 - Backend Laravel
cd csndr-react-frontend/csndr-laravel-backend
composer install
php artisan key:generate
php artisan serve --host=127.0.0.1 --port=8000

# Terminal 2 - Frontend React
cd csndr-react-frontend
npm install
npm start
```

## 🔑 Comptes de Test Disponibles

**Base de données :** `csndr_db` (déjà configurée et fonctionnelle)

### 👑 Administrateur
- **Email :** `admin@csndr.test`
- **Mot de passe :** `Password123!`
- **Rôle :** Administrateur complet

### 👨‍🏫 Professeurs
- **prof1@csndr.test** / `Password123!` - Classe CP-A
- **prof2@csndr.test** / `Password123!` - Classe CE1-A  
- **prof3@csndr.test** / `Password123!` - Classe CE2-A

### 👨‍👩‍👧‍👦 Parents
- **parent1@csndr.test** / `Password123!` - Famille Durand
- **parent2@csndr.test** / `Password123!` - Famille Moreau
- **parent3@csndr.test** / `Password123!` - Famille Simon

### 👧👦 Élèves
- **eleve1@csndr.test** / `Password123!` - Emma Durand (CP-A)
- **eleve2@csndr.test** / `Password123!` - Lucas Moreau (CE1-A)
- **eleve3@csndr.test** / `Password123!` - Léa Simon (CE2-A)
- **eleve4@csndr.test** / `Password123!` - Thomas Durand (CM1-A)
- **eleve5@csndr.test** / `Password123!` - Jade Moreau (CM2-A)

## 🏗️ Architecture

### Frontend (React 19.1.1)
- **Framework :** React avec Hooks et Context API
- **Routing :** React Router v7
- **Styling :** Tailwind CSS avec palette personnalisée
- **HTTP Client :** Axios avec intercepteurs
- **État :** Context API pour l'authentification

### Backend (Laravel 8.75)
- **Framework :** Laravel avec Sanctum pour l'authentification
- **Base de données :** MySQL/MariaDB avec Eloquent ORM
- **API :** RESTful avec authentification JWT
- **Migrations :** Structure de base de données complète

## ✨ Fonctionnalités

### 🔐 Authentification
- Connexion/déconnexion sécurisée
- Gestion des rôles (Admin, Professeur, Parent, Élève)
- Tokens JWT pour l'API

### 👥 Gestion des Utilisateurs
- CRUD complet des utilisateurs
- Attribution des rôles et classes
- Gestion des relations parent-enfant

### 🏫 Gestion des Classes
- Création/modification/suppression des classes
- Attribution des professeurs aux classes

### 💬 Système de Messagerie
- Conversations privées entre utilisateurs
- Gestion des conversations par rôle

### 📅 Gestion des Événements
- Création et gestion des événements scolaires
- Dates de début et fin

### 📚 Gestion des Devoirs
- Création et attribution des devoirs
- Upload de fichiers
- Gestion des dates limites

### 📊 Gestion des Notes
- Saisie des notes avec coefficients
- Commentaires des professeurs
- Historique des évaluations

## 🔧 Problèmes Résolus

✅ **Authentification :** Remplacement du système mock par l'API Laravel  
✅ **Base de données :** Connexion directe à `csndr_db` sans données mockées  
✅ **API :** Toutes les routes fonctionnelles et testées  
✅ **Comptes de test :** 15 comptes créés avec relations correctes  
✅ **Migrations :** Structure de base de données optimisée  
✅ **Gestion d'erreurs :** Composant Toast standardisé  
✅ **Configuration :** Fichier de configuration centralisé  

## 📁 Structure des Fichiers

```
csndr-react-frontend/
├── src/
│   ├── components/          # Composants React
│   ├── services/            # Services API
│   ├── config/              # Configuration
│   └── assets/              # Ressources statiques
├── csndr-laravel-backend/   # Backend Laravel
│   ├── app/                 # Logique métier
│   ├── database/            # Migrations et seeders
│   ├── routes/              # Routes API
│   └── config/              # Configuration Laravel
└── start-project.bat        # Script de démarrage automatique
```

## 🚨 Dépannage

### Erreur de connexion à la base de données
- Vérifier que MySQL/MariaDB est démarré
- Vérifier les paramètres dans `.env`
- Vérifier que la base `csndr_db` existe

### Erreur 404 sur l'API
- Vérifier que le serveur Laravel est démarré sur le port 8000
- Vérifier que les routes sont bien définies dans `routes/api.php`

### Problème d'authentification
- Vérifier que les comptes de test existent dans la base
- Vérifier que le token JWT est bien envoyé dans les headers

## 📝 Mises à Jour

### Dernière mise à jour : 10/08/2025
- ✅ Création des comptes de test dans `csndr_db`
- ✅ Test complet de l'API d'authentification
- ✅ Vérification des routes protégées
- ✅ Documentation des comptes de test

## 🌐 URLs d'accès

- **Frontend React :** http://localhost:3000
- **Backend Laravel :** http://127.0.0.1:8000
- **API :** http://127.0.0.1:8000/api
- **Documentation Laravel :** https://laravel.com/docs

---

**Projet CSNDR** - Système de gestion scolaire complet et fonctionnel ! 🎓
