import React, { useState } from 'react';
import { Plus, Edit, Trash2, Calendar } from 'lucide-react';
import Modal from './Modal';

const EventsManagement = ({ data, setData, user }) => {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingEvent, setEditingEvent] = useState(null);
  const [formData, setFormData] = useState({
    titre: '', description: '', date: ''
  });

  // Ouvre le modal
  const openModal = (event = null) => {
    setEditingEvent(event);
    setFormData(event || { titre: '', description: '', date: '' });
    setIsModalOpen(true);
  };

  // Gestion du formulaire
  const handleSubmit = (e) => {
    e.preventDefault();
    setData(prev => ({
      ...prev,
      evenements: editingEvent
        ? prev.evenements.map(event => event.id === editingEvent.id ? { ...formData, id: event.id } : event)
        : [...prev.evenements, { ...formData, id: Date.now(), auteur_id: user.id }]
    }));
    setIsModalOpen(false);
  };

  // Suppression d'un événement
  const handleDelete = (eventId) => {
    if (window.confirm('Supprimer cet événement ?')) {
      setData(prev => ({
        ...prev,
        evenements: prev.evenements.filter(event => event.id !== eventId)
      }));
    }
  };

  const canEdit = user.role === 'admin' || user.role === 'professeur';
  const sortedEvents = [...data.evenements].sort((a, b) => new Date(a.date) - new Date(b.date));

  return (
    <div className="p-4 sm:p-6">
      <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <h2 className="text-xl sm:text-2xl font-semibold text-title">Événements</h2>
        {canEdit && (
          <button onClick={() => openModal()} className="flex items-center gap-2 btn-primary mt-4 sm:mt-0">
            <Plus size={16} />
            Nouvel événement
          </button>
        )}
      </div>
      <div className="grid gap-4">
        {sortedEvents.map(event => {
          const auteur = data.users.find(u => u.id === event.auteur_id);
          const eventDate = new Date(event.date);
          const isPast = eventDate < new Date();
          const isToday = eventDate.toDateString() === new Date().toDateString();
          return (
            <div key={event.id} className={`card ${isToday ? 'border-[var(--bleu-principal)]' : isPast ? 'opacity-60' : 'border-[var(--vert-accent)]'}`}>
              <div className="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-4">
                <div>
                  <h3 className="text-lg sm:text-xl font-semibold text-title">{event.titre}</h3>
                  <div className="text-sm text-body mt-1">Par: {auteur?.prenom} {auteur?.nom}</div>
                </div>
                {canEdit && !isPast && (
                  <div className="flex gap-2 mt-4 sm:mt-0">
                    <button onClick={() => openModal(event)} className="text-[var(--bleu-principal)] hover:text-opacity-80">
                      <Edit size={16} />
                    </button>
                    <button onClick={() => handleDelete(event.id)} className="text-[var(--rouge-erreur)] hover:text-opacity-80">
                      <Trash2 size={16} />
                    </button>
                  </div>
                )}
              </div>
              <p className="text-body mb-3 text-sm sm:text-base">{event.description}</p>
              <div className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${
                isToday ? 'bg-[var(--bleu-clair)] text-[var(--bleu-principal)]' :
                isPast ? 'bg-[var(--gris-neutre)] text-[var(--blanc-pur)]' :
                'bg-[var(--vert-clair)] text-[var(--vert-accent)]'
              }`}>
                <Calendar size={16} className="mr-1" />
                {eventDate.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
                {isToday && ' - Aujourd\'hui'}
              </div>
            </div>
          );
        })}
        {sortedEvents.length === 0 && <div className="text-center py-8 text-body">Aucun événement</div>}
      </div>
      {canEdit && (
        <Modal
          isOpen={isModalOpen}
          onClose={() => setIsModalOpen(false)}
          title={editingEvent ? 'Modifier événement' : 'Nouvel événement'}
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
            <div>
              <label className="block text-sm font-medium text-body mb-1">Date</label>
              <input
                type="date"
                value={formData.date}
                onChange={(e) => setFormData({...formData, date: e.target.value})}
                className="input"
                required
              />
            </div>
            <div className="flex gap-2 justify-end">
              <button onClick={() => setIsModalOpen(false)} className="px-4 py-2 border rounded hover:bg-[var(--bleu-clair)] text-body text-sm sm:text-base">
                Annuler
              </button>
              <button onClick={handleSubmit} className="btn-primary">
                {editingEvent ? 'Modifier' : 'Créer'}
              </button>
            </div>
          </div>
        </Modal>
      )}
    </div>
  );
};

export default EventsManagement;