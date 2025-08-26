<?php
// Détermine la page actuelle pour le lien actif
$activePage = basename($_SERVER['REQUEST_URI']);

function is_active(string $pageName, string $activePage): string {
    // Gère le cas de la racine/dashboard
    if ($pageName === 'dashboard' && in_array($activePage, ['', 'dashboard'])) {
        return ' active';
    }
    return str_contains($activePage, $pageName) ? ' active' : '';
}
?>
<aside class="sidebar">
  <div class="sidebar-header">
    <a href="<?= $base ?? '' ?>/dashboard" class="sidebar-logo">
      <img src="<?= $base ?? '' ?>/assets/logo.PNG" alt="CSNDR Logo">
      <span class="sidebar-logo-text">CSNDR</span>
    </a>
  </div>
  <nav class="sidebar-nav">
    <a href="<?= $base ?? '' ?>/dashboard" class="sidebar-link<?= is_active('dashboard', $activePage) ?>">
      <i data-lucide="layout-dashboard"></i>
      <span>Tableau de bord</span>
    </a>
    <a href="<?= $base ?? '' ?>/notes" class="sidebar-link<?= is_active('notes', $activePage) ?>">
      <i data-lucide="star"></i>
      <span>Notes</span>
    </a>
    <a href="<?= $base ?? '' ?>/classes" class="sidebar-link<?= is_active('classes', $activePage) ?>">
      <i data-lucide="book"></i>
      <span>Classes</span>
    </a>
    <a href="<?= $base ?? '' ?>/homeworks" class="sidebar-link<?= is_active('homeworks', $activePage) ?>">
      <i data-lucide="book-open"></i>
      <span>Devoirs</span>
    </a>
    <a href="<?= $base ?? '' ?>/events" class="sidebar-link<?= is_active('events', $activePage) ?>">
      <i data-lucide="calendar"></i>
      <span>Événements</span>
    </a>
    <a href="<?= $base ?? '' ?>/messages" class="sidebar-link<?= is_active('messages', $activePage) ?>">
      <i data-lucide="message-square"></i>
      <span>Messagerie</span>
    </a>
    <?php if (($user['role'] ?? '') === 'admin'): ?>
      <a href="<?= $base ?? '' ?>/admin/users" class="sidebar-link<?= is_active('users', $activePage) ?>">
        <i data-lucide="users"></i>
        <span>Utilisateurs</span>
      </a>
    <?php endif; ?>
  </nav>
  <div class="sidebar-footer">
    <div class="flex items-center gap-3 p-4 border-t border-[var(--bleu-principal)]/20">
        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[var(--bleu-principal)] to-[var(--vert-accent)] text-white flex items-center justify-center font-bold text-sm">
            <?= strtoupper(substr($user['prenom'] ?? 'U', 0, 1)) ?>
        </div>
        <div class="flex-1 overflow-hidden">
            <p class="text-sm font-semibold text-white truncate"><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></p>
            <p class="text-xs text-white/60 capitalize"><?= htmlspecialchars($user['role'] ?? '') ?></p>
        </div>
        <a href="<?= $base ?? '' ?>/logout" class="text-white/60 hover:text-white transition-colors">
            <i data-lucide="log-out" class="w-5 h-5"></i>
        </a>
    </div>
  </div>
</aside>
