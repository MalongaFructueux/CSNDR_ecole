<?php /* public/views/user_form.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($user)?'Modifier':'Créer' ?> un utilisateur - CSNDR</title>
  <link rel="stylesheet" href="/CSNDR/public/assets/css/tailwind.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="p-4 md:p-8">
  <header class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
      <img src="/CSNDR/public/assets/logo.svg" alt="CSNDR" class="w-10 h-10 rounded-full">
      <h1 class="text-xl font-semibold text-[var(--text-title)]"><?= isset($user)?'Modifier':'Créer' ?> un utilisateur</h1>
    </div>
    <div class="flex gap-3">
      <a href="/CSNDR/public/admin/users" class="btn-secondary">Retour</a>
      <a href="/CSNDR/public/logout" class="btn-secondary"><i data-lucide="log-out" class="mr-2"></i>Déconnexion</a>
    </div>
  </header>

  <main class="card max-w-3xl">
    <?php if (!empty($errors)): ?>
      <div class="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm">
        <?php foreach ($errors as $e): ?>
          <div>• <?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-1">Nom</label>
        <input class="input" name="nom" value="<?= htmlspecialchars($old['nom'] ?? ($user['nom'] ?? '')) ?>" required>
      </div>
      <div>
        <label class="block mb-1">Prénom</label>
        <input class="input" name="prenom" value="<?= htmlspecialchars($old['prenom'] ?? ($user['prenom'] ?? '')) ?>" required>
      </div>
      <div class="md:col-span-2">
        <label class="block mb-1">Email</label>
        <input type="email" class="input" name="email" value="<?= htmlspecialchars($old['email'] ?? ($user['email'] ?? '')) ?>" required>
      </div>
      <div>
        <label class="block mb-1">Mot de passe <?= isset($user)?'<span class="text-xs text-gray-500">(laisser vide pour inchangé)</span>':'' ?></label>
        <input type="password" class="input" name="password" <?= isset($user)?'':'required' ?>>
      </div>
      <div>
        <label class="block mb-1">Rôle</label>
        <?php $roleVal = $old['role'] ?? ($user['role'] ?? 'student'); ?>
        <select class="input" name="role" required>
          <option value="student" <?= $roleVal==='student'?'selected':'' ?>>Élève</option>
          <option value="teacher" <?= $roleVal==='teacher'?'selected':'' ?>>Professeur</option>
          <option value="parent" <?= $roleVal==='parent'?'selected':'' ?>>Parent</option>
          <option value="admin" <?= $roleVal==='admin'?'selected':'' ?>>Admin</option>
        </select>
      </div>
      <div>
        <label class="block mb-1">Classe (ID)</label>
        <select class="input" name="classe_id">
          <option value="">-- Aucune --</option>
          <?php foreach (($classes ?? []) as $c): ?>
            <?php $sel = (int)($old['classe_id'] ?? ($user['classe_id'] ?? 0)) === (int)$c['id']; ?>
            <option value="<?= (int)$c['id'] ?>" <?= $sel?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block mb-1">Parent (ID)</label>
        <select class="input" name="parent_id">
          <option value="">-- Aucun --</option>
          <?php foreach (($users ?? []) as $p): ?>
            <?php if (($p['role'] ?? '') !== 'parent') continue; ?>
            <?php $sel = (int)($old['parent_id'] ?? ($user['parent_id'] ?? 0)) === (int)$p['id']; ?>
            <option value="<?= (int)$p['id'] ?>" <?= $sel?'selected':'' ?>><?= htmlspecialchars($p['nom'].' '.$p['prenom']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-2 flex gap-3 pt-2">
        <button class="btn-primary" type="submit">Enregistrer</button>
        <a class="btn-secondary" href="/CSNDR/public/admin/users">Annuler</a>
      </div>
    </form>
  </main>

  <script>lucide.createIcons();</script>
</body>
</html>
