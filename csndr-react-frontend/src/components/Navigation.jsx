import React from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import RoleBadge from './RoleBadge';

/**
 * Composant Navigation - Barre de navigation principale avec gestion des rôles
 * 
 * Ce composant gère :
 * - L'affichage du header avec le logo CSNDR
 * - La navigation selon le rôle de l'utilisateur
 * - L'affichage des badges de rôle avec couleurs distinctives
 * - La déconnexion
 * 
 * @param {Object} user - Objet utilisateur connecté avec ses propriétés (id, nom, prenom, role)
 * @param {Function} onLogout - Fonction de déconnexion
 */
const Navigation = ({ user, onLogout }) => {
  const navigate = useNavigate();
  const location = useLocation();
  
  /**
   * Configuration des menus selon le rôle utilisateur
   * Chaque rôle a accès à des fonctionnalités spécifiques
   */
  const menuItems = {
    // Admin : Accès complet à toutes les fonctionnalités
    admin: [
      { key: 'users', label: 'Utilisateurs', icon: '👥' },
      { key: 'classes', label: 'Classes', icon: '🏫' },
      { key: 'events', label: 'Événements', icon: '📅' },
      { key: 'messages', label: 'Messages', icon: '💬' },
      { key: 'homework', label: 'Devoirs', icon: '📚' },
      { key: 'grades', label: 'Notes', icon: '📊' }
    ],
    // Professeur : Gestion des devoirs, notes et communication
    professeur: [
      { key: 'messages', label: 'Messages', icon: '💬' },
      { key: 'homework', label: 'Devoirs', icon: '📚' },
      { key: 'grades', label: 'Notes', icon: '📊' },
      { key: 'events', label: 'Événements', icon: '📅' }
    ],
    // Parent : Consultation des devoirs et notes de ses enfants
    parent: [
      { key: 'messages', label: 'Messages', icon: '💬' },
      { key: 'homework', label: 'Devoirs', icon: '📚' },
      { key: 'grades', label: 'Notes', icon: '📊' },
      { key: 'events', label: 'Événements', icon: '📅' }
    ],
    // Élève : Consultation de ses propres devoirs et notes
    eleve: [
      { key: 'homework', label: 'Devoirs', icon: '📚' },
      { key: 'grades', label: 'Notes', icon: '📊' },
      { key: 'events', label: 'Événements', icon: '📅' },
      { key: 'messages', label: 'Messages', icon: '💬' }
    ]
  }[user.role] || [];

  /**
   * Obtient la couleur de fond selon le rôle
   * Utilisé pour les badges et éléments visuels
   */
  const getRoleColor = (role) => {
    switch (role) {
      case 'admin': return 'bg-role-admin';
      case 'professeur': return 'bg-role-professeur';
      case 'parent': return 'bg-role-parent';
      case 'eleve': return 'bg-role-eleve';
      default: return 'bg-gray-500';
    }
  };

  return (
    <nav className="bg-gradient-to-r from-primary-600 to-primary-800 text-white p-4 shadow-lg sticky top-0 z-40">
      {/* Header principal avec logo et informations de l'école */}
      <div className="container mx-auto flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div className="flex items-center gap-3">
          {/* Logo CSNDR avec design circulaire */}
          <div className="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-lg font-bold text-white">
            CSNDR
          </div>
          <div>
            <h1 className="text-xl sm:text-2xl font-bold">Centre Scolaire Notre Dame du Rosaire</h1>
            <p className="text-white/80 text-sm">Brazzaville, Congo</p>
          </div>
        </div>
        
        {/* Informations utilisateur et bouton de déconnexion */}
        <div className="flex items-center gap-3">
          <div className="text-right">
            <p className="text-white font-medium">{user.prenom} {user.nom}</p>
            {/* Badge de rôle avec couleur distinctive */}
            <RoleBadge role={user.role} />
          </div>
          <button 
            onClick={onLogout} 
            className="px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-lg hover:bg-white/30 transition-all duration-200 border border-white/30 hover:border-white/50 shadow-lg"
          >
            Déconnexion
          </button>
        </div>
      </div>
      
      {/* Navigation des menus selon le rôle */}
      <div className="flex flex-wrap gap-2 sm:gap-3 mt-4 pt-4 border-t border-white/20">
        {menuItems.map(item => {
          const path = `/${item.key}`;
          const active = location.pathname === path;
          return (
            <button
              key={item.key}
              onClick={() => navigate(path)}
              className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm sm:text-base transition-all duration-200 border ${
                active 
                  ? 'bg-white text-primary-700 border-white shadow-lg' 
                  : 'text-white/90 border-white/20 hover:bg-white/10 hover:border-white/30'
              }`}
            >
              <span>{item.icon}</span>
              <span>{item.label}</span>
            </button>
          );
        })}
      </div>
    </nav>
  );
};

export default Navigation;