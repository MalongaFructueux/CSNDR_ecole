<?php /* public/views/login.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion - CSNDR</title>
  <?php $twv = @filemtime(__DIR__ . '/../assets/css/tailwind.css') ?: time(); ?>
  <link rel="stylesheet" href="<?= $base ?? '' ?>/assets/css/tailwind.css?v=<?= $twv ?>">
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="flex items-center justify-center min-h-screen p-6">
  <div class="w-full max-w-md animate-fade-in">
    <div class="card-compact">
      <!-- Logo and Header -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-[var(--bleu-principal)] to-[var(--bleu-hover)] mb-4 shadow-lg">
          <img src="<?= $base ?? '' ?>/assets/logo.PNG" alt="CSNDR" class="w-10 h-10 object-contain filter brightness-0 invert">
        </div>
        <h1 class="text-3xl font-bold text-primary mb-2">Bienvenue</h1>
        <p class="text-secondary">Centre Scolaire Notre Dame du Rosaire</p>
        <div class="w-16 h-1 bg-gradient-to-r from-[var(--bleu-principal)] to-[var(--vert-accent)] rounded-full mx-auto mt-4"></div>
      </div>

      <!-- Error Messages -->
      <?php if (!empty($errors)): ?>
        <div class="alert-error animate-slide-up">
          <div class="flex items-center gap-2 mb-2">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <span class="font-semibold">Erreur de connexion</span>
          </div>
          <?php foreach ($errors as $e): ?>
            <div class="text-sm">• <?= htmlspecialchars($e) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form id="loginForm" method="post" action="<?= $base ?? '' ?>/login" class="space-y-6" novalidate>
        <div class="input-group">
          <i data-lucide="mail" class="input-icon"></i>
          <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                 placeholder="Adresse email" class="input" aria-label="Email" required>
        </div>
        
        <div class="input-group">
          <i data-lucide="lock" class="input-icon"></i>
          <input type="password" name="password" placeholder="Mot de passe" 
                 class="input" aria-label="Mot de passe" required minlength="6">
        </div>
        
        <button class="btn-primary w-full text-lg py-4" type="submit">
          <i data-lucide="log-in" class="mr-2 w-5 h-5"></i>
          Se connecter
        </button>
      </form>

      <!-- Footer -->
      <div class="text-center mt-8 pt-6 border-t border-[var(--gris-border)]">
        <p class="text-secondary text-sm">
          Pas de compte ? 
          <a href="<?= $base ?? '' ?>/register" class="text-[var(--bleu-principal)] font-semibold hover:text-[var(--bleu-hover)] transition-colors">
            Créer un compte
          </a>
        </p>
      </div>
    </div>
    
    <!-- School Info Footer -->
    <div class="text-center mt-6 text-xs text-[var(--gris-neutre)]">
      <p>Brazzaville, République du Congo</p>
      <p class="flex items-center justify-center gap-1 mt-1">
        <i data-lucide="graduation-cap" class="w-3 h-3"></i>
        Excellence • Discipline • Respect
      </p>
    </div>
  </div>
  
  <script>
    lucide.createIcons();
    
    // Add loading state to form submission
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      const btn = this.querySelector('button[type="submit"]');
      btn.classList.add('loading');
      btn.innerHTML = '<i data-lucide="loader-2" class="mr-2 w-5 h-5 animate-spin"></i>Connexion...';
    });
  </script>
  <script src="<?= $base ?? '' ?>/assets/js/auth.js"></script>
</body>
</html>
