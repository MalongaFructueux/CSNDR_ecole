import React from 'react';

/**
 * Composant RoleBadge - Affichage des badges de rôle avec couleurs distinctives
 * 
 * Ce composant gère :
 * - L'affichage des badges de rôle avec des couleurs spécifiques
 * - La traduction des rôles en français
 * - L'apparence visuelle cohérente avec la charte graphique
 * 
 * Chaque rôle a sa propre couleur :
 * - Admin : Rouge (#DC2626) - Accès complet
 * - Professeur : Vert (#059669) - Gestion pédagogique
 * - Parent : Bleu (#2563EB) - Consultation enfants
 * - Élève : Violet (#7C3AED) - Accès limité
 * 
 * @param {string} role - Rôle de l'utilisateur (admin, professeur, parent, eleve)
 * @param {string} className - Classes CSS supplémentaires
 * @returns {JSX.Element} Badge avec couleur et texte appropriés
 */
const RoleBadge = ({ role, className = '' }) => {
  /**
   * Configuration des badges selon le rôle
   * Retourne un objet avec les propriétés de style et le label
   */
  const getRoleConfig = (role) => {
    switch (role) {
      case 'admin':
        return {
          backgroundColor: 'bg-role-admin',
          textColor: 'text-white',
          borderColor: 'border-role-admin',
          label: 'Administrateur'
        };
      case 'professeur':
        return {
          backgroundColor: 'bg-role-professeur',
          textColor: 'text-white',
          borderColor: 'border-role-professeur',
          label: 'Professeur'
        };
      case 'parent':
        return {
          backgroundColor: 'bg-role-parent',
          textColor: 'text-white',
          borderColor: 'border-role-parent',
          label: 'Parent'
        };
      case 'eleve':
        return {
          backgroundColor: 'bg-role-eleve',
          textColor: 'text-white',
          borderColor: 'border-role-eleve',
          label: 'Élève'
        };
      default:
        return {
          backgroundColor: 'bg-gray-500',
          textColor: 'text-white',
          borderColor: 'border-gray-500',
          label: role
        };
    }
  };

  // Récupération de la configuration selon le rôle
  const config = getRoleConfig(role);

  return (
    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.backgroundColor} ${config.textColor} ${config.borderColor} border ${className}`}>
      {config.label}
    </span>
  );
};

export default RoleBadge;
