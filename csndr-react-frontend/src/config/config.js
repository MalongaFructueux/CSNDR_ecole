/**
 * Configuration centralisée pour l'application CSNDR
 */

const config = {
  // Configuration de l'API
  api: {
    baseURL: process.env.REACT_APP_API_URL || 'http://127.0.0.1:8000/api',
    timeout: 10000,
  },

  // Configuration de l'application
  app: {
    name: 'Centre Scolaire Notre Dame du Rosaire',
    version: '1.0.0',
    description: 'Plateforme scolaire moderne et sécurisée',
  },

  // Configuration des rôles
  roles: {
    admin: {
      label: 'Administrateur',
      color: 'bg-role-admin',
      permissions: ['all']
    },
    professeur: {
      label: 'Professeur',
      color: 'bg-role-professeur',
      permissions: ['read', 'write', 'delete_own']
    },
    parent: {
      label: 'Parent',
      color: 'bg-role-parent',
      permissions: ['read_children']
    },
    eleve: {
      label: 'Élève',
      color: 'bg-role-eleve',
      permissions: ['read_own']
    }
  },

  // Configuration des messages d'erreur
  errors: {
    network: 'Erreur de connexion au serveur',
    unauthorized: 'Accès non autorisé',
    forbidden: 'Accès refusé',
    notFound: 'Ressource non trouvée',
    validation: 'Données invalides',
    server: 'Erreur interne du serveur',
    unknown: 'Une erreur inattendue s\'est produite'
  },

  // Configuration des notifications
  notifications: {
    defaultDuration: 5000,
    successDuration: 3000,
    errorDuration: 8000,
    warningDuration: 6000,
    infoDuration: 5000
  },

  // Configuration des dates
  dateFormat: {
    display: 'DD/MM/YYYY',
    api: 'YYYY-MM-DD',
    datetime: 'DD/MM/YYYY HH:mm'
  }
};

export default config;
