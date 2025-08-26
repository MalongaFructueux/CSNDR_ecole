<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($class) ? 'Modifier' : 'Ajouter' ?> une classe - CSNDR</title>
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
      $isEditMode = isset($class);
      $pageTitle = $isEditMode ? 'Modifier la Classe' : 'Ajouter une Classe';
      ?>
      <header class="page-header">
        <div class="page-header-content">
          <div class="flex items-center gap-4">
            <a href="<?= $base ?>/classes" class="btn-secondary p-2 h-auto">
              <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
          </div>
        </div>
      </header>

      <div class="flex-1 p-4 sm:p-8 flex justify-center">
        <div class="card w-full max-w-2xl">
          <div class="card-header">
            <h3 class="text-xl font-bold text-primary">
              <?= $isEditMode ? 'Détails de la classe' : 'Nouvelle classe' ?>
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

            <div>
              <label for="nom" class="form-label">Nom de la classe</label>
              <div class="relative">
                <i data-lucide="book-marked" class="input-icon"></i>
                <input id="nom" class="input pl-10" name="nom" value="<?= htmlspecialchars($old['nom'] ?? ($class['nom'] ?? '')) ?>" required placeholder="Ex: Mathématiques 101">
              </div>
            </div>

            <div class="flex justify-end gap-4 pt-4">
              <a class="btn-secondary" href="<?= $base ?>/classes">Annuler</a>
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
