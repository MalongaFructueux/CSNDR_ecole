import React, { useState, useEffect } from 'react';
import { Plus, Trash2, Edit } from 'lucide-react';
import Modal from './Modal';
import RoleBadge from './RoleBadge';
import { getUsers, createUser, deleteUser, updateUser } from '../services/api';
import Toast from './Toast';
import ConfirmDialog from './ConfirmDialog';

const UserManagement = ({ user }) => {
  const [users, setUsers] = useState([]);
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
  const [submitting, setSubmitting] = useState(false);
  const [toast, setToast] = useState({ open: false, type: 'success', message: '' });
  const [confirm, setConfirm] = useState({ open: false, id: null });
  const [error, setError] = useState(null);

  const loadUsers = async () => {
    try {
      const response = await getUsers();
      setUsers(response.data);
    } catch (err) {
      console.error(err);
      setToast({ open: true, type: 'error', message: "Erreur lors de la récupération des utilisateurs" });
    }
  };

  useEffect(() => {
    loadUsers();
  }, []);

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
    setIsModalOpen(true);
  };

  // Gestion du formulaire
  const handleSubmit = async (e) => {
    e.preventDefault();
    const formDataObj = new FormData(e.target);
    const userData = {
      nom: formDataObj.get('nom'),
      prenom: formDataObj.get('prenom'),
      email: formDataObj.get('email'),
      mot_de_passe: formDataObj.get('mot_de_passe'),
      role: formDataObj.get('role'),
      classe_id: formDataObj.get('classe_id') || null,
      parent_id: formDataObj.get('parent_id') || null
    };

    try {
      setSubmitting(true);
      if (editingUser) {
        // Modification d'un utilisateur existant
        await updateUser(editingUser.id, userData);
        setToast({ open: true, type: 'success', message: "Utilisateur modifié avec succès" });
      } else {
        // Création d'un nouvel utilisateur
        await createUser(userData);
        setToast({ open: true, type: 'success', message: "Utilisateur créé avec succès" });
      }
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
      loadUsers();
    } catch (error) {
      console.error('Erreur:', error);
      setToast({ open: true, type: 'error', message: "Erreur lors de la sauvegarde de l'utilisateur" });
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
      setToast({ open: true, type: 'error', message: 'Erreur lors de la suppression de l\'utilisateur' });
    }
  };

  return (
    <div className="p-4 sm:p-6">
      <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <h2 className="text-xl sm:text-2xl font-semibold text-title">Gestion des utilisateurs</h2>
        <button onClick={() => openModal()} className="flex items-center gap-2 btn-primary mt-4 sm:mt-0">
          <Plus size={16} />
          Nouvel utilisateur
        </button>
      </div>
      
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {users.map(user => (
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
      }}>
        <div className="p-6">
          <h3 className="text-lg font-semibold mb-4">
            {editingUser ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur'}
          </h3>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Nom
                </label>
                <input
                  type="text"
                  name="nom"
                  value={formData.nom}
                  onChange={(e) => setFormData({...formData, nom: e.target.value})}
                  required
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="Nom de famille"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Prénom
                </label>
                <input
                  type="text"
                  name="prenom"
                  value={formData.prenom}
                  onChange={(e) => setFormData({...formData, prenom: e.target.value})}
                  required
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="Prénom"
                />
              </div>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Email
              </label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={(e) => setFormData({...formData, email: e.target.value})}
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="email@exemple.com"
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Mot de passe
              </label>
              <input
                type="password"
                name="mot_de_passe"
                value={formData.mot_de_passe}
                onChange={(e) => setFormData({...formData, mot_de_passe: e.target.value})}
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Mot de passe"
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Rôle
              </label>
              <select
                name="role"
                value={formData.role}
                onChange={(e) => setFormData({...formData, role: e.target.value})}
                required
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
                  Classe ID (optionnel)
                </label>
                <input
                  type="number"
                  name="classe_id"
                  value={formData.classe_id}
                  onChange={(e) => setFormData({...formData, classe_id: e.target.value})}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="ID de la classe"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Parent ID (optionnel)
                </label>
                <input
                  type="number"
                  name="parent_id"
                  value={formData.parent_id}
                  onChange={(e) => setFormData({...formData, parent_id: e.target.value})}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  placeholder="ID du parent"
                />
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