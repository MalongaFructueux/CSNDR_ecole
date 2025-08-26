<?php /* public/views/dashboard.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tableau de bord - CSNDR</title>
  <?php $twv = @filemtime(__DIR__ . '/../assets/css/tailwind.css') ?: time(); ?>
  <link rel="stylesheet" href="<?= $base ?>/assets/css/tailwind.css?v=<?= $twv ?>">
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="app-layout">
    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <?php 
      $pageTitle = 'Tableau de bord';
      include __DIR__ . '/layout/header.php'; 
      ?>

      <!-- Page Content -->
      <div class="flex-1 p-4 sm:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Welcome & Profile Card -->
          <div class="lg:col-span-2 card">
            <div class="card-header">
                <h2 class="text-xl font-bold text-primary">Bienvenue, <?= htmlspecialchars($user['prenom'] ?? '') ?> !</h2>
            </div>
            <p class="text-secondary mb-6">Voici un aperçu de votre journée.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="bg-[var(--gris-light)] p-4 rounded-xl flex items-center gap-4">
                <i data-lucide="user-circle" class="w-8 h-8 text-[var(--bleu-principal)]"></i>
                <div>
                  <p class="text-sm text-secondary">Nom</p>
                  <p class="font-semibold text-primary"><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></p>
                </div>
              </div>
              <div class="bg-[var(--gris-light)] p-4 rounded-xl flex items-center gap-4">
                <i data-lucide="shield-check" class="w-8 h-8 text-[var(--vert-accent)]"></i>
                <div>
                  <p class="text-sm text-secondary">Rôle</p>
                  <p class="font-semibold text-primary capitalize"><?= htmlspecialchars($user['role'] ?? '') ?></p>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Stats Card -->
          <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-bold text-primary">En bref</h2>
            </div>
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <i data-lucide="star" class="w-6 h-6 text-yellow-500"></i>
                    <p><?= count($notes ?? []) ?> nouvelles notes</p>
                </div>
                <div class="flex items-center gap-4">
                    <i data-lucide="book-open" class="w-6 h-6 text-green-500"></i>
                    <p><?= count($homeworks ?? []) ?> devoirs en cours</p>
                </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="<?= $base ?>/assets/js/app.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>
