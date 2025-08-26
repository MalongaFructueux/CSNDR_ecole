<?php /* public/views/messages.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Messagerie - CSNDR</title>
  <?php $twv = @filemtime(__DIR__ . '/../assets/css/tailwind.css') ?: time(); ?>
  <link rel="stylesheet" href="<?= $base ?>/assets/css/tailwind.css?v=<?= $twv ?>">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="p-4 md:p-8">
  <header class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
      <img src="<?= $base ?>/assets/logo.svg" alt="CSNDR" class="w-10 h-10 rounded-full">
      <div>
        <h1 class="text-xl font-semibold text-[var(--text-title)] flex items-center gap-2">
          <i data-lucide="messages-square"></i>
          Messagerie
        </h1>
        <p class="text-sm text-gray-500">Échangez avec l’équipe pédagogique et les parents.</p>
      </div>
    </div>
    <div class="flex gap-3">
      <a href="<?= $base ?>/dashboard" class="btn-secondary"><i data-lucide="layout-dashboard" class="mr-2"></i>Dashboard</a>
      <a href="<?= $base ?>/logout" class="btn-secondary"><i data-lucide="log-out" class="mr-2"></i>Déconnexion</a>
    </div>
  </header>

  <main class="grid gap-6 md:grid-cols-3">
    <!-- Sidebar: New message + conversations -->
    <aside class="card md:col-span-1 space-y-4">
      <?php if (!empty($errors)): ?>
        <div class="p-3 rounded bg-red-50 text-red-700 text-sm">
          <?php foreach ($errors as $e): ?>
            <div>• <?= htmlspecialchars($e) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div>
        <h2 class="text-lg font-semibold text-[var(--text-title)] mb-3 flex items-center gap-2">
          <i data-lucide="square-pen"></i> Nouveau message
        </h2>
        <?php $canSend = in_array(($role ?? ''), ['admin','professeur','parent'], true); ?>
        <form action="<?= $base ?>/messages/create" method="post" class="space-y-3">
          <div>
            <label class="block mb-1">Destinataire</label>
            <div class="relative">
              <i data-lucide="users" class="input-icon"></i>
              <select class="input pl-9" name="to_id" <?= $canSend ? '' : 'disabled' ?> required>
                <option value="">-- Sélectionner --</option>
                <?php foreach (($users ?? []) as $u): ?>
                  <option value="<?= (int)$u['id'] ?>"><?= htmlspecialchars($u['nom'].' '.$u['prenom'].' ('.$u['email'].')') ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div>
            <label class="block mb-1">Message</label>
            <textarea class="input" name="content" rows="3" placeholder="Votre message…" <?= $canSend ? '' : 'disabled' ?> required></textarea>
          </div>
          <button class="btn-primary w-full" type="submit" <?= $canSend ? '' : 'disabled' ?>><i data-lucide="send" class="mr-2"></i>Envoyer</button>
          <?php if (!$canSend): ?>
            <p class="text-xs text-gray-500">Votre rôle ne permet pas d’envoyer des messages.</p>
          <?php endif; ?>
        </form>
      </div>

      <div>
        <h2 class="text-lg font-semibold text-[var(--text-title)] mb-3">Conversations</h2>
        <div class="relative mb-3">
          <i data-lucide="search" class="input-icon"></i>
          <input class="input pl-9" placeholder="Rechercher…" oninput="filterConversations(this.value)">
        </div>
        <ul id="convList" class="space-y-2 max-h-[50vh] overflow-auto pr-1">
          <?php foreach (($inbox ?? []) as $m): ?>
            <?php $other = ($m['from_id'] == ($_SESSION['user_id'] ?? 0)) ? ($m['to_id'] ?? 0) : ($m['from_id'] ?? 0); ?>
            <li data-search="<?= htmlspecialchars(strtolower(($m['from_nom'] ?? '').' '.($m['from_prenom'] ?? '').' '.($m['content'] ?? ''))) ?>">
              <a class="btn-secondary w-full justify-start" href="<?= $base ?>/messages/thread/<?= (int)$other ?>">
                <div class="text-left">
                  <div class="text-sm font-medium text-[var(--text-title)]">Conversation #<?= (int)$other ?></div>
                  <div class="text-xs text-gray-500"><?= htmlspecialchars(mb_strimwidth($m['content'] ?? '', 0, 60, '…')) ?></div>
                </div>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </aside>

    <!-- Thread area -->
    <section class="md:col-span-2 card">
      <h2 class="text-lg font-semibold text-[var(--text-title)] mb-3">Fil de discussion</h2>
      <?php if (empty($thread)): ?>
        <div class="text-gray-500 text-sm">Sélectionnez une conversation pour afficher les messages.</div>
      <?php else: ?>
        <div class="space-y-3" id="thread">
          <?php foreach ($thread as $msg): ?>
            <?php $mine = ($msg['from_id'] == ($_SESSION['user_id'] ?? 0)); ?>
            <div class="flex <?= $mine ? 'justify-end' : 'justify-start' ?>">
              <div class="max-w-[80%] p-3 rounded-2xl border <?= $mine ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' ?>">
                <div class="text-[11px] text-gray-500 mb-1 flex items-center gap-2">
                  <i data-lucide="user-2" class="w-3 h-3"></i>
                  <?= htmlspecialchars(($msg['from_prenom'] ?? '').' '.($msg['from_nom'] ?? '')) ?> — <?= htmlspecialchars($msg['created_at'] ?? '') ?>
                </div>
                <div class="whitespace-pre-wrap leading-relaxed"><?= nl2br(htmlspecialchars($msg['content'] ?? '')) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <script>
    function filterConversations(q) {
      q = (q || '').toLowerCase();
      const items = document.querySelectorAll('#convList > li');
      items.forEach(li => {
        const v = li.getAttribute('data-search') || '';
        li.style.display = v.includes(q) ? '' : 'none';
      });
    }
    lucide.createIcons();
  </script>
</body>
</html>
