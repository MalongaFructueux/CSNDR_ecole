import React, { useState } from 'react';
import { Plus, Edit, Trash2 } from 'lucide-react';
import Modal from './Modal';

const GradesManagement = ({ data, setData, user }) => {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingGrade, setEditingGrade] = useState(null);
  const [formData, setFormData] = useState({
    eleve_id: '', matiere: '', note: '', commentaire: ''
  });

  // Filtrer les notes selon rôle
  const getNotes = () => {
    if (user.role === 'admin') return data.notes;
    if (user.role === 'professeur') return data.notes.filter(n => n.professeur_id === user.id);
    if (user.role === 'eleve') return data.notes.filter(n => n.eleve_id === user.id);
    if (user.role === 'parent') {
      const enfant = data.users.find(u => u.parent_id === user.id);
      return enfant ? data.notes.filter(n => n.eleve_id === enfant.id) : [];
    }
    return [];
  };

  // Ouvre le modal
  const openModal = (grade = null) => {
    setEditingGrade(grade);
    setFormData(grade || { eleve_id: '', matiere: '', note: '', commentaire: '' });
    setIsModalOpen(true);
  };

  // Gestion du formulaire
  const handleSubmit = (e) => {
    e.preventDefault();
    setData(prev => ({
      ...prev,
      notes: editingGrade
        ? prev.notes.map(grade => grade.id === editingGrade.id ? { ...formData, id: grade.id } : grade)
        : [...prev.notes, { ...formData, id: Date.now(), professeur_id: user.id, note: parseFloat(formData.note), date: new Date().toISOString().split('T')[0] }]
    }));
    setIsModalOpen(false);
  };

  // Suppression d'une note
  const handleDelete = (gradeId) => {
    if (window.confirm('Supprimer cette note ?')) {
      setData(prev => ({
        ...prev,
        notes: prev.notes.filter(grade => grade.id !== gradeId)
      }));
    }
  };

  const canEdit = user.role === 'admin' || user.role === 'professeur';
  const notes = getNotes();

  // Regrouper par élève
  const notesByStudent = notes.reduce((acc, note) => {
    const student = data.users.find(u => u.id === note.eleve_id);
    if (!student) return acc;
    if (!acc[student.id]) acc[student.id] = { student, notes: [] };
    acc[student.id].notes.push(note);
    return acc;
  }, {});

  return (
    <div className="p-4 sm:p-6">
      <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <h2 className="text-xl sm:text-2xl font-semibold text-title">
          {user.role === 'parent' ? 'Notes de votre enfant' : user.role === 'eleve' ? 'Mes notes' : 'Gestion des notes'}
        </h2>
        {canEdit && (
          <button onClick={() => openModal()} className="flex items-center gap-2 btn-primary mt-4 sm:mt-0">
            <Plus size={16} />
            Nouvelle note
          </button>
        )}
      </div>
      <div className="space-y-6">
        {Object.values(notesByStudent).map(({ student, notes }) => {
          const moyenne = notes.length > 0 ? (notes.reduce((sum, note) => sum + note.note, 0) / notes.length).toFixed(2) : 0;
          return (
            <div key={student.id} className="card border-[var(--bleu-principal)]">
              <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4">
                <h3 className="text-lg sm:text-xl font-semibold text-title">{student.prenom} {student.nom}</h3>
                <div className="text-lg font-semibold text-[var(--bleu-principal)] mt-2 sm:mt-0">Moyenne: {moyenne}/20</div>
              </div>
              <div className="overflow-x-auto">
                <table className="w-full responsive-table">
                  <thead className="bg-[var(--bleu-clair)]">
                    <tr>
                      <th className="px-4 py-2 text-left text-title">Matière</th>
                      <th className="px-4 py-2 text-left text-title">Note</th>
                      <th className="px-4 py-2 text-left text-title">Date</th>
                      {canEdit && <th className="px-4 py-2 text-left text-title">Actions</th>}
                    </tr>
                  </thead>
                  <tbody>
                    {notes.map(note => (
                      <tr key={note.id} className="border-t">
                        <td data-label="Matière" className="px-4 py-2 text-body">{note.matiere}</td>
                        <td data-label="Note" className="px-4 py-2">
                          <span className={`px-2 py-1 rounded text-sm font-medium ${
                            note.note >= 16 ? 'bg-[var(--vert-clair)] text-[var(--vert-accent)]' :
                            note.note >= 12 ? 'bg-[var(--bleu-clair)] text-[var(--bleu-principal)]' :
                            note.note >= 8 ? 'bg-[var(--orange-clair)] text-[var(--orange-secondaire)]' :
                            'bg-[var(--rouge-erreur)] text-[var(--blanc-pur)]'
                          }`}>
                            {note.note}/20
                          </span>
                        </td>
                        <td data-label="Date" className="px-4 py-2 text-body">{new Date(note.date).toLocaleDateString('fr-FR')}</td>
                        {canEdit && (
                          <td data-label="Actions" className="px-4 py-2">
                            <div className="flex gap-2">
                              <button onClick={() => openModal(note)} className="text-[var(--bleu-principal)] hover:text-opacity-80">
                                <Edit size={16} />
                              </button>
                              <button onClick={() => handleDelete(note.id)} className="text-[var(--rouge-erreur)] hover:text-opacity-80">
                                <Trash2 size={16} />
                              </button>
                            </div>
                          </td>
                        )}
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          );
        })}
        {Object.keys(notesByStudent).length === 0 && <div className="text-center py-8 text-body">Aucune note</div>}
      </div>
      {canEdit && (
        <Modal
          isOpen={isModalOpen}
          onClose={() => setIsModalOpen(false)}
          title={editingGrade ? 'Modifier note' : 'Nouvelle note'}
        >
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-body mb-1">Élève</label>
              <select
                value={formData.eleve_id}
                onChange={(e) => setFormData({...formData, eleve_id: parseInt(e.target.value)})}
                className="input"
                required
              >
                <option value="">Sélectionner un élève</option>
                {data.users
                  .filter(u => u.role === 'eleve' && (user.role === 'admin' || u.classe_id === user.classe_id))
                  .map(student => (
                    <option key={student.id} value={student.id}>{student.prenom} {student.nom}</option>
                  ))}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-body mb-1">Matière</label>
              <input
                type="text"
                value={formData.matiere}
                onChange={(e) => setFormData({...formData, matiere: e.target.value})}
                className="input"
                placeholder="ex: Mathématiques"
                required
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-body mb-1">Note (sur 20)</label>
              <input
                type="number"
                min="0"
                max="20"
                step="0.5"
                value={formData.note}
                onChange={(e) => setFormData({...formData, note: e.target.value})}
                className="input"
                required
              />
            </div>
            <div className="flex gap-2 justify-end">
              <button onClick={() => setIsModalOpen(false)} className="px-4 py-2 border rounded hover:bg-[var(--bleu-clair)] text-body text-sm sm:text-base">
                Annuler
              </button>
              <button onClick={handleSubmit} className="btn-primary">
                {editingGrade ? 'Modifier' : 'Créer'}
              </button>
            </div>
          </div>
        </Modal>
      )}
    </div>
  );
};

export default GradesManagement;