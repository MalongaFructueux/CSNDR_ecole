document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
      const email = loginForm.email.value.trim();
      const pwd = loginForm.password.value;
      let ok = true;
      if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) { ok = false; }
      if (pwd.length < 6) { ok = false; }
      if (!ok) {
        e.preventDefault();
        alert('Veuillez saisir un email valide et un mot de passe (>=6).');
      }
    });
  }

  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', (e) => {
      const email = registerForm.email.value.trim();
      const pwd = registerForm.password.value;
      const nom = registerForm.nom.value.trim();
      const prenom = registerForm.prenom.value.trim();
      let ok = true;
      if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) ok = false;
      if (pwd.length < 6) ok = false;
      if (!nom || !prenom) ok = false;
      if (!ok) { e.preventDefault(); alert('Champs requis invalides.'); }
    });
  }
});
