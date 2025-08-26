<?php /* public/views/note_form.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($note)?'Modifier':'Ajouter' ?> une note - CSNDR</title>
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
          <p class="text-white/80 text-xs flex items-center gap-1 mt-1"><i data-lucide="star" class="w-4 h-4"></i><?= isset($note)?'Modifier':'Ajouter' ?> une note</p>
        </div>
      </div>
      <div class="flex gap-3">
        <a href="<?= $base ?>/notes" class="btn-secondary bg-white/10 border-white/30 text-white hover:bg-white/20">Retour</a>
        <a href="<?= $base ?>/logout" class="btn-secondary bg-white/10 border-white/30 text-white hover:bg-white/20"><i data-lucide="log-out" class="mr-2"></i>Déconnexion</a>
      </div>
    </div>
  </header>

  <main class="card max-w-2xl" data-animate="slide">
    <?php if (!empty($errors)): ?>
      <div class="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm">
        <?php foreach ($errors as $e): ?>
          <div>• <?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="md:col-span-2">
        <label class="block mb-1">Étudiant</label>
        <select class="input" name="eleve_id" required>
          <option value="">-- Sélectionner --</option>
          <?php foreach (($students ?? []) as $s): ?>
            <?php $selected = (string)($old['eleve_id'] ?? ($note['eleve_id'] ?? '')) === (string)$s['id'] ? 'selected' : ''; ?>
            <option value="<?= (int)$s['id'] ?>" <?= $selected ?>><?= htmlspecialchars($s['nom'].' '.$s['prenom'].' ('.$s['email'].')') ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block mb-1">Matière</label>
        <div class="relative">
          <i data-lucide="book" class="input-icon"></i>
          <input class="input pl-10" name="matiere" value="<?= htmlspecialchars($old['matiere'] ?? ($note['matiere'] ?? '')) ?>" maxlength="60" required>
        </div>
        <div class="flex justify-end mt-1 text-xs text-gray-500"><span id="matiere-count">0/60</span></div>
      </div>
      <div>
        <label class="block mb-1">Note (0-20)</label>
        <div class="relative">
          <i data-lucide="star" class="input-icon"></i>
          <input type="number" step="0.01" min="0" max="20" class="input pl-10" name="note" value="<?= htmlspecialchars((string)($old['note'] ?? ($note['note'] ?? ''))) ?>" required>
        </div>
      </div>
      <div class="md:col-span-2">
        <label class="block mb-1">Type</label>
        <?php $type = $old['type'] ?? ($note['type'] ?? 'Devoir'); ?>
        <select class="input" name="type">
          <option value="Devoir" <?= $type==='Devoir'?'selected':''; ?>>Devoir</option>
          <option value="Examen" <?= $type==='Examen'?'selected':''; ?>>Examen</option>
          <option value="Participation" <?= $type==='Participation'?'selected':''; ?>>Participation</option>
        </select>
      </div>
      <div class="md:col-span-2 flex gap-3 pt-2">
        <button class="btn-primary" type="submit">Enregistrer</button>
        <a class="btn-secondary" href="<?= $base ?>/notes">Annuler</a>
      </div>
    </form>
  </main>

  <script src="<?= $base ?>/assets/js/app.js"></script>
  <script>
    lucide.createIcons();
    <?php if (!empty($errors)): foreach ($errors as $e): ?>
      try{ CSNDRToast('<?= htmlspecialchars($e) ?>','error'); }catch(e){}
    <?php endforeach; endif; ?>
    const form = document.querySelector('form');
    const matiere = form.querySelector('input[name="matiere"]');
    const note = form.querySelector('input[name="note"]');
    const type = form.querySelector('select[name="type"]');
    const eleve = form.querySelector('select[name="eleve_id"]');
    const count = document.getElementById('matiere-count');

    function updateCount(){
      if (!matiere || !count) return;
      const max = parseInt(matiere.getAttribute('maxlength')||'0',10);
      count.textContent = (matiere.value||'').length + '/' + max;
    }
    updateCount();
    matiere?.addEventListener('input', updateCount);

    function validateNote(){
      if (!note) return;
      const v = parseFloat(note.value);
      if (Number.isFinite(v) && v >= 0 && v <= 20){
        note.setCustomValidity('');
      } else {
        note.setCustomValidity('La note doit être comprise entre 0 et 20.');
      }
    }
    note?.addEventListener('input', validateNote);
    note?.addEventListener('change', validateNote);
    validateNote();

    // Prevent double submit
    form?.addEventListener('submit', (e)=>{
      validateNote();
      if (!form.checkValidity()) return; // browser shows messages
      const btn = form.querySelector('button[type="submit"]');
      if (btn){
        btn.disabled = true;
        btn.classList.add('opacity-50','pointer-events-none');
      }
    });

    // Draft persistence (eleve, matiere, note, type)
    const draftKey = 'note_form_draft';
    try{
      const saved = JSON.parse(localStorage.getItem(draftKey)||'null');
      if (saved){
        if (eleve && !eleve.value) eleve.value = saved.eleve_id||'';
        if (matiere && !matiere.value) matiere.value = saved.matiere||'';
        if (note && !note.value) note.value = saved.note||'';
        if (type && !type.value) type.value = saved.type||'Devoir';
        updateCount(); validateNote();
      }
    }catch{}
    function saveDraft(){
      const data = {
        eleve_id: eleve?.value||'',
        matiere: matiere?.value||'',
        note: note?.value||'',
        type: type?.value||'Devoir'
      };
      try{ localStorage.setItem(draftKey, JSON.stringify(data)); }catch{}
    }
    eleve?.addEventListener('change', saveDraft);
    matiere?.addEventListener('input', saveDraft);
    note?.addEventListener('input', saveDraft);
    type?.addEventListener('change', saveDraft);
  </script>
</body>
</html>
