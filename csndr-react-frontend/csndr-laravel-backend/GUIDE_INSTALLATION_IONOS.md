# Guide d'installation sur IONOS

## Résolution des problèmes de connexion

Ce guide vous aidera à résoudre les problèmes de connexion avec votre application Laravel hébergée sur IONOS.

### 1. Configuration du middleware Sanctum

Assurez-vous que le middleware `EnsureFrontendRequestsAreStateful` est activé dans le fichier `app/Http/Kernel.php` :

```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

### 2. Configuration du fichier .env

Copiez le fichier `.env.ionos` fourni et renommez-le en `.env` sur votre serveur IONOS. Assurez-vous de configurer correctement les variables suivantes :

```
APP_URL=https://csndr-gestion.com
SANCTUM_STATEFUL_DOMAINS=csndr-gestion.com
SESSION_DOMAIN=csndr-gestion.com
```

N'oubliez pas de générer une clé d'application si ce n'est pas déjà fait :

```
php artisan key:generate
```

### 3. Vérification de la configuration CORS

La configuration CORS dans `app/Http/Middleware/Cors.php` doit permettre les requêtes depuis votre domaine frontend :

```php
$response->headers->set('Access-Control-Allow-Origin', 'https://csndr-gestion.com');
```

### 4. Vérification de la table personal_access_tokens

Assurez-vous que la table `personal_access_tokens` existe dans votre base de données. Si ce n'est pas le cas, exécutez :

```
php artisan migrate
```

### 5. Vérification de la configuration frontend

Dans le fichier `src/services/api.js` du frontend, assurez-vous que l'URL de l'API est correctement configurée :

```javascript
baseURL: process.env.REACT_APP_API_URL || (
    process.env.NODE_ENV === 'production' 
        ? 'https://csndr-gestion.com/api'
        : 'http://localhost:8000/api'
),
```

### 6. Vérification des logs d'erreur

Consultez les logs d'erreur Laravel pour identifier d'éventuels problèmes :

```
tail -f storage/logs/laravel.log
```

### 7. Vérification des permissions de fichiers

Assurez-vous que les permissions des fichiers et dossiers sont correctement configurées :

```
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 8. Redémarrage du serveur

Après avoir effectué ces modifications, redémarrez votre serveur web si nécessaire.

## Problèmes courants

### Erreur CORS

Si vous rencontrez des erreurs CORS, vérifiez que :

1. Le middleware Cors est correctement configuré
2. Les en-têtes CORS sont correctement définis
3. Le domaine frontend est autorisé dans la configuration CORS

### Erreur d'authentification

Si vous rencontrez des erreurs d'authentification :

1. Vérifiez que le middleware Sanctum est activé
2. Vérifiez que les domaines stateful sont correctement configurés
3. Vérifiez que les cookies sont correctement transmis
4. Vérifiez que la table personal_access_tokens existe

### Erreur de base de données

Si vous rencontrez des erreurs de base de données :

1. Vérifiez les informations de connexion dans le fichier .env
2. Vérifiez que la base de données existe et est accessible
3. Vérifiez que les migrations ont été exécutées