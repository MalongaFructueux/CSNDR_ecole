<?php /* public/views/events.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Événements - CSNDR</title>
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
      $pageTitle = 'Gestion des Événements';
      ?>
      <header class="page-header">
        <div class="page-header-content">
          <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
          <div class="flex items-center gap-4">
            <?php if (in_array(($user['role'] ?? ''), ['professeur', 'admin'])) : ?>
              <a href="<?= $base ?>/events/create" class="btn-primary">
                <i data-lucide="plus" class="mr-2 w-4 h-4"></i>Ajouter un événement
              </a>
            <?php endif; ?>
          </div>
        </div>
      </header>

      <div class="flex-1 p-4 sm:p-8">
        <div class="card">
          <div class="card-header">
            <h3 class="text-xl font-bold text-primary">Liste des événements</h3>
            <span class="badge-neutral"><?= count($events ?? []) ?> événement(s)</span>
          </div>
          <div class="overflow-x-auto">
            <table class="table">
              <thead>
                <tr>
                  <th>Titre</th>
                  <th>Date de début</th>
                  <th>Date de fin</th>
                  <th>Organisateur</th>
                  <?php if (in_array(($user['role'] ?? ''), ['professeur', 'admin'])) : ?>
                    <th class="text-center">Actions</th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($events)) : ?>
                  <tr>
                    <td colspan="5" class="text-center py-12">
                      <div class="flex flex-col items-center gap-4 text-secondary">
                        <i data-lucide="calendar-x" class="w-12 h-12 text-[var(--gris-neutre)]/50"></i>
                        <p class="text-lg font-medium">Aucun événement planifié</p>
                        <p class="text-sm">Les événements créés apparaîtront ici.</p>
                      </div>
                    </td>
                  </tr>
                <?php else : ?>
                  <?php foreach (($events ?? []) as $e) : ?>
                    <tr>
                      <td class="font-medium"><?= htmlspecialchars($e['title'] ?? '') ?></td>
                      <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($e['start_at'] ?? ''))) ?></td>
                      <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($e['end_at'] ?? ''))) ?></td>
                      <td><?= htmlspecialchars(($e['creator_prenom'] ?? '') . ' ' . ($e['creator_nom'] ?? '')) ?></td>
                      <?php if (in_array(($user['role'] ?? ''), ['professeur', 'admin'])) : ?>
                        <td>
                          <div class="flex items-center justify-center gap-2">
                            <a class="btn-secondary p-2 h-auto" href="<?= $base ?>/events/edit/<?= (int)($e['id'] ?? 0) ?>">
                              <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                            <a class="btn-danger p-2 h-auto" data-confirm="Supprimer cet événement ?" href="<?= $base ?>/events/delete/<?= (int)($e['id'] ?? 0) ?>">
                              <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </a>
                          </div>
                        </td>
                      <?php endif; ?>
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
    <?php if (!empty($_SESSION['flash'])) : $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
      try {
        CSNDRToast('<?= htmlspecialchars($f['message'] ?? '') ?>', '<?= htmlspecialchars($f['type'] ?? 'success') ?>');
      } catch (e) {}
    <?php endif; ?>
  </script>
</body>
</html>
