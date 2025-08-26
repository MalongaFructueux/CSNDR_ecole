<?php /* public/views/classes.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Classes - CSNDR</title>
  <?php $twv = @filemtime(__DIR__ . '/../assets/css/tailwind.css') ?: time(); ?>
  <link rel="stylesheet" href="<?= $base ?? '' ?>/assets/css/tailwind.css?v=<?= $twv ?>">
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="app-layout">
    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <main class="main-content">
      <?php 
      $pageTitle = 'Gestion des Classes';
      ?>
      <header class="page-header">
        <div class="page-header-content">
          <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
          <div class="flex items-center gap-4">
            <?php if (($user['role'] ?? '') === 'admin'): ?>
              <a href="<?= $base ?>/classes/create" class="btn-primary">
                <i data-lucide="plus" class="mr-2 w-4 h-4"></i>Ajouter une classe
              </a>
            <?php endif; ?>
          </div>
        </div>
      </header>

      <div class="flex-1 p-4 sm:p-8">
        <div class="card">
          <div class="card-header">
            <h3 class="text-xl font-bold text-primary">Liste des classes</h3>
            <span class="badge-neutral"><?= count($classes ?? []) ?> classe(s)</span>
          </div>
          <div class="overflow-x-auto">
            <table class="table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nom de la classe</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($classes)): ?>
                  <tr>
                    <td colspan="3" class="text-center py-12">
                      <div class="flex flex-col items-center gap-4 text-secondary">
                        <i data-lucide="inbox" class="w-12 h-12 text-[var(--gris-neutre)]/50"></i>
                        <p class="text-lg font-medium">Aucune classe disponible</p>
                        <p class="text-sm">Les classes apparaîtront ici une fois ajoutées.</p>
                      </div>
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach (($classes ?? []) as $c): ?>
                    <tr>
                      <td><span class="font-mono text-sm">#<?= (int)$c['id'] ?></span></td>
                      <td class="font-medium"><?= htmlspecialchars($c['nom']) ?></td>
                      <td>
                        <div class="flex items-center justify-center gap-2">
                          <a class="btn-secondary p-2 h-auto" href="<?= $base ?>/classes/edit/<?= (int)$c['id'] ?>">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                          </a>
                          <a class="btn-danger p-2 h-auto" data-confirm="Supprimer cette classe ?" href="<?= $base ?>/classes/delete/<?= (int)$c['id'] ?>">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="<?= $base ?>/assets/js/app.js"></script>
  <script>
    lucide.createIcons();
    document.addEventListener('click', (e) => {
      const a = e.target.closest('[data-confirm]');
      if (a) {
        const message = a.getAttribute('data-confirm');
        if (!confirm(`⚠️ ${message}\n\nCette action est irréversible.`)) {
          e.preventDefault();
        }
      }
    });
  </script>
</body>
</html>
