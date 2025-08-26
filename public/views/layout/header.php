<?php
// public/views/layout/header.php

// Le titre est défini dans la page qui inclut cet en-tête
$pageTitle = $pageTitle ?? 'Page'; 
?>
<header class="page-header">
  <div class="page-header-content">
    <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
    <div class="flex items-center gap-4">
      <a href="#" class="relative text-secondary hover:text-primary transition-colors">
        <i data-lucide="bell"></i>
        <span class="absolute -top-1 -right-1 w-3 h-3 bg-[var(--rouge-erreur)] rounded-full border-2 border-white"></span>
      </a>
      <a href="#" class="relative text-secondary hover:text-primary transition-colors">
        <i data-lucide="message-square"></i>
        <span class="absolute -top-1 -right-1 w-3 h-3 bg-[var(--vert-accent)] rounded-full border-2 border-white"></span>
      </a>
    </div>
  </div>
</header>
