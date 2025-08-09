import React, { useState } from 'react';
import { Plus, Edit, Trash2 } from 'lucide-react';
import Modal from './Modal';

const HomeworkManagement = ({ data, setData, user }) => {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingHomework, setEditingHomework] = useState(null);
  const [formData, setFormData] = useState({
    titre: '', description: '', date_limite: '', classe_id: ''
  });

  // Filtrer les devoirs selon rôle
  const getHomeworks = () => {
    if (user.role === 'admin') return data.devoirs;
    if (user.role === 'professeur') return data.devoirs.filter(d => d.professeur_id === user.id);
    if (user.role === 'eleve') return data.devoirs.filter(d => d.classe_id === user.classe_id);
    if (user.role === 'parent') {
      const enfant = data.users.find(u => u.parent_id === user.id);
      return enfant ? data.devoirs.filter(d => d.classe_id === enfant.classe_id) : [];
    }
    return [];
  };

  // Ouvre le modal
  const openModal = (homework = null) => {
    setEditingHomework(homework);
    setFormData(homework || { titre: '', description: '', date_limite: '', classe_id: user.classe_id || '' });
    setIsModalOpen(true);
  };

  // Gestion du formulaire
  const handleSubmit = (e) => {
    e.preventDefault();
    setData(prev => ({
      ...prev,
      devoirs: editingHomework
        ? prev.devoirs.map(homework => homework.id === editingHomework.id ? { ...formData, id: homework.id } : homework)
        : [...prev.devoirs, { ...formData, id: Date.now(), professeur_id: user.id, classe_id: parseInt(formData.classe_id) }]
    }));
    setIsModalOpen(false);
  };

  // Suppression d'un devoir
  const handleDelete = (homeworkId) => {
    if (window.confirm('Supprimer ce devoir ?')) {
      setData(prev => ({
        ...prev,
        devoirs: prev.devoirs.filter(homework => homework.id !== homeworkId)
      }));
    }
  };

  const canEdit = user.role === 'admin' || user.role === 'professeur';
  const homeworks = getHomeworks();

  return (
    <div className="p-4 sm:p-6">
      <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <h2 className="text-xl sm:text-2xl font-semibold text-title">
          {user.role === 'parent' ? 'Devoirs de votre enfant' : user.role === 'eleve' ? 'Mes devoirs' : 'Gestion des devoirs'}
        </h2>
        {canEdit && (
          <button onClick={() => openModal()} className="flex items-center gap-2 btn-primary mt-4 sm:mt-0">
            <Plus size={16} />
            Nouveau devoir
          </button>
        )}
      </div>
      <div className="grid gap-4">
        {homeworks.map(homework => {
          const classe = data.classes.find(c => c.id === homework.classe_id);
          const professeur = data.users.find(u => u.id === homework.professeur_id);
          const isOverdue = new Date(homework.date_limite) < new Date();
          return (
            <div key={homework.id} className={`card ${isOverdue ? 'border-[var(--rouge-erreur)]' : 'border-[var(--vert-accent)]'}`}>
              <div className="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4">
                <div>
                  <h3 className="text-lg sm:text-xl font-semibold text-title">{homework.titre}</h3>
                  <div className="text-sm text-body mt-1 flex flex-col sm:flex-row sm:gap-4">
                    <span>Classe: {classe?.nom}</span>
                    {user.role !== 'professeur' && <span>Professeur: {professeur?.prenom} {professeur?.nom}</span>}
                  </div>
                </div>
                {canEdit && (
                  <div className="flex gap-2 mt-4 sm:mt-0">
                    <button onClick={() => openModal(homework)} className="text-[var(--bleu-principal)] hover:text-opacity-80">
                      <Edit size={16} />
                    </button>
                    <button onClick={() => handleDelete(homework.id)} className="text-[var(--rouge-erreur)] hover:text-opacity-80">
                      <Trash2 size={16} />
                    </button>
                  </div>
                )}
              </div>
              <p className="text-body mb-3 text-sm sm:text-base">{homework.description}</p>
              <div className={`inline-flex items-center px-3 py-1 rounded-full text-sm ${
                isOverdue ? 'bg-[var(--rouge-erreur)] text-[var(--blanc-pur)]' : 'bg-[var(--vert-clair)] text-[var(--vert-accent)]'
              }`}>
                À rendre le: {new Date(homework.date_limite).toLocaleDateString('fr-FR')}
                {isOverdue && ' (En retard)'}
              </div>
            </div>
          );
        })}
        {homeworks.length === 0 && <div className="text-center py-8 text-body">Aucun devoir</div>}
      </div>
      {canEdit && (
        <Modal
          isOpen={isModalOpen}
          onClose={() => setIsModalOpen(false)}
          title={editingHomework ? 'Modifier devoir' : 'Nouveau devoir'}
        >
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-body mb-1">Titre</label>
              <input
                type="text"
                value={formData.titre}
                onChange={(e) => setFormData({...formData, titre: e.target.value})}
                className="input"
                required
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-body mb-1">Description</label>
              <textarea
                value={formData.description}
                onChange={(e) => setFormData({...formData, description: e.target.value})}
                className="input h-32"
                required
              />
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-body mb-1">Date limite</label>
                <input
                  type="date"
                  value={formData.date_limite}
                  onChange={(e) => setFormData({...formData, date_limite: e.target.value})}
                  className="input"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-body mb-1">Classe</label>
                <select
                  value={formData.classe_id}
                  onChange={(e) => setFormData({...formData, classe_id: e.target.value})}
                  className="input"
                  required
                >
                  <option value="">Sélectionner une classe</option>
                  {data.classes.map(classe => (
                    <option key={classe.id} value={classe.id}>{classe.nom}</option>
                  ))}
                </select>
              </div>
            </div>
            <div className="flex gap-2 justify-end">
              <button onClick={() => setIsModalOpen(false)} className="px-4 py-2 border rounded hover:bg-[var(--bleu-clair)] text-body text-sm sm:text-base">
                Annuler
              </button>
              <button onClick={handleSubmit} className="btn-primary">
                {editingHomework ? 'Modifier' : 'Créer'}
              </button>
            </div>
          </div>
        </Modal>
      )}
    </div>
  );
};

export default HomeworkManagement;