import React, { useState } from 'react';
import { Plus, Edit, Trash2 } from 'lucide-react';
import Modal from './Modal';
import { mockData } from '../mockData';
import { saveClass as apiSaveClass, deleteClass as apiDeleteClass } from '../services/api';
import Toast from './Toast';
import ConfirmDialog from './ConfirmDialog';

const ClassManagement = ({ data = mockData, setData = () => {}, refreshClasses = async () => {} }) => {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingClass, setEditingClass] = useState(null);
  const [formData, setFormData] = useState({ nom: '' });
  const [submitting, setSubmitting] = useState(false);
  const [toast, setToast] = useState({ open: false, type: 'success', message: '' });
  const [confirm, setConfirm] = useState({ open: false, id: null });

  // Ouvre le modal
  const openModal = (classe = null) => {
    setEditingClass(classe);
    setFormData(classe || { nom: '' });
    setIsModalOpen(true);
  };

  // Gestion du formulaire
  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      setSubmitting(true);
      if (!editingClass) {
        await apiSaveClass({ nom: formData.nom });
        setToast({ open: true, type: 'success', message: 'Classe créée avec succès' });
      } else {
        setToast({ open: true, type: 'error', message: 'La modification de classe sera ajoutée prochainement.' });
      }
      await refreshClasses();
      setIsModalOpen(false);
    } catch (err) {
      console.error(err);
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
      await apiDeleteClass(classId);
      await refreshClasses();
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
        {(data?.classes || []).map(classe => {
          const students = (data?.users || []).filter(user => user.classe_id === classe.id && user.role === 'eleve');
          const teacher = (data?.users || []).find(user => user.classe_id === classe.id && user.role === 'professeur');
          return (
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
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <h4 className="font-medium text-title mb-2">Professeur</h4>
                  <p className="text-body text-sm sm:text-base">{teacher ? `${teacher.prenom} ${teacher.nom}` : 'Aucun'}</p>
                </div>
                <div>
                  <h4 className="font-medium text-title mb-2">Élèves ({students.length})</h4>
                  <div className="space-y-1">
                    {students.slice(0, 3).map(student => (
                      <p key={student.id} className="text-sm text-body">{student.prenom} {student.nom}</p>
                    ))}
                    {students.length > 3 && <p className="text-sm text-[var(--gris-neutre)]">+{students.length - 3} autres...</p>}
                  </div>
                </div>
              </div>
            </div>
          );
        })}
      </div>
      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title={editingClass ? 'Modifier classe' : 'Nouvelle classe'}
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-body mb-1">Nom de la classe</label>
            <input
              type="text"
              value={formData.nom}
              onChange={(e) => setFormData({...formData, nom: e.target.value})}
              className="input"
              required
              placeholder="ex: CP-A"
            />
          </div>
          <div className="flex gap-2 justify-end">
            <button onClick={() => setIsModalOpen(false)} className="px-4 py-2 border rounded hover:bg-[var(--bleu-clair)] text-body text-sm sm:text-base">
              Annuler
            </button>
            <button onClick={handleSubmit} className="btn-primary" disabled={submitting}>
              {submitting ? 'Enregistrement…' : (editingClass ? 'Modifier' : 'Créer')}
            </button>
          </div>
        </div>
      </Modal>
      <Toast {...toast} onClose={() => setToast({...toast, open: false})} />
      <ConfirmDialog
        open={confirm.open}
        title="Supprimer la classe"
        message="Êtes-vous sûr de vouloir supprimer cette classe ? Cette action est irréversible."
        confirmText="Supprimer"
        cancelText="Annuler"
        onConfirm={confirmDelete}
        onCancel={() => setConfirm({ open: false, id: null })}
      />
    </div>
  );
};

export default ClassManagement;