import React, { useState, useEffect } from 'react';
import { Plus, Trash2, Edit } from 'lucide-react';
import Modal from './Modal';
import { getClasses, createClass, deleteClass, updateClass } from '../services/api';
import Toast from './Toast';
import ConfirmDialog from './ConfirmDialog';

const ClassManagement = ({ user }) => {
  const [classes, setClasses] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingClass, setEditingClass] = useState(null);
  const [formData, setFormData] = useState({ nom: '' });
  const [submitting, setSubmitting] = useState(false);
  const [toast, setToast] = useState({ open: false, type: 'success', message: '' });
  const [confirm, setConfirm] = useState({ open: false, id: null });
  const [error, setError] = useState(null);

  const loadClasses = async () => {
    try {
      const response = await getClasses();
      setClasses(response.data);
    } catch (err) {
      console.error(err);
      setToast({ open: true, type: 'error', message: "Erreur lors de la récupération des classes" });
    }
  };

  useEffect(() => {
    loadClasses();
  }, []);

  // Ouvre le modal
  const openModal = (classe = null) => {
    setEditingClass(classe);
    setFormData(classe || { nom: '' });
    setIsModalOpen(true);
  };

  // Gestion du formulaire
  const handleSubmit = async (e) => {
    e.preventDefault();
    const formDataObj = new FormData(e.target);
    const classData = {
      nom: formDataObj.get('nom')
    };

    try {
      setSubmitting(true);
      if (editingClass) {
        // Modification d'une classe existante
        await updateClass(editingClass.id, classData);
        setToast({ open: true, type: 'success', message: 'Classe modifiée avec succès' });
      } else {
        // Création d'une nouvelle classe
        await createClass(classData);
        setToast({ open: true, type: 'success', message: 'Classe créée avec succès' });
      }
      setIsModalOpen(false);
      setEditingClass(null);
      setFormData({ nom: '' });
      loadClasses();
    } catch (error) {
      console.error('Erreur:', error);
      setToast({ open: true, type: 'error', message: 'Erreur lors de la sauvegarde de la classe' });
    } finally {
      setSubmitting(false);
    }
  };

  // Suppression d'une classe
  const handleDelete = (classId) => {
    setConfirm({ open: true, id: classId });
  };

  const confirmDelete = async () => {
    try {
      const classId = confirm.id;
      setConfirm({ open: false, id: null });
      await deleteClass(classId);
      await loadClasses();
      setToast({ open: true, type: 'success', message: 'Classe supprimée' });
    } catch (err) {
      console.error(err);
      setToast({ open: true, type: 'error', message: 'Erreur lors de la suppression de la classe' });
    }
  };

  return (
    <div className="p-4 sm:p-6">
      <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <h2 className="text-xl sm:text-2xl font-semibold text-title">Gestion des classes</h2>
        <button onClick={() => openModal()} className="flex items-center gap-2 btn-primary mt-4 sm:mt-0">
          <Plus size={16} />
          Nouvelle classe
        </button>
      </div>
      
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {classes.map(classe => (
          <div key={classe.id} className="card border-[var(--vert-accent)]">
            <div className="flex justify-between items-start mb-4">
              <h3 className="text-lg sm:text-xl font-semibold text-title">{classe.nom}</h3>
              <div className="flex gap-2">
                <button onClick={() => openModal(classe)} className="text-[var(--bleu-principal)] hover:text-opacity-80">
                  <Edit size={16} />
                </button>
                <button onClick={() => handleDelete(classe.id)} className="text-[var(--rouge-erreur)] hover:text-opacity-80">
                  <Trash2 size={16} />
                </button>
              </div>
            </div>
            <div className="text-sm text-body">
              <p>ID: {classe.id}</p>
              <p>Créée le: {new Date(classe.created_at).toLocaleDateString('fr-FR')}</p>
            </div>
          </div>
        ))}
      </div>

      {/* Modal pour créer/modifier une classe */}
      <Modal isOpen={isModalOpen} onClose={() => {
        setIsModalOpen(false);
        setEditingClass(null);
        setFormData({ nom: '' });
      }}>
        <div className="p-6">
          <h3 className="text-lg font-semibold mb-4">
            {editingClass ? 'Modifier la classe' : 'Nouvelle classe'}
          </h3>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Nom de la classe
              </label>
              <input
                type="text"
                name="nom"
                value={formData.nom}
                onChange={(e) => setFormData({...formData, nom: e.target.value})}
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Ex: 6ème A"
              />
            </div>
            
            <div className="flex justify-end gap-2">
              <button
                type="button"
                onClick={() => {
                  setIsModalOpen(false);
                  setEditingClass(null);
                  setFormData({ nom: '' });
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
                {submitting ? 'Enregistrement...' : (editingClass ? 'Modifier' : 'Créer')}
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
        message="Êtes-vous sûr de vouloir supprimer cette classe ? Cette action est irréversible."
        onConfirm={confirmDelete}
        onCancel={() => setConfirm({ open: false, id: null })}
      />
    </div>
  );
};

export default ClassManagement;