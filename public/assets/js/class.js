document.addEventListener('click', (e) => {
  const a = e.target.closest('[data-confirm]');
  if (a) {
    if (!confirm(a.getAttribute('data-confirm'))) {
      e.preventDefault();
    }
  }
});
