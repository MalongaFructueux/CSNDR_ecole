import React, { useState, useEffect } from 'react';
import { Plus, Calendar, Edit, Trash2, Eye } from 'lucide-react';
import Modal from './Modal';
import { getEvents, createEvent, updateEvent, deleteEvent } from '../services/api';

/**
 * Composant EventsManagement - Gestion des événements du Centre Scolaire
 * 
 * Ce composant gère :
 * - L'affichage de tous les événements (visible par tous les utilisateurs)
 * - La création d'événements (Admin uniquement)
 * - La modification d'événements (Admin uniquement)
 * - La suppression d'événements (Admin uniquement)
 * 
 * Fonctionnalités principales :
 * - Interface moderne avec design responsive
 * - Restrictions d'accès selon les rôles
 * - Gestion des formulaires avec validation
 * - Affichage des événements avec dates formatées
 * - Modal pour création/modification
 * 
 * @param {Object} user - Objet utilisateur connecté
 */
const EventsManagement = ({ user }) => {
  // États locaux pour la gestion des données
  const [events, setEvents] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingEvent, setEditingEvent] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  /**
   * Chargement initial des événements
   * Exécuté au montage du composant
   */
  useEffect(() => {
    loadEvents();
  }, []);

  /**
   * Charge tous les événements depuis l'API
   * Les événements sont visibles par tous les utilisateurs
   */
  const loadEvents = async () => {
    try {
      setLoading(true);
      const response = await getEvents();
      setEvents(response.data);
    } catch (error) {
      setError('Erreur lors du chargement des événements');
      console.error('Erreur:', error);
    } finally {
      setLoading(false);
    }
  };

  /**
   * Gère la soumission du formulaire (création ou modification)
   * Valide les données et envoie la requête à l'API
   * @param {Event} e - Événement du formulaire
   */
  const handleSubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const eventData = {
      titre: formData.get('titre'),
      description: formData.get('description'),
      date: formData.get('date')
    };

    try {
      if (editingEvent) {
        // Modification d'un événement existant
        await updateEvent(editingEvent.id, eventData);
      } else {
        // Création d'un nouvel événement
        await createEvent(eventData);
      }
      setIsModalOpen(false);
      setEditingEvent(null);
      loadEvents();
      e.target.reset();
    } catch (error) {
      setError('Erreur lors de la sauvegarde de l\'événement');
      console.error('Erreur:', error);
    }
  };

  /**
   * Ouvre le modal en mode édition pour un événement spécifique
   * @param {Object} event - Événement à modifier
   */
  const handleEdit = (event) => {
    setEditingEvent(event);
    setIsModalOpen(true);
  };

  /**
   * Supprime un événement après confirmation
   * @param {number} eventId - ID de l'événement à supprimer
   */
  const handleDelete = async (eventId) => {
    if (window.confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
      try {
        await deleteEvent(eventId);
        loadEvents();
      } catch (error) {
        setError('Erreur lors de la suppression de l\'événement');
        console.error('Erreur:', error);
      }
    }
  };

  /**
   * Formate une date en format français lisible
   * @param {string} dateString - Date au format ISO
   * @returns {string} Date formatée
   */
  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  // Affichage du loader pendant le chargement
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
        {/* Header avec titre et bouton nouveau événement (Admin uniquement) */}
        <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
          <div>
            <h2 className="text-2xl font-bold text-gray-900">Événements</h2>
            <p className="text-gray-600">Gestion des événements du Centre Scolaire</p>
          </div>
          {/* Bouton nouveau événement visible uniquement pour les admins */}
          {user.role === 'admin' && (
            <button
              onClick={() => setIsModalOpen(true)}
              className="flex items-center gap-2 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors mt-4 sm:mt-0"
            >
              <Plus size={16} />
              Nouvel événement
            </button>
          )}
        </div>

        {/* Affichage des erreurs */}
        {error && (
          <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {error}
          </div>
        )}

        {/* Grille des événements */}
        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          {events.map((event) => (
            <div key={event.id} className="bg-white rounded-lg shadow-lg overflow-hidden">
              {/* Header de l'événement avec titre et date */}
              <div className="bg-gradient-to-r from-primary-600 to-primary-700 p-4">
                <div className="flex items-center justify-between">
                  <h3 className="text-lg font-semibold text-white">{event.titre}</h3>
                  <div className="flex items-center gap-2">
                    <Calendar size={16} className="text-white/80" />
                    <span className="text-sm text-white/80">{formatDate(event.date)}</span>
                  </div>
                </div>
              </div>
              
              {/* Contenu de l'événement */}
              <div className="p-4">
                <p className="text-gray-600 text-sm mb-4">{event.description}</p>
                <div className="flex items-center justify-between">
                  <div className="text-sm text-gray-500">
                    <span className="font-medium">Créé par:</span> {event.auteur?.prenom} {event.auteur?.nom}
                  </div>
                  {/* Actions (Admin uniquement) */}
                  {user.role === 'admin' && (
                    <div className="flex items-center gap-2">
                      <button
                        onClick={() => handleEdit(event)}
                        className="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                        title="Modifier l'événement"
                      >
                        <Edit size={16} />
                      </button>
                      <button
                        onClick={() => handleDelete(event.id)}
                        className="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                        title="Supprimer l'événement"
                      >
                        <Trash2 size={16} />
                      </button>
                    </div>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Message si aucun événement */}
        {events.length === 0 && !loading && (
          <div className="text-center py-12">
            <Calendar size={48} className="mx-auto text-gray-300 mb-4" />
            <h3 className="text-lg font-medium text-gray-900 mb-2">Aucun événement</h3>
            <p className="text-gray-500">
              {user.role === 'admin' 
                ? 'Créez le premier événement en cliquant sur "Nouvel événement"'
                : 'Aucun événement n\'a été créé pour le moment'
              }
            </p>
          </div>
        )}
      </div>

      {/* Modal pour créer/modifier un événement */}
      <Modal isOpen={isModalOpen} onClose={() => {
        setIsModalOpen(false);
        setEditingEvent(null);
      }}>
        <div className="p-6">
          <h3 className="text-lg font-semibold mb-4">
            {editingEvent ? 'Modifier l\'événement' : 'Nouvel événement'}
          </h3>
          <form onSubmit={handleSubmit} className="space-y-4">
            {/* Champ titre */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Titre
              </label>
              <input
                type="text"
                name="titre"
                defaultValue={editingEvent?.titre}
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Titre de l'événement"
              />
            </div>
            
            {/* Champ description */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Description
              </label>
              <textarea
                name="description"
                defaultValue={editingEvent?.description}
                required
                rows={4}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Description de l'événement"
              />
            </div>
            
            {/* Champ date */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Date
              </label>
              <input
                type="date"
                name="date"
                defaultValue={editingEvent?.date?.split('T')[0]}
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
            </div>
            
            {/* Boutons d'action */}
            <div className="flex justify-end gap-2">
              <button
                type="button"
                onClick={() => {
                  setIsModalOpen(false);
                  setEditingEvent(null);
                }}
                className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Annuler
              </button>
              <button
                type="submit"
                className="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
              >
                {editingEvent ? 'Modifier' : 'Créer'}
              </button>
            </div>
          </form>
        </div>
      </Modal>
    </div>
  );
};

export default EventsManagement;