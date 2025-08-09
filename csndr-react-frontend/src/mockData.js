// Données mockées
export const mockData = {
  users: [
    // Comptes de test alignés avec le backend seedé
    { id: 1, nom: 'Admin', prenom: 'Principal', email: 'admin@csndr.test', password: 'Password123!', role: 'admin', classe_id: null },
    { id: 2, nom: 'Dupont', prenom: 'Paul', email: 'prof@csndr.test', password: 'Password123!', role: 'professeur', classe_id: 1 },
    { id: 3, nom: 'Martin', prenom: 'Claire', email: 'parent@csndr.test', password: 'Password123!', role: 'parent', classe_id: null },
    { id: 4, nom: 'Ndiaye', prenom: 'Amadou', email: 'eleve@csndr.test', password: 'Password123!', role: 'eleve', classe_id: 1, parent_id: 3 },
  ],
  classes: [
    { id: 1, nom: 'CP-A' },
    { id: 2, nom: 'CE1-B' },
    { id: 3, nom: 'CE2-A' },
  ],
  devoirs: [
    { id: 1, titre: 'Mathématiques - Additions', description: 'Exercices page 25', date_limite: '2024-12-15', professeur_id: 2, classe_id: 1 },
    { id: 2, titre: 'Français - Dictée', description: 'Réviser les mots de la semaine', date_limite: '2024-12-18', professeur_id: 2, classe_id: 1 },
  ],
  notes: [
    { id: 1, eleve_id: 4, matiere: 'Mathématiques', note: 15.5, professeur_id: 2, date: '2024-12-10' },
    { id: 2, eleve_id: 4, matiere: 'Français', note: 17.0, professeur_id: 2, date: '2024-12-12' },
  ],
  evenements: [
    { id: 1, titre: 'Réunion parents-professeurs', description: 'Rencontre trimestrielle', date: '2024-12-20', auteur_id: 1 },
    { id: 2, titre: 'Sortie pédagogique', description: 'Visite du musée', date: '2024-12-22', auteur_id: 2 },
  ],
  messages: [
    { id: 1, expediteur_id: 2, destinataire_id: 3, contenu: 'Emma a bien participé aujourd\'hui', date_envoi: '2024-12-10 14:30' },
  ]
};