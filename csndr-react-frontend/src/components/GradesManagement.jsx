import React, { useState, useEffect } from 'react';
import { Plus, BookOpen, Edit, Trash2, TrendingUp } from 'lucide-react';
import Modal from './Modal';
import RoleBadge from './RoleBadge';
import { getGrades, createGrade, updateGrade, deleteGrade, getUsers } from '../services/api';

const GradesManagement = ({ user }) => {
  const [grades, setGrades] = useState([]);
  const [students, setStudents] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingGrade, setEditingGrade] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    loadGrades();
    if (user.role === 'admin' || user.role === 'professeur') {
      loadStudents();
    }
  }, []);

  const loadGrades = async () => {
    try {
      setLoading(true);
      const response = await getGrades();
      setGrades(response.data);
    } catch (error) {
      setError('Erreur lors du chargement des notes');
      console.error('Erreur:', error);
    } finally {
      setLoading(false);
    }
  };

  const loadStudents = async () => {
    try {
      const response = await getUsers();
      // Filtrer seulement les élèves
      const studentsData = response.data.filter(u => u.role === 'eleve');
      setStudents(studentsData);
    } catch (error) {
      console.error('Erreur lors du chargement des élèves:', error);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const gradeData = {
      note: parseFloat(formData.get('note')),
      matiere: formData.get('matiere'),
      commentaire: formData.get('commentaire'),
      eleve_id: parseInt(formData.get('eleve_id'))
    };

    try {
      if (editingGrade) {
        await updateGrade(editingGrade.id, gradeData);
      } else {
        await createGrade(gradeData);
      }
      setIsModalOpen(false);
      setEditingGrade(null);
      loadGrades();
      e.target.reset();
    } catch (error) {
      setError('Erreur lors de la sauvegarde de la note');
      console.error('Erreur:', error);
    }
  };

  const handleEdit = (grade) => {
    setEditingGrade(grade);
    setIsModalOpen(true);
  };

  const handleDelete = async (gradeId) => {
    if (window.confirm('Êtes-vous sûr de vouloir supprimer cette note ?')) {
      try {
        await deleteGrade(gradeId);
        loadGrades();
      } catch (error) {
        setError('Erreur lors de la suppression de la note');
        console.error('Erreur:', error);
      }
    }
  };

  const getGradeColor = (note) => {
    if (note >= 16) return 'text-green-600 bg-green-100';
    if (note >= 12) return 'text-blue-600 bg-blue-100';
    if (note >= 8) return 'text-yellow-600 bg-yellow-100';
    return 'text-red-600 bg-red-100';
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
            <h2 className="text-2xl font-bold text-gray-900">Notes</h2>
            <p className="text-gray-600">Gestion des notes du Centre Scolaire</p>
          </div>
          {canEdit && (
            <button
              onClick={() => setIsModalOpen(true)}
              className="flex items-center gap-2 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors mt-4 sm:mt-0"
            >
              <Plus size={16} />
              Nouvelle note
            </button>
          )}
        </div>

        {error && (
          <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {error}
          </div>
        )}

        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          {grades.map((grade) => (
            <div key={grade.id} className="bg-white rounded-lg shadow-lg overflow-hidden">
              <div className="bg-gradient-to-r from-green-600 to-green-700 p-4">
                <div className="flex items-center justify-between">
                  <h3 className="text-lg font-semibold text-white">{grade.matiere}</h3>
                  <div className={`px-3 py-1 rounded-full text-white font-bold ${getGradeColor(grade.note).replace('text-', '').replace('bg-', 'bg-')}`}>
                    {grade.note}/20
                  </div>
                </div>
              </div>
              <div className="p-4">
                {grade.commentaire && (
                  <p className="text-gray-600 text-sm mb-4">{grade.commentaire}</p>
                )}
                <div className="flex items-center justify-between">
                  <div className="text-sm text-gray-500">
                    <span className="font-medium">Élève:</span> {grade.eleve?.prenom} {grade.eleve?.nom}
                  </div>
                  {canEdit && (
                    <div className="flex items-center gap-2">
                      <button
                        onClick={() => handleEdit(grade)}
                        className="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                      >
                        <Edit size={16} />
                      </button>
                      <button
                        onClick={() => handleDelete(grade.id)}
                        className="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      >
                        <Trash2 size={16} />
                      </button>
                    </div>
                  )}
                </div>
                {grade.professeur && (
                  <div className="mt-2 text-sm text-gray-500">
                    <span className="font-medium">Professeur:</span> {grade.professeur.prenom} {grade.professeur.nom}
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>

        {grades.length === 0 && !loading && (
          <div className="text-center py-12">
            <BookOpen size={48} className="mx-auto text-gray-300 mb-4" />
            <h3 className="text-lg font-medium text-gray-900 mb-2">Aucune note</h3>
            <p className="text-gray-500">
              {canEdit 
                ? 'Créez la première note en cliquant sur "Nouvelle note"'
                : 'Aucune note n\'a été créée pour le moment'
              }
            </p>
          </div>
        )}
      </div>

      {/* Modal pour créer/modifier une note */}
      <Modal isOpen={isModalOpen} onClose={() => {
        setIsModalOpen(false);
        setEditingGrade(null);
      }}>
        <div className="p-6">
          <h3 className="text-lg font-semibold mb-4">
            {editingGrade ? 'Modifier la note' : 'Nouvelle note'}
          </h3>
          <form onSubmit={handleSubmit} className="space-y-4">
            {user.role === 'admin' && (
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Élève
                </label>
                <select
                  name="eleve_id"
                  defaultValue={editingGrade?.eleve_id}
                  required
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
                  <option value="">Sélectionnez un élève</option>
                  {students.map((student) => (
                    <option key={student.id} value={student.id}>
                      {student.prenom} {student.nom}
                    </option>
                  ))}
                </select>
              </div>
            )}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Matière
              </label>
              <input
                type="text"
                name="matiere"
                defaultValue={editingGrade?.matiere}
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Nom de la matière"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Note (/20)
              </label>
              <input
                type="number"
                name="note"
                min="0"
                max="20"
                step="0.5"
                defaultValue={editingGrade?.note}
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Note sur 20"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Commentaire (optionnel)
              </label>
              <textarea
                name="commentaire"
                defaultValue={editingGrade?.commentaire}
                rows={3}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Commentaire sur la note"
              />
            </div>
            <div className="flex justify-end gap-2">
              <button
                type="button"
                onClick={() => {
                  setIsModalOpen(false);
                  setEditingGrade(null);
                }}
                className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Annuler
              </button>
              <button
                type="submit"
                className="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
              >
                {editingGrade ? 'Modifier' : 'Créer'}
              </button>
            </div>
          </form>
        </div>
      </Modal>
    </div>
  );
};

export default GradesManagement;