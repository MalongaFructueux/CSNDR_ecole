import React from 'react';
import { useNavigate, useLocation } from 'react-router-dom';

const Navigation = ({ user, onLogout }) => {
  const navigate = useNavigate();
  const location = useLocation();
  const menuItems = {
    admin: ['users', 'classes', 'events', 'messages'],
    professeur: ['messages', 'homeworks', 'grades', 'events'],
    eleve: ['homeworks', 'grades', 'events', 'messages'],
    parent: ['homeworks', 'grades', 'events', 'messages'],
  }[user.role] || [];

  const toggleTheme = () => {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    document.documentElement.setAttribute('data-theme', currentTheme === 'dark' ? 'light' : 'dark');
  };

  return (
    <nav className="bg-[var(--bleu-principal)]/95 backdrop-blur-sm text-[var(--blanc-pur)] p-4 shadow-lg sticky top-0 z-40">
      <div className="container mx-auto flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div className="flex items-center gap-3">
          <div className="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-semibold">EP</div>
          <h1 className="text-xl sm:text-2xl font-semibold">École Primaire</h1>
          <span className="ml-2 inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-white/15 border border-white/20">
            {user.role}
          </span>
        </div>
        <div className="flex items-center gap-3">
          <span className="text-white/90 text-sm sm:text-base">{user.prenom} {user.nom}</span>
          <button
            onClick={toggleTheme}
            className="px-3 py-2 rounded text-sm sm:text-base text-white/90 border border-white/20 hover:bg-white/10"
            aria-label="Changer de thème"
          >
            {document.documentElement.getAttribute('data-theme') === 'dark' ? '☀️ Clair' : '🌙 Sombre'}
          </button>
          <button onClick={onLogout} className="px-3 py-2 bg-white text-[var(--bleu-principal)] rounded hover:bg-white/90 transition shadow-sm text-sm sm:text-base">
            Déconnexion
          </button>
        </div>
      </div>
      <div className="flex flex-wrap gap-2 sm:gap-3 mt-4">
        {menuItems.map(key => {
          const path = `/${key}`;
          const active = location.pathname === path;
          return (
            <button
              key={key}
              onClick={() => navigate(path)}
              className={`px-3 py-2 rounded text-sm sm:text-base transition border ${active ? 'bg-white text-[var(--bleu-principal)] border-white' : 'text-white/90 border-white/20 hover:bg-white/10'}`}
            >
              {key.charAt(0).toUpperCase() + key.slice(1)}
            </button>
          );
        })}
      </div>
    </nav>
  );
};

export default Navigation;