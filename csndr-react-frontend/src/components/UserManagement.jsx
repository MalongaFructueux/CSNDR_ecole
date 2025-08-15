import React, { useState, useEffect } from 'react';
import { Plus, Trash2, Edit, Search, Filter, RefreshCw } from 'lucide-react';
import Modal from './Modal';
import RoleBadge from './RoleBadge';
import { getUsers, createUser, deleteUser, updateUser, searchUsers } from '../services/api';
import Toast from './Toast';
import ConfirmDialog from './ConfirmDialog';

const UserManagement = ({ user }) => {
  const [users, setUsers] = useState([]);
  const [filteredUsers, setFilteredUsers] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingUser, setEditingUser] = useState(null);
  const [formData, setFormData] = useState({
    nom: '',
    prenom: '',
    email: '',
    mot_de_passe: '',
    role: 'eleve',
    classe_id: '',
    parent_id: ''
  });
  const [formErrors, setFormErrors] = useState({});
  const [submitting, setSubmitting] = useState(false);
  const [loading, setLoading] = useState(false);
  const [toast, setToast] = useState({ open: false, type: 'success', message: '' });
  const [confirm, setConfirm] = useState({ open: false, id: null });
  const [error, setError] = useState(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [roleFilter, setRoleFilter] = useState('all');

  // Validation du formulaire
  const validateForm = () => {
    const errors = {};
    
    if (!formData.nom.trim()) {
      errors.nom = 'Le nom est requis';
    } else if (formData.nom.trim().length < 2) {
      errors.nom = 'Le nom doit contenir au moins 2 caractères';
    }
    
    if (!formData.prenom.trim()) {
      errors.prenom = 'Le prénom est requis';
    } else if (formData.prenom.trim().length < 2) {
      errors.prenom = 'Le prénom doit contenir au moins 2 caractères';
    }
    
    if (!formData.email.trim()) {
      errors.email = 'L\'email est requis';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
      errors.email = 'Format d\'email invalide';
    }
    
    if (!editingUser && !formData.mot_de_passe) {
      errors.mot_de_passe = 'Le mot de passe est requis';
    } else if (formData.mot_de_passe && formData.mot_de_passe.length < 6) {
      errors.mot_de_passe = 'Le mot de passe doit contenir au moins 6 caractères';
    }
    
    if (formData.role === 'eleve' && !formData.classe_id) {
      errors.classe_id = 'Une classe est requise pour un élève';
    }
    
    // Le parent_id est optionnel. Si non fourni, le backend en assignera un.
    // On valide seulement si une valeur est entrée mais n'est pas un nombre.
    if (formData.parent_id && !/^[0-9]+$/.test(formData.parent_id)) {
      errors.parent_id = 'L\'ID du parent doit être un nombre.';
    }
    
    setFormErrors(errors);
    return Object.keys(errors).length === 0;
  };

  const loadUsers = async () => {
    try {
      setLoading(true);
      setError(null);
      console.log('Début du chargement des utilisateurs...');
      const response = await getUsers();
      console.log('Utilisateurs chargés:', response.data);
      setUsers(response.data);
      setFilteredUsers(response.data);
    } catch (err) {
      console.error('Erreur lors du chargement des utilisateurs:', err);
      setError(err.message || 'Erreur lors de la récupération des utilisateurs');
      setToast({ open: true, type: 'error', message: err.message || 'Erreur lors de la récupération des utilisateurs' });
      setUsers([]);
      setFilteredUsers([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadUsers();
  }, []);

  // Filtrage des utilisateurs
  useEffect(() => {
    let filtered = users;
    
    // Filtre par recherche
    if (searchQuery.trim()) {
      filtered = filtered.filter(user => 
        user.nom.toLowerCase().includes(searchQuery.toLowerCase()) ||
        user.prenom.toLowerCase().includes(searchQuery.toLowerCase()) ||
        user.email.toLowerCase().includes(searchQuery.toLowerCase())
      );
    }
    
    // Filtre par rôle
    if (roleFilter !== 'all') {
      filtered = filtered.filter(user => user.role === roleFilter);
    }
    
    setFilteredUsers(filtered);
  }, [users, searchQuery, roleFilter]);

  // Ouvre le modal pour création/édition
  const openModal = (user = null) => {
    setEditingUser(user);
    setFormData(user || {
      nom: '',
      prenom: '',
      email: '',
      mot_de_passe: '',
      role: 'eleve',
      classe_id: '',
      parent_id: ''
    });
    setFormErrors({});
    setIsModalOpen(true);
  };

  // Gestion du formulaire
  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!validateForm()) {
      return;
    }

    setSubmitting(true);
    setError(null);

    const dataToSend = { ...formData };

    if (editingUser && !dataToSend.mot_de_passe) {
      delete dataToSend.mot_de_passe;
    }

    // Assurer que les IDs sont des nombres ou null
    if (dataToSend.classe_id) {
      dataToSend.classe_id = parseInt(dataToSend.classe_id, 10);
    } else {
      delete dataToSend.classe_id;
    }

    if (dataToSend.parent_id) {
      dataToSend.parent_id = parseInt(dataToSend.parent_id, 10);
    } else {
      delete dataToSend.parent_id;
    }

    try {
      if (editingUser) {
        await updateUser(editingUser.id, dataToSend);
        setToast({ open: true, type: 'success', message: 'Utilisateur modifié avec succès' });
      } else {
        await createUser(dataToSend);
        setToast({ open: true, type: 'success', message: 'Utilisateur créé avec succès' });
      }
      
      // Réinitialiser et fermer le modal
      setIsModalOpen(false);
      setEditingUser(null);
      setFormData({
        nom: '', prenom: '', email: '', mot_de_passe: '', 
        role: 'eleve', classe_id: '', parent_id: ''
      });
      setFormErrors({});

      await loadUsers();

    } catch (err) {
      console.error('Erreur:', err);
      const errorMessage = err.response?.data?.message || err.message || "Erreur lors de la sauvegarde de l'utilisateur";
      setToast({ open: true, type: 'error', message: errorMessage });
    } finally {
      setSubmitting(false);
    }
  };

  // Suppression d'un utilisateur
  const handleDelete = (userId) => {
    setConfirm({ open: true, id: userId });
  };

  const confirmDelete = async () => {
    try {
      const userId = confirm.id;
      setConfirm({ open: false, id: null });
      await deleteUser(userId);
      await loadUsers();
      setToast({ open: true, type: 'success', message: 'Utilisateur supprimé' });
    } catch (err) {
      console.error(err);
      const errorMessage = err.response?.data?.message || err.message || 'Erreur lors de la suppression de l\'utilisateur';
      setToast({ open: true, type: 'error', message: errorMessage });
    }
  };

  const resetFilters = () => {
    setSearchQuery('');
    setRoleFilter('all');
  };

  return (
    <div className="p-4 sm:p-6">
      <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <h2 className="text-xl sm:text-2xl font-semibold text-title">Gestion des utilisateurs</h2>
        <div className="flex gap-2">
          <button onClick={() => loadUsers()} className="flex items-center gap-2 bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors mt-4 sm:mt-0">
            <RefreshCw size={16} />
            Recharger
          </button>
          <button onClick={() => openModal()} className="flex items-center gap-2 btn-primary mt-4 sm:mt-0">
            <Plus size={16} />
            Nouvel utilisateur
          </button>
        </div>
      </div>

      {/* Filtres et recherche */}
      <div className="mb-6 bg-white p-4 rounded-lg shadow-sm border">
        <div className="flex flex-col sm:flex-row gap-4">
          <div className="flex-1">
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
              <input
                type="text"
                placeholder="Rechercher par nom, prénom ou email..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
            </div>
          </div>
          <div className="flex gap-2">
            <select
              value={roleFilter}
              onChange={(e) => setRoleFilter(e.target.value)}
              className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
            >
              <option value="all">Tous les rôles</option>
              <option value="admin">Administrateur</option>
              <option value="professeur">Professeur</option>
              <option value="parent">Parent</option>
              <option value="eleve">Élève</option>
            </select>
            <button
              onClick={resetFilters}
              className="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Réinitialiser
            </button>
          </div>
        </div>
        {searchQuery || roleFilter !== 'all' ? (
          <div className="mt-2 text-sm text-gray-600">
            {filteredUsers.length} utilisateur(s) trouvé(s)
          </div>
        ) : null}
      </div>
      
      {loading ? (
        <div className="flex items-center justify-center h-64">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
        </div>
      ) : error ? (
        <div className="text-center py-8">
          <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <p className="font-medium">Erreur de chargement</p>
            <p className="text-sm">{error}</p>
            <button 
              onClick={loadUsers}
              className="mt-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
            >
              Réessayer
            </button>
          </div>
        </div>
      ) : filteredUsers.length === 0 ? (
        <div className="text-center py-8 text-gray-500">
          <p>Aucun utilisateur trouvé</p>
          {(searchQuery || roleFilter !== 'all') && (
            <button 
              onClick={resetFilters}
              className="mt-2 px-4 py-2 text-primary-600 hover:text-primary-700 underline"
            >
              Réinitialiser les filtres
            </button>
          )}
        </div>
      ) : (
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {filteredUsers.map(user => (
            <div key={user.id} className="card border-[var(--vert-accent)]">
              <div className="flex justify-between items-start mb-4">
                <div>
                  <h3 className="text-lg sm:text-xl font-semibold text-title">{user.prenom} {user.nom}</h3>
                  <p className="text-sm text-body">{user.email}</p>
                </div>
                <div className="flex gap-2">
                  <button onClick={() => openModal(user)} className="text-[var(--bleu-principal)] hover:text-opacity-80">
                    <Edit size={16} />
                  </button>
                  <button onClick={() => handleDelete(user.id)} className="text-[var(--rouge-erreur)] hover:text-opacity-80">
                    <Trash2 size={16} />
                  </button>
                </div>
              </div>
              <div className="space-y-2">
                <RoleBadge role={user.role} />
                <div className="text-sm text-body">
                  <p>ID: {user.id}</p>
                  {user.classe_id && <p>Classe ID: {user.classe_id}</p>}
                  {user.parent_id && <p>Parent ID: {user.parent_id}</p>}
                </div>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Modal pour créer/modifier un utilisateur */}
      <Modal isOpen={isModalOpen} onClose={() => {
        setIsModalOpen(false);
        setEditingUser(null);
        setFormData({
          nom: '',
          prenom: '',
          email: '',
          mot_de_passe: '',
          role: 'eleve',
          classe_id: '',
          parent_id: ''
        });
        setFormErrors({});
      }}>
        <div className="p-6">
          <h3 className="text-lg font-semibold mb-4">
            {editingUser ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur'}
          </h3>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Nom *
                </label>
                <input
                  type="text"
                  name="nom"
                  value={formData.nom}
                  onChange={(e) => setFormData({...formData, nom: e.target.value})}
                  className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent ${
                    formErrors.nom ? 'border-red-500' : 'border-gray-300'
                  }`}
                  placeholder="Nom de famille"
                />
                {formErrors.nom && (
                  <p className="text-red-500 text-xs mt-1">{formErrors.nom}</p>
                )}
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Prénom *
                </label>
                <input
                  type="text"
                  name="prenom"
                  value={formData.prenom}
                  onChange={(e) => setFormData({...formData, prenom: e.target.value})}
                  className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent ${
                    formErrors.prenom ? 'border-red-500' : 'border-gray-300'
                  }`}
                  placeholder="Prénom"
                />
                {formErrors.prenom && (
                  <p className="text-red-500 text-xs mt-1">{formErrors.prenom}</p>
                )}
              </div>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Email *
              </label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={(e) => setFormData({...formData, email: e.target.value})}
                className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent ${
                  formErrors.email ? 'border-red-500' : 'border-gray-300'
                }`}
                placeholder="email@exemple.com"
              />
              {formErrors.email && (
                <p className="text-red-500 text-xs mt-1">{formErrors.email}</p>
              )}
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Mot de passe {!editingUser && '*'}
              </label>
              <input
                type="password"
                name="mot_de_passe"
                value={formData.mot_de_passe}
                onChange={(e) => setFormData({...formData, mot_de_passe: e.target.value})}
                className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent ${
                  formErrors.mot_de_passe ? 'border-red-500' : 'border-gray-300'
                }`}
                placeholder={editingUser ? "Laisser vide pour ne pas changer" : "Mot de passe"}
              />
              {formErrors.mot_de_passe && (
                <p className="text-red-500 text-xs mt-1">{formErrors.mot_de_passe}</p>
              )}
              {editingUser && (
                <p className="text-gray-500 text-xs mt-1">Laissez vide pour conserver le mot de passe actuel</p>
              )}
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Rôle *
              </label>
              <select
                name="role"
                value={formData.role}
                onChange={(e) => setFormData({...formData, role: e.target.value})}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              >
                <option value="admin">Administrateur</option>
                <option value="professeur">Professeur</option>
                <option value="parent">Parent</option>
                <option value="eleve">Élève</option>
              </select>
            </div>
            
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Classe ID {formData.role === 'eleve' && '*'}
                </label>
                <input
                  type="number"
                  name="classe_id"
                  value={formData.classe_id}
                  onChange={(e) => setFormData({...formData, classe_id: e.target.value})}
                  className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent ${
                    formErrors.classe_id ? 'border-red-500' : 'border-gray-300'
                  }`}
                  placeholder="ID de la classe"
                />
                {formErrors.classe_id && (
                  <p className="text-red-500 text-xs mt-1">{formErrors.classe_id}</p>
                )}
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Parent ID {formData.role === 'eleve' && '*'}
                </label>
                <input
                  type="number"
                  name="parent_id"
                  value={formData.parent_id}
                  onChange={(e) => setFormData({...formData, parent_id: e.target.value})}
                  className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent ${
                    formErrors.parent_id ? 'border-red-500' : 'border-gray-300'
                  }`}
                  placeholder="ID du parent"
                />
                {formErrors.parent_id && (
                  <p className="text-red-500 text-xs mt-1">{formErrors.parent_id}</p>
                )}
              </div>
            </div>
            
            <div className="flex justify-end gap-2">
              <button
                type="button"
                onClick={() => {
                  setIsModalOpen(false);
                  setEditingUser(null);
                  setFormData({
                    nom: '',
                    prenom: '',
                    email: '',
                    mot_de_passe: '',
                    role: 'eleve',
                    classe_id: '',
                    parent_id: ''
                  });
                  setFormErrors({});
                }}
                className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Annuler
              </button>
              <button
                type="submit"
                disabled={submitting}
                className="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50"
              >
                {submitting ? 'Enregistrement...' : (editingUser ? 'Modifier' : 'Créer')}
              </button>
            </div>
          </form>
        </div>
      </Modal>

      {/* Toast pour les notifications */}
      <Toast
        open={toast.open}
        type={toast.type}
        message={toast.message}
        onClose={() => setToast({ ...toast, open: false })}
      />

      {/* Dialog de confirmation pour la suppression */}
      <ConfirmDialog
        open={confirm.open}
        title="Confirmer la suppression"
        message="Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible."
        onConfirm={confirmDelete}
        onCancel={() => setConfirm({ open: false, id: null })}
      />
    </div>
  );
};

export default UserManagement;