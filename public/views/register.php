<?php /* public/views/register.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inscription - CSNDR</title>
  <?php $twv = @filemtime(__DIR__ . '/../assets/css/tailwind.css') ?: time(); ?>
  <link rel="stylesheet" href="<?= $base ?>/assets/css/tailwind.css?v=<?= $twv ?>">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="p-4 md:p-8">
  <header class="sticky top-0 z-40 mb-6 text-white shadow-lg bg-gradient-to-r from-blue-600 to-indigo-600">
    <div class="container mx-auto p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-lg font-bold">CSNDR</div>
        <div>
          <h1 class="text-xl sm:text-2xl font-bold">Centre Scolaire Notre Dame du Rosaire</h1>
          <p class="text-white/80 text-sm">Brazzaville, Congo</p>
          <p class="text-white/80 text-xs flex items-center gap-1 mt-1"><i data-lucide="user-plus" class="w-4 h-4"></i>Inscription</p>
        </div>
      </div>
      <div class="flex gap-3">
        <a href="<?= $base ?>/login" class="btn-secondary bg-white/10 border-white/30 text-white hover:bg-white/20">Se connecter</a>
      </div>
    </div>
  </header>

  <div class="w-full max-w-2xl card mx-auto" data-animate="slide">
    <div class="flex items-center gap-3 mb-6">
      <div class="w-12 h-12 rounded-full bg-blue-600/10 text-blue-700 flex items-center justify-center font-bold">CS</div>
      <h1 class="text-2xl font-semibold text-[var(--text-title)]">Créer un compte</h1>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm">
        <?php foreach ($errors as $e): ?>
          <div>• <?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form id="registerForm" method="post" action="<?= $base ?>/register" class="grid grid-cols-1 md:grid-cols-2 gap-4" novalidate>
      <div>
        <label class="block mb-1">Nom</label>
        <input class="input" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" required>
      </div>
      <div>
        <label class="block mb-1">Prénom</label>
        <input class="input" name="prenom" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" required>
      </div>
      <div class="md:col-span-2">
        <label class="block mb-1">Email</label>
        <input type="email" class="input" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
      </div>
      <div class="md:col-span-2">
        <label class="block mb-1">Mot de passe</label>
        <input type="password" class="input" name="password" minlength="6" required>
      </div>
      <div>
        <label class="block mb-1">Rôle</label>
        <select class="input" name="role">
          <?php $role = $old['role'] ?? 'eleve'; ?>
          <option value="eleve" <?= $role==='eleve'?'selected':''; ?>>Élève</option>
          <option value="professeur" <?= $role==='professeur'?'selected':''; ?>>Professeur</option>
          <option value="parent" <?= $role==='parent'?'selected':''; ?>>Parent</option>
          <option value="admin" <?= $role==='admin'?'selected':''; ?>>Admin</option>
        </select>
      </div>
      <div>
        <label class="block mb-1">Classe (optionnel)</label>
        <select class="input" name="classe_id">
          <option value="">--</option>
          <?php foreach (($classes ?? []) as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= (($old['classe_id'] ?? '')==$c['id'])?'selected':''; ?>><?= htmlspecialchars($c['nom']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="block mb-1">Parent (optionnel)</label>
        <select class="input" name="parent_id">
          <option value="">--</option>
          <?php foreach (($parents ?? []) as $p): ?>
            <option value="<?= (int)$p['id'] ?>" <?= (($old['parent_id'] ?? '')==$p['id'])?'selected':''; ?>><?= htmlspecialchars($p['nom'].' '.$p['prenom'].' ('.$p['email'].')') ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-2 flex gap-3 pt-2">
        <button class="btn-primary" type="submit">Créer</button>
        <a class="btn-secondary" href="<?= $base ?>/login">Annuler</a>
      </div>
    </form>
  </div>
  <script src="<?= $base ?>/assets/js/app.js"></script>
  <script>
    lucide.createIcons();
    <?php if (!empty($errors)): foreach ($errors as $e): ?>
      try{ CSNDRToast('<?= htmlspecialchars($e) ?>','error'); }catch(e){}
    <?php endforeach; endif; ?>
  </script>
</body>
</html>
