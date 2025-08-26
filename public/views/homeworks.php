<?php /* public/views/homeworks.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Devoirs - CSNDR</title>
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
      $pageTitle = 'Gestion des Devoirs';
      ?>
      <header class="page-header">
        <div class="page-header-content">
          <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
          <div class="flex items-center gap-4">
            <?php if (in_array(($user['role'] ?? ''), ['professeur', 'admin'])) : ?>
              <a href="<?= $base ?>/homeworks/create" class="btn-primary">
                <i data-lucide="plus" class="mr-2 w-4 h-4"></i>Ajouter un devoir
              </a>
            <?php endif; ?>
          </div>
        </div>
      </header>

      <div class="flex-1 p-4 sm:p-8">
        <div class="card">
          <div class="card-header">
            <h3 class="text-xl font-bold text-primary">Liste des devoirs</h3>
            <span class="badge-neutral"><?= count($homeworks ?? []) ?> devoir(s)</span>
          </div>
          <div class="overflow-x-auto">
            <table class="table">
              <thead>
                <tr>
                  <th>Titre</th>
                  <th>Classe</th>
                  <th>Date d'échéance</th>
                  <th>Auteur</th>
                  <?php if (in_array(($user['role'] ?? ''), ['professeur', 'admin'])) : ?>
                    <th class="text-center">Actions</th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($homeworks)) : ?>
                  <tr>
                    <td colspan="5" class="text-center py-12">
                      <div class="flex flex-col items-center gap-4 text-secondary">
                        <i data-lucide="clipboard-list" class="w-12 h-12 text-[var(--gris-neutre)]/50"></i>
                        <p class="text-lg font-medium">Aucun devoir à afficher</p>
                        <p class="text-sm">Les devoirs créés apparaîtront ici.</p>
                      </div>
                    </td>
                  </tr>
                <?php else : ?>
                  <?php foreach (($homeworks ?? []) as $h) : ?>
                    <tr>
                      <td class="font-medium"><?= htmlspecialchars($h['title'] ?? '') ?></td>
                      <td><span class="badge-primary"><?= htmlspecialchars($h['class_name'] ?? 'N/A') ?></span></td>
                      <td><?= htmlspecialchars(date('d/m/Y', strtotime($h['due_at'] ?? ''))) ?></td>
                      <td><?= htmlspecialchars(($h['creator_prenom'] ?? '') . ' ' . ($h['creator_nom'] ?? '')) ?></td>
                      <?php if (in_array(($user['role'] ?? ''), ['professeur', 'admin'])) : ?>
                        <td>
                          <div class="flex items-center justify-center gap-2">
                            <a class="btn-secondary p-2 h-auto" href="<?= $base ?>/homeworks/edit/<?= (int)($h['id'] ?? 0) ?>">
                              <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                            <a class="btn-danger p-2 h-auto" data-confirm="Supprimer ce devoir ?" href="<?= $base ?>/homeworks/delete/<?= (int)($h['id'] ?? 0) ?>">
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
    <?php if (!empty($_SESSION['flash'])) : $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
      try {
        CSNDRToast('<?= htmlspecialchars($f['message'] ?? '') ?>', '<?= htmlspecialchars($f['type'] ?? 'success') ?>');
      } catch (e) {}
    <?php endif; ?>
  </script>
</body>
</html>
