<?php /* public/views/homework_form.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($homework) ? 'Modifier' : 'Créer' ?> un devoir - CSNDR</title>
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
      $isEditMode = isset($homework);
      $pageTitle = $isEditMode ? 'Modifier le Devoir' : 'Créer un Devoir';
      ?>
      <header class="page-header">
        <div class="page-header-content">
          <div class="flex items-center gap-4">
            <a href="<?= $base ?>/homeworks" class="btn-secondary p-2 h-auto">
              <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
          </div>
        </div>
      </header>

      <div class="flex-1 p-4 sm:p-8 flex justify-center">
        <div class="card w-full max-w-4xl">
          <div class="card-header">
            <h3 class="text-xl font-bold text-primary">
              <?= $isEditMode ? 'Détails du devoir' : 'Nouveau devoir' ?>
            </h3>
          </div>
          <form method="post" class="p-6 space-y-6">
            <?php if (!empty($errors)) : ?>
              <div class="alert-danger">
                <ul class="list-disc pl-5">
                  <?php foreach ($errors as $e) : ?>
                    <li><?= htmlspecialchars($e) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="md:col-span-2">
                <label for="title" class="form-label">Titre du devoir</label>
                <div class="relative">
                  <i data-lucide="file-text" class="input-icon"></i>
                  <input id="title" class="input pl-10" name="title" value="<?= htmlspecialchars($old['title'] ?? ($homework['title'] ?? '')) ?>" required placeholder="Ex: Exercices de mathématiques">
                </div>
              </div>

              <div class="md:col-span-2">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" class="input" name="description" rows="4" placeholder="Décrivez les consignes et détails du devoir..."><?= htmlspecialchars($old['description'] ?? ($homework['description'] ?? '')) ?></textarea>
              </div>

              <div>
                <label for="due_at" class="form-label">Date d'échéance</label>
                <div class="relative">
                  <i data-lucide="calendar" class="input-icon"></i>
                  <input id="due_at" type="datetime-local" class="input pl-10" name="due_at" value="<?= htmlspecialchars($old['due_at'] ?? ($homework['due_at'] ?? '')) ?>" required>
                </div>
              </div>

              <div>
                <label for="classe_id" class="form-label">Classe</label>
                <div class="relative">
                  <i data-lucide="users" class="input-icon"></i>
                  <select id="classe_id" class="input pl-10" name="classe_id" required>
                    <option value="">-- Sélectionner une classe --</option>
                    <?php foreach (($classes ?? []) as $c) : ?>
                      <?php $sel = (int)($old['classe_id'] ?? ($homework['classe_id'] ?? 0)) === (int)$c['id']; ?>
                      <option value="<?= (int)$c['id'] ?>" <?= $sel ? 'selected' : '' ?>><?= htmlspecialchars($c['nom'] ?? $c['name'] ?? '') ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="flex justify-end gap-4 pt-4">
              <a class="btn-secondary" href="<?= $base ?>/homeworks">Annuler</a>
              <button class="btn-primary" type="submit">
                <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                Enregistrer
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>

  <script src="<?= $base ?>/assets/js/app.js"></script>
  <script>lucide.createIcons();</script>
</body>
</html>
