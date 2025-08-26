<?php /* public/views/users.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Utilisateurs (Admin) - CSNDR</title>
  <link rel="stylesheet" href="/CSNDR/public/assets/css/tailwind.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="p-4 md:p-8">
  <header class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
      <img src="/CSNDR/public/assets/logo.svg" alt="CSNDR" class="w-10 h-10 rounded-full">
      <h1 class="text-xl font-semibold text-[var(--text-title)]">Utilisateurs</h1>
    </div>
    <div class="flex gap-3">
      <a href="/CSNDR/public/dashboard" class="btn-secondary">Dashboard</a>
      <a href="/CSNDR/public/admin/users/create" class="btn-primary"><i data-lucide="user-plus" class="mr-2"></i>Ajouter</a>
      <a href="/CSNDR/public/logout" class="btn-secondary"><i data-lucide="log-out" class="mr-2"></i>Déconnexion</a>
    </div>
  </header>

  <main class="card">
    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Classe</th>
            <th>Parent</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($users ?? []) as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['nom'] ?? '') ?></td>
              <td><?= htmlspecialchars($u['prenom'] ?? '') ?></td>
              <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
              <td><span class="badge"><?= htmlspecialchars($u['role'] ?? '') ?></span></td>
              <td><?= htmlspecialchars((string)($u['classe_id'] ?? '')) ?></td>
              <td><?= htmlspecialchars((string)($u['parent_id'] ?? '')) ?></td>
              <td class="flex gap-2">
                <a class="btn-secondary" href="/CSNDR/public/admin/users/edit/<?= (int)$u['id'] ?>"><i data-lucide="edit" class="mr-1"></i>Éditer</a>
                <a class="btn-secondary" data-confirm="Supprimer cet utilisateur ?" href="/CSNDR/public/admin/users/delete/<?= (int)$u['id'] ?>"><i data-lucide="trash" class="mr-1"></i>Supprimer</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <script>lucide.createIcons();</script>
  <script src="/CSNDR/public/assets/js/class.js"></script>
</body>
</html>
