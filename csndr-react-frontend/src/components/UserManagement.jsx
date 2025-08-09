import React, { useState } from 'react';
import { Plus, Edit, Trash2 } from 'lucide-react';
import Modal from './Modal';
import { mockData } from '../mockData';
import { saveUser, deleteUser as apiDeleteUser } from '../services/api';
import Toast from './Toast';
import ConfirmDialog from './ConfirmDialog';

const UserManagement = ({ data = mockData, setData = () => {}, refreshUsers = async () => {} }) => {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingUser, setEditingUser] = useState(null);
  const [formData, setFormData] = useState({
    nom: '', prenom: '', email: '', password: '', role: 'eleve', classe_id: ''
  });
  const [submitting, setSubmitting] = useState(false);
  const [toast, setToast] = useState({ open: false, type: 'success', message: '' });
  const [confirm, setConfirm] = useState({ open: false, id: null });

  // Ouvre le modal pour création/édition
  const openModal = (user = null) => {
    setEditingUser(user);
    setFormData(user || { nom: '', prenom: '', email: '', password: '', role: 'eleve', classe_id: '' });
    setIsModalOpen(true);
  };

  // Gestion du formulaire
  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      setSubmitting(true);
      // For now, only create new users via API
      if (!editingUser) {
        await saveUser({
          nom: formData.nom,
          prenom: formData.prenom,
          email: formData.email,
          password: formData.password,
          role: formData.role,
          classe_id: formData.classe_id || null,
        });
        setToast({ open: true, type: 'success', message: "Utilisateur créé avec succès" });
      } else {
        setToast({ open: true, type: 'error', message: 'La modification utilisateur sera ajoutée prochainement.' });
      }
      await refreshUsers();
      setIsModalOpen(false);
    } catch (err) {
      console.error(err);
      setToast({ open: true, type: 'error', message: "Erreur lors de l'enregistrement de l'utilisateur" });
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
      await apiDeleteUser(userId);
      await refreshUsers();
      setToast({ open: true, type: 'success', message: 'Utilisateur supprimé' });
    } catch (err) {
      console.error(err);
      setToast({ open: true, type: 'error', message: "Erreur lors de la suppression de l'utilisateur" });
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
      <div className="card responsive-table">
        <table className="w-full">
          <thead className="bg-[var(--bleu-clair)]">
            <tr>
              <th className="px-4 py-3 text-left text-title">Nom</th>
              <th className="px-4 py-3 text-left text-title">Prénom</th>
              <th className="px-4 py-3 text-left text-title">Email</th>
              <th className="px-4 py-3 text-left text-title">Rôle</th>
              <th className="px-4 py-3 text-left text-title">Classe</th>
              <th className="px-4 py-3 text-left text-title">Actions</th>
            </tr>
          </thead>
          <tbody>
            {(data?.users || []).map(user => (
              <tr key={user.id} className="border-t">
                <td data-label="Nom" className="px-4 py-3 text-body">{user.nom}</td>
                <td data-label="Prénom" className="px-4 py-3 text-body">{user.prenom}</td>
                <td data-label="Email" className="px-4 py-3 text-body">{user.email}</td>
                <td data-label="Rôle" className="px-4 py-3">
                  <span className={`px-2 py-1 rounded text-sm ${
                    user.role === 'admin' ? 'bg-[var(--rouge-erreur)] text-[var(--blanc-pur)]' :
                    user.role === 'professeur' ? 'bg-[var(--bleu-principal)] text-[var(--blanc-pur)]' :
                    user.role === 'parent' ? 'bg-[var(--vert-accent)] text-[var(--blanc-pur)]' :
                    'bg-[var(--orange-secondaire)] text-[var(--blanc-pur)]'
                  }`}>
                    {user.role}
                  </span>
                </td>
                <td data-label="Classe" className="px-4 py-3 text-body">
                  {user.classe_id && (data?.classes || []).find(c => c.id === user.classe_id)?.nom}
                </td>
                <td data-label="Actions" className="px-4 py-3">
                  <div className="flex gap-2">
                    <button onClick={() => openModal(user)} className="text-[var(--bleu-principal)] hover:text-opacity-80">
                      <Edit size={16} />
                    </button>
                    <button onClick={() => handleDelete(user.id)} className="text-[var(--rouge-erreur)] hover:text-opacity-80">
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title={editingUser ? 'Modifier utilisateur' : 'Nouvel utilisateur'}
      >
        <div className="space-y-4">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-body mb-1">Nom</label>
              <input
                type="text"
                value={formData.nom}
                onChange={(e) => setFormData({...formData, nom: e.target.value})}
                className="input"
                required
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-body mb-1">Prénom</label>
              <input
                type="text"
                value={formData.prenom}
                onChange={(e) => setFormData({...formData, prenom: e.target.value})}
                className="input"
                required
              />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-body mb-1">Email</label>
            <input
              type="email"
              value={formData.email}
              onChange={(e) => setFormData({...formData, email: e.target.value})}
              className="input"
              required
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-body mb-1">Mot de passe</label>
            <input
              type="password"
              value={formData.password}
              onChange={(e) => setFormData({...formData, password: e.target.value})}
              className="input"
              required
            />
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-body mb-1">Rôle</label>
              <select
                value={formData.role}
                onChange={(e) => setFormData({...formData, role: e.target.value})}
                className="input"
              >
                <option value="admin">Administrateur</option>
                <option value="professeur">Professeur</option>
                <option value="parent">Parent</option>
                <option value="eleve">Élève</option>
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-body mb-1">Classe</label>
              <select
                value={formData.classe_id || ''}
                onChange={(e) => setFormData({...formData, classe_id: e.target.value ? parseInt(e.target.value) : null})}
                className="input"
              >
                <option value="">Aucune classe</option>
                {(data?.classes || []).map(classe => (
                  <option key={classe.id} value={classe.id}>{classe.nom}</option>
                ))}
              </select>
            </div>
          </div>
          <div className="flex gap-2 justify-end">
            <button onClick={() => setIsModalOpen(false)} className="px-4 py-2 border rounded hover:bg-[var(--bleu-clair)] text-body text-sm sm:text-base">
              Annuler
            </button>
            <button onClick={handleSubmit} className="btn-primary" disabled={submitting}>
              {submitting ? 'Enregistrement…' : (editingUser ? 'Modifier' : 'Créer')}
            </button>
          </div>
        </div>
      </Modal>
      <Toast {...toast} onClose={() => setToast({...toast, open: false})} />
      <ConfirmDialog
        open={confirm.open}
        title="Supprimer l'utilisateur"
        message="Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible."
        confirmText="Supprimer"
        cancelText="Annuler"
        onConfirm={confirmDelete}
        onCancel={() => setConfirm({ open: false, id: null })}
      />
    </div>
  );
};

export default UserManagement;