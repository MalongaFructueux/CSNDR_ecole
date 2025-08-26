<?php /* public/views/event_form.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($event) ? 'Modifier' : 'Créer' ?> un événement - CSNDR</title>
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
      $isEditMode = isset($event);
      $pageTitle = $isEditMode ? 'Modifier l\'Événement' : 'Créer un Événement';
      ?>
      <header class="page-header">
        <div class="page-header-content">
          <div class="flex items-center gap-4">
            <a href="<?= $base ?>/events" class="btn-secondary p-2 h-auto">
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
              <?= $isEditMode ? 'Détails de l\'événement' : 'Nouvel événement' ?>
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
                <label for="title" class="form-label">Titre de l'événement</label>
                <div class="relative">
                  <i data-lucide="calendar-check" class="input-icon"></i>
                  <input id="title" class="input pl-10" name="title" value="<?= htmlspecialchars($old['title'] ?? ($event['title'] ?? '')) ?>" required placeholder="Ex: Réunion Parents-Professeurs" maxlength="120">
                </div>
                <div class="flex justify-end mt-1 text-xs text-gray-500"><span id="title-count">0/120</span></div>
              </div>

              <div class="md:col-span-2">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" class="input" name="description" rows="4" placeholder="Détails, lieu, informations complémentaires..." maxlength="500"><?= htmlspecialchars($old['description'] ?? ($event['description'] ?? '')) ?></textarea>
                <div class="flex items-center justify-between mt-1 text-xs text-gray-500">
                  <p>Précisez le lieu et les consignes importantes</p>
                  <span id="desc-count">0/500</span>
                </div>
              </div>

              <div>
                <label for="start_at" class="form-label">Date et heure de début</label>
                <div class="relative">
                  <i data-lucide="clock" class="input-icon"></i>
                  <input id="start_at" type="datetime-local" class="input pl-10" name="start_at" value="<?= htmlspecialchars($old['start_at'] ?? ($event['start_at'] ?? '')) ?>" required>
                </div>
              </div>

              <div>
                <label for="end_at" class="form-label">Date et heure de fin (optionnel)</label>
                <div class="relative">
                  <i data-lucide="clock" class="input-icon"></i>
                  <input id="end_at" type="datetime-local" class="input pl-10" name="end_at" value="<?= htmlspecialchars($old['end_at'] ?? ($event['end_at'] ?? '')) ?>">
                </div>
                <p class="text-xs text-gray-500 mt-1">Laissez vide si non applicable</p>
              </div>
            </div>

            <div class="flex justify-end gap-4 pt-4">
              <a class="btn-secondary" href="<?= $base ?>/events">Annuler</a>
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
  <script>
    lucide.createIcons();
    
    // Character counters
    const title = document.getElementById('title');
    const description = document.getElementById('description');
    const titleCount = document.getElementById('title-count');
    const descCount = document.getElementById('desc-count');
    
    function updateCount(input, counter) {
      if (!input || !counter) return;
      const max = parseInt(input.getAttribute('maxlength') || '0', 10);
      counter.textContent = (input.value || '').length + '/' + max;
    }
    
    if (title && titleCount) {
      updateCount(title, titleCount);
      title.addEventListener('input', () => updateCount(title, titleCount));
    }
    
    if (description && descCount) {
      updateCount(description, descCount);
      description.addEventListener('input', () => updateCount(description, descCount));
    }
    
    // Date validation
    const startAt = document.getElementById('start_at');
    const endAt = document.getElementById('end_at');
    
    function validateDates() {
      if (startAt && endAt && startAt.value && endAt.value && endAt.value < startAt.value) {
        endAt.setCustomValidity('La date de fin ne peut pas être avant le début.');
      } else {
        endAt?.setCustomValidity('');
      }
    }
    
    startAt?.addEventListener('change', () => {
      if (endAt && startAt.value) endAt.min = startAt.value;
      validateDates();
    });
    endAt?.addEventListener('change', validateDates);
    validateDates();
  </script>
</body>
</html>
