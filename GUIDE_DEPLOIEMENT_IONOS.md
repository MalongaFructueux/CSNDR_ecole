# 🚀 Guide de Déploiement CSNDR sur IONOS

## 📋 Checklist de Déploiement

### ✅ **Étape 1: Préparer les fichiers**
- [ ] Uploader tous les fichiers Laravel dans le dossier racine IONOS
- [ ] Uploader le build React dans `public/`
- [ ] Vérifier que `csndr_db_corrected.sql` est disponible

### ✅ **Étape 2: Configuration Base de Données**
```sql
-- 1. Créer une nouvelle base de données sur IONOS
-- 2. Importer OBLIGATOIREMENT csndr_db_corrected.sql (PAS l'ancien fichier)
-- 3. Vérifier que les tables utilisent InnoDB
-- 4. Confirmer que la table users a un champ 'password' (pas 'mot_de_passe')
```

### ✅ **Étape 3: Configuration .env**
Créer/modifier le fichier `.env` sur IONOS :

```env
APP_NAME=CSNDR
APP_ENV=production
APP_KEY=base64:K8vJ2mR9nQ7xL3pW5tY8uI1oP4sA6dF9gH2jK5lM8nB7cV0zX3qE6rT9yU2iO5pS
APP_DEBUG=false
APP_URL=https://csndr-gestion.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=VOTRE_NOM_BDD_IONOS
DB_USERNAME=VOTRE_USER_IONOS
DB_PASSWORD=VOTRE_PASS_IONOS

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.ionos.fr
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@csndr-gestion.com
MAIL_FROM_NAME="${APP_NAME}"
```

### ✅ **Étape 4: Permissions Fichiers**
```bash
# Exécuter ces commandes sur IONOS via SSH ou cPanel Terminal
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env
chmod 644 .htaccess
```

### ✅ **Étape 5: Commandes Laravel**
```bash
# Vider tous les caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimiser pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### ✅ **Étape 6: Configuration Apache**
Vérifier que le fichier `.htaccess` dans `public/` contient :

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]

    # CORS Headers for API
    Header always set Access-Control-Allow-Origin "*"
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"
    
    # Handle preflight OPTIONS requests
    RewriteCond %{REQUEST_METHOD} OPTIONS
    RewriteRule ^(.*)$ $1 [R=200,L]
</IfModule>
```

### ✅ **Étape 7: Vérification et Tests**

1. **Vérifier les logs d'erreur** :
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Tester les endpoints API** :
   - `GET /api/auth/check-email` 
   - `POST /api/auth/register`
   - `POST /api/auth/login`

3. **Vérifier la base de données** :
   ```sql
   DESCRIBE users; -- Doit montrer 'password', pas 'mot_de_passe'
   SHOW TABLE STATUS; -- Toutes les tables doivent être InnoDB
   ```

## 🚨 **Problèmes Courants et Solutions**

### Erreur 500 - Internal Server Error
- ✅ Vérifier que `APP_KEY` est définie dans `.env`
- ✅ Vérifier les permissions des dossiers `storage/` et `bootstrap/cache/`
- ✅ Consulter `storage/logs/laravel.log`
- ✅ Vider tous les caches Laravel

### Erreur de Base de Données
- ✅ Vérifier les paramètres de connexion dans `.env`
- ✅ S'assurer que `csndr_db_corrected.sql` a été importé
- ✅ Vérifier que les tables utilisent InnoDB

### Erreur CORS
- ✅ Vérifier le fichier `.htaccess`
- ✅ S'assurer que `fruitcake/laravel-cors` est installé
- ✅ Vérifier la configuration dans `config/cors.php`

### Erreur d'Authentification
- ✅ Vérifier que la table `users` utilise le champ `password`
- ✅ Vérifier que `laravel/sanctum` est installé et configuré
- ✅ S'assurer que la table `personal_access_tokens` existe

## 📞 **Support de Débogage**

Si l'erreur persiste après ces étapes :

1. **Activer le mode debug temporairement** :
   ```env
   APP_DEBUG=true
   LOG_LEVEL=debug
   ```

2. **Consulter les logs détaillés** :
   ```bash
   tail -100 storage/logs/laravel.log
   ```

3. **Tester en ligne de commande** :
   ```bash
   php artisan tinker
   >>> App\Models\User::first()
   ```

## ✅ **Validation Finale**

Une fois le déploiement terminé, ces fonctionnalités doivent marcher :
- [ ] Inscription d'un nouveau parent
- [ ] Inscription d'un nouvel élève
- [ ] Connexion avec les comptes créés
- [ ] Navigation dans l'interface selon le rôle
- [ ] API endpoints répondent correctement

**Le projet CSNDR sera alors entièrement fonctionnel sur IONOS !**
