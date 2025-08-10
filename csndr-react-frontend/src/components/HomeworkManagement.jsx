import React, { useState, useEffect } from 'react';
import { Plus, BookOpen, Edit, Trash2, Calendar } from 'lucide-react';
import Modal from './Modal';
import RoleBadge from './RoleBadge';
import { getHomework, createHomework, updateHomework, deleteHomework, getClasses } from '../services/api';

const HomeworkManagement = ({ user }) => {
  const [homeworks, setHomeworks] = useState([]);
  const [classes, setClasses] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingHomework, setEditingHomework] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    loadHomeworks();
    if (user.role === 'admin' || user.role === 'professeur') {
      loadClasses();
    }
  }, []);

  const loadHomeworks = async () => {
    try {
      setLoading(true);
      const response = await getHomework();
      setHomeworks(response.data);
    } catch (error) {
      setError('Erreur lors du chargement des devoirs');
      console.error('Erreur:', error);
    } finally {
      setLoading(false);
    }
  };

  const loadClasses = async () => {
    try {
      const response = await getClasses();
      setClasses(response.data);
    } catch (error) {
      console.error('Erreur lors du chargement des classes:', error);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const homeworkData = {
      titre: formData.get('titre'),
      description: formData.get('description'),
      date_limite: formData.get('date_limite'),
      classe_id: parseInt(formData.get('classe_id'))
    };

    try {
      if (editingHomework) {
        await updateHomework(editingHomework.id, homeworkData);
      } else {
        await createHomework(homeworkData);
      }
      setIsModalOpen(false);
      setEditingHomework(null);
      loadHomeworks();
      e.target.reset();
    } catch (error) {
      setError('Erreur lors de la sauvegarde du devoir');
      console.error('Erreur:', error);
    }
  };

  const handleEdit = (homework) => {
    setEditingHomework(homework);
    setIsModalOpen(true);
  };

  const handleDelete = async (homeworkId) => {
    if (window.confirm('Êtes-vous sûr de vouloir supprimer ce devoir ?')) {
      try {
        await deleteHomework(homeworkId);
        loadHomeworks();
      } catch (error) {
        setError('Erreur lors de la suppression du devoir');
        console.error('Erreur:', error);
      }
    }
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  const canEdit = user.role === 'admin' || user.role === 'professeur';

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  return (
    <div className="p-6 bg-gray-50 min-h-screen">
      <div className="max-w-7xl mx-auto">
        <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
          <div>
            <h2 className="text-2xl font-bold text-gray-900">Devoirs</h2>
            <p className="text-gray-600">Gestion des devoirs du Centre Scolaire</p>
          </div>
          {canEdit && (
            <button
              onClick={() => setIsModalOpen(true)}
              className="flex items-center gap-2 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors mt-4 sm:mt-0"
            >
              <Plus size={16} />
              Nouveau devoir
            </button>
          )}
        </div>

        {error && (
          <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {error}
          </div>
        )}

        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          {homeworks.map((homework) => (
            <div key={homework.id} className="bg-white rounded-lg shadow-lg overflow-hidden">
              <div className="bg-gradient-to-r from-blue-600 to-blue-700 p-4">
                <div className="flex items-center justify-between">
                  <h3 className="text-lg font-semibold text-white">{homework.titre}</h3>
                  <div className="flex items-center gap-2">
                    <Calendar size={16} className="text-white/80" />
                    <span className="text-sm text-white/80">{formatDate(homework.date_limite)}</span>
                  </div>
                </div>
              </div>
              <div className="p-4">
                <p className="text-gray-600 text-sm mb-4">{homework.description}</p>
                <div className="flex items-center justify-between">
                  <div className="text-sm text-gray-500">
                    <span className="font-medium">Classe:</span> {homework.classe?.nom}
                  </div>
                  {canEdit && (
                    <div className="flex items-center gap-2">
                      <button
                        onClick={() => handleEdit(homework)}
                        className="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                      >
                        <Edit size={16} />
                      </button>
                      <button
                        onClick={() => handleDelete(homework.id)}
                        className="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      >
                        <Trash2 size={16} />
                      </button>
                    </div>
                  )}
                </div>
                {homework.professeur && (
                  <div className="mt-2 text-sm text-gray-500">
                    <span className="font-medium">Professeur:</span> {homework.professeur.prenom} {homework.professeur.nom}
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>

        {homeworks.length === 0 && !loading && (
          <div className="text-center py-12">
            <BookOpen size={48} className="mx-auto text-gray-300 mb-4" />
            <h3 className="text-lg font-medium text-gray-900 mb-2">Aucun devoir</h3>
            <p className="text-gray-500">
              {canEdit 
                ? 'Créez le premier devoir en cliquant sur "Nouveau devoir"'
                : 'Aucun devoir n\'a été créé pour le moment'
              }
            </p>
          </div>
        )}
      </div>

      {/* Modal pour créer/modifier un devoir */}
      <Modal isOpen={isModalOpen} onClose={() => {
        setIsModalOpen(false);
        setEditingHomework(null);
      }}>
        <div className="p-6">
          <h3 className="text-lg font-semibold mb-4">
            {editingHomework ? 'Modifier le devoir' : 'Nouveau devoir'}
          </h3>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Titre
              </label>
              <input
                type="text"
                name="titre"
                defaultValue={editingHomework?.titre}
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Titre du devoir"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Description
              </label>
              <textarea
                name="description"
                defaultValue={editingHomework?.description}
                required
                rows={4}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Description du devoir"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Classe
              </label>
              <select
                name="classe_id"
                defaultValue={editingHomework?.classe_id}
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              >
                <option value="">Sélectionnez une classe</option>
                {classes.map((classe) => (
                  <option key={classe.id} value={classe.id}>
                    {classe.nom}
                  </option>
                ))}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Date limite
              </label>
              <input
                type="date"
                name="date_limite"
                defaultValue={editingHomework?.date_limite?.split('T')[0]}
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
            </div>
            <div className="flex justify-end gap-2">
              <button
                type="button"
                onClick={() => {
                  setIsModalOpen(false);
                  setEditingHomework(null);
                }}
                className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Annuler
              </button>
              <button
                type="submit"
                className="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
              >
                {editingHomework ? 'Modifier' : 'Créer'}
              </button>
            </div>
          </form>
        </div>
      </Modal>
    </div>
  );
};

export default HomeworkManagement;