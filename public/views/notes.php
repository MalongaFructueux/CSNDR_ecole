<?php /* public/views/notes.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notes - CSNDR</title>
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

    <main class="main-content">
      <?php 
      $pageTitle = 'Gestion des Notes';
      // Custom header for notes page with action button
      ?>
      <header class="page-header">
        <div class="page-header-content">
          <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
          <div class="flex items-center gap-4">
            <?php if (($role ?? '') === 'professeur' || ($role ?? '') === 'admin'): ?>
              <a href="<?= $base ?>/notes/create" class="btn-primary">
                <i data-lucide="plus" class="mr-2 w-4 h-4"></i>Ajouter une note
              </a>
            <?php endif; ?>
          </div>
        </div>
      </header>

      <div class="flex-1 p-4 sm:p-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-gradient-to-r from-[var(--bleu-principal)] to-[var(--bleu-hover)] rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-white/80 text-sm">Total des notes</p>
                <p class="text-3xl font-bold"><?= count($notes ?? []) ?></p>
              </div>
              <i data-lucide="book-open" class="w-10 h-10 text-white/60"></i>
            </div>
          </div>
          <div class="bg-gradient-to-r from-[var(--vert-accent)] to-[var(--vert-hover)] rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-white/80 text-sm">Moyenne générale</p>
                <p class="text-3xl font-bold">
                  <?php 
                  $total = 0; $count = 0;
                  foreach (($notes ?? []) as $n) {
                    if (is_numeric($n['note'] ?? '')) {
                      $total += (float)$n['note'];
                      $count++;
                    }
                  }
                  echo $count > 0 ? number_format($total / $count, 1) : '--';
                  ?>
                </p>
              </div>
              <i data-lucide="trending-up" class="w-10 h-10 text-white/60"></i>
            </div>
          </div>
          <div class="bg-gradient-to-r from-[var(--gris-neutre)] to-gray-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-white/80 text-sm">Matières</p>
                <p class="text-3xl font-bold">
                  <?= count(array_unique(array_column($notes ?? [], 'matiere'))) ?>
                </p>
              </div>
              <i data-lucide="layers" class="w-10 h-10 text-white/60"></i>
            </div>
          </div>
        </div>

        <!-- Notes Table -->
        <div class="card">
          <div class="card-header">
            <h3 class="text-xl font-bold text-primary">Liste des notes</h3>
            <span class="badge-neutral"><?= count($notes ?? []) ?> résultat(s)</span>
          </div>
          <div class="overflow-x-auto">
            <table class="table">
              <thead>
                <tr>
                  <?php if (($role ?? '') !== 'eleve'): ?><th>Élève</th><?php endif; ?>
                  <th>Matière</th>
                  <th class="text-center">Note</th>
                  <th>Type</th>
                  <?php if (($role ?? '') !== 'eleve' && ($role ?? '') !== 'parent'): ?>
                    <th class="text-center">Actions</th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($notes)): ?>
                  <tr>
                    <td colspan="<?= (($role ?? '') === 'eleve') ? '3' : (($role ?? '') === 'parent' ? '4' : '5') ?>" class="text-center py-12">
                      <div class="flex flex-col items-center gap-4 text-secondary">
                        <i data-lucide="inbox" class="w-12 h-12 text-[var(--gris-neutre)]/50"></i>
                        <p class="text-lg font-medium">Aucune note disponible</p>
                        <p class="text-sm">Les notes apparaîtront ici une fois ajoutées.</p>
                      </div>
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($notes as $n): ?>
                    <tr>
                      <?php if (($role ?? '') !== 'eleve'): ?>
                        <td>
                          <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[var(--bleu-light)] flex items-center justify-center text-[var(--bleu-principal)] text-sm font-semibold">
                              <?= strtoupper(substr($n['eleve_prenom'] ?? 'X', 0, 1)) ?>
                            </div>
                            <span class="font-medium"><?= htmlspecialchars(($n['eleve_prenom'] ?? '') . ' ' . ($n['eleve_nom'] ?? '')) ?></span>
                          </div>
                        </td>
                      <?php endif; ?>
                      <td><span class="badge-primary"><?= htmlspecialchars($n['matiere'] ?? '') ?></span></td>
                      <td class="text-center">
                        <?php 
                        $note = (float)($n['note'] ?? 0);
                        $color = $note >= 15 ? 'text-[var(--vert-accent)]' : ($note >= 10 ? 'text-[var(--bleu-principal)]' : 'text-[var(--rouge-erreur)]');
                        ?>
                        <span class="text-xl font-bold <?= $color ?>"><?= htmlspecialchars((string)($n['note'] ?? '')) ?></span>
                        <span class="text-sm text-secondary">/20</span>
                      </td>
                      <td><span class="badge-neutral"><?= htmlspecialchars($n['type'] ?? '') ?></span></td>
                      <?php if (($role ?? '') === 'professeur' || ($role ?? '') === 'admin'): ?>
                        <td>
                          <div class="flex items-center justify-center gap-2">
                            <a class="btn-secondary p-2 h-auto" href="<?= $base ?>/notes/edit/<?= (int)($n['id'] ?? 0) ?>">
                              <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                            <a class="btn-danger p-2 h-auto" data-confirm="Supprimer cette note ?" href="<?= $base ?>/notes/delete/<?= (int)($n['id'] ?? 0) ?>">
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
    <?php if (!empty($_SESSION['flash'])): $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
      try{ 
        CSNDRToast('<?= htmlspecialchars($f['message'] ?? '') ?>','<?= htmlspecialchars($f['type'] ?? 'success') ?>'); 
      }catch(e){
        console.log('Toast notification:', '<?= htmlspecialchars($f['message'] ?? '') ?>');
      }
    <?php endif; ?>
  </script>
</body>
</html>
