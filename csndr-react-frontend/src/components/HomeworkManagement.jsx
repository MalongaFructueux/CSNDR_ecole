import React, { useState, useEffect } from 'react';
import { Plus, BookOpen, Edit, Trash2, Calendar, Download, Loader2 } from 'lucide-react';
import Modal from './Modal';
import RoleBadge from './RoleBadge';
import { getHomework, createHomework, updateHomework, deleteHomework, getClasses, getParentChildren } from '../services/api';
import api from '../services/api';

const HomeworkManagement = ({ user }) => {
  const [homeworks, setHomeworks] = useState([]);
  const [classes, setClasses] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingHomework, setEditingHomework] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [downloadingIds, setDownloadingIds] = useState({});
  const [success, setSuccess] = useState(null);
  const [children, setChildren] = useState([]);
  const [selectedChildId, setSelectedChildId] = useState('');

  useEffect(() => {
    loadHomeworks();
    if (user.role === 'admin' || user.role === 'professeur') {
      loadClasses();
    }
    if (user.role === 'parent') {
      loadChildren();
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

  const loadChildren = async () => {
    try {
      const response = await getParentChildren(user.id);
      setChildren(response.data || []);
    } catch (error) {
      console.error('Erreur lors du chargement des enfants du parent:', error);
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
    
    // Récupérer le fichier uploadé
    const fichier = formData.get('fichier');
    
    const homeworkData = new FormData();
    homeworkData.append('titre', formData.get('titre'));
    homeworkData.append('description', formData.get('description'));
    homeworkData.append('date_limite', formData.get('date_limite'));
    homeworkData.append('classe_id', formData.get('classe_id'));
    
    // Debug: Afficher les données envoyées
    console.log('Données du devoir:', {
      titre: formData.get('titre'),
      description: formData.get('description'),
      date_limite: formData.get('date_limite'),
      classe_id: formData.get('classe_id'),
      fichier: fichier ? `${fichier.name} (${fichier.size} bytes)` : 'Aucun fichier'
    });
    
    // Ajouter le fichier s'il existe
    if (fichier && fichier.size > 0) {
      homeworkData.append('fichier', fichier);
    }

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
      console.error('Erreur complète:', error);
      console.error('Réponse du serveur:', error.response?.data);
      
      // Gestion spécifique des erreurs de validation (422)
      if (error.response?.status === 422) {
        const serverResponse = error.response.data;
        
        if (serverResponse.errors) {
          // Afficher les erreurs de validation spécifiques
          const errorMessages = [];
          
          Object.entries(serverResponse.errors).forEach(([field, messages]) => {
            if (Array.isArray(messages)) {
              errorMessages.push(...messages);
            } else {
              errorMessages.push(messages);
            }
          });
          
          setError(`Erreurs de validation :\n• ${errorMessages.join('\n• ')}`);
        } else if (serverResponse.message) {
          setError(`Erreur de validation : ${serverResponse.message}`);
        } else {
          setError('Erreur de validation. Vérifiez que tous les champs sont correctement remplis.');
        }
      } else if (error.response?.status === 403) {
        setError('Accès refusé. Vous n\'avez pas les permissions pour créer un devoir.');
      } else {
        setError('Erreur lors de la sauvegarde du devoir. Veuillez réessayer.');
      }
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

  // Téléchargement authentifié du fichier joint d'un devoir
  const handleDownload = async (homework) => {
    try {
      setDownloadingIds((prev) => ({ ...prev, [homework.id]: true }));
      // Téléchargement avec retries en cas d'erreurs réseau temporaires
      const maxRetries = 2;
      let attempt = 0;
      let response;
      while (true) {
        try {
          response = await api.get(`/homework/${homework.id}/download`, {
            responseType: 'blob',
          });
          break; // succès
        } catch (err) {
          const status = err.response?.status;
          const isNetwork = !err.response || status === 0;
          const isTransient = isNetwork || [502, 503, 504].includes(status);
          if (attempt < maxRetries && isTransient) {
            await new Promise((res) => setTimeout(res, 800 * (attempt + 1)));
            attempt += 1;
            continue;
          }
          throw err; // relancer si non transitoire ou plus de retries
        }
      }

      // Récupérer le nom de fichier depuis les headers si disponible
      const contentDisposition = response.headers?.['content-disposition'] || response.headers?.get?.('content-disposition');
      let filename = homework.nom_fichier_original || 'fichier';
      if (contentDisposition) {
        const match = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/i.exec(contentDisposition);
        if (match && match[1]) {
          filename = match[1].replace(/['"]/g, '');
          try { filename = decodeURIComponent(filename); } catch (_) {}
        }
      }

      const mimeType = response.headers?.['content-type'] || 'application/octet-stream';
      const blob = new Blob([response.data], { type: mimeType });

      // Support IE/Edge legacy
      if (window.navigator && window.navigator.msSaveOrOpenBlob) {
        window.navigator.msSaveOrOpenBlob(blob, filename);
        setSuccess(`Téléchargement démarré: ${filename}`);
        setTimeout(() => setSuccess(null), 4000);
        return;
      }

      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      a.remove();
      window.URL.revokeObjectURL(url);
      setSuccess(`Téléchargement démarré: ${filename}`);
      setTimeout(() => setSuccess(null), 4000);
    } catch (error) {
      console.error('Erreur lors du téléchargement du fichier:', error);
      const status = error.response?.status;
      if (status === 404) {
        setError('Fichier non trouvé. Il a peut-être été supprimé.');
      } else if (status === 403) {
        setError("Accès refusé au téléchargement de ce fichier.");
      } else {
        setError("Impossible de télécharger le fichier du devoir. Vérifiez votre connexion et réessayez.");
      }
    } finally {
      setDownloadingIds((prev) => {
        const copy = { ...prev };
        delete copy[homework.id];
        return copy;
      });
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

  // Appliquer le filtre par enfant pour les parents: filtre par classe de l'enfant
  const displayedHomeworks = (() => {
    if (user.role !== 'parent') return homeworks;
    if (!selectedChildId) return homeworks;
    const child = children.find(c => String(c.id) === String(selectedChildId));
    if (!child) return homeworks;
    return homeworks.filter(hw => String(hw.classe_id) === String(child.classe_id));
  })();

  return (
    <div className="p-6 bg-gray-50 min-h-screen">
      <div className="max-w-7xl mx-auto">
        <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
          <div>
            <h2 className="text-2xl font-bold text-gray-900">Devoirs</h2>
            <p className="text-gray-600">Gestion des devoirs du Centre Scolaire</p>
          </div>
          <div className="flex items-center gap-3 mt-4 sm:mt-0">
            {user.role === 'parent' && children.length > 0 && (
              <select
                value={selectedChildId}
                onChange={(e) => setSelectedChildId(e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              >
                <option value="">Tous les enfants</option>
                {children.map((child) => (
                  <option key={child.id} value={child.id}>
                    {child.prenom} {child.nom}
                  </option>
                ))}
              </select>
            )}
            {canEdit && (
              <button
                onClick={() => setIsModalOpen(true)}
                className="flex items-center gap-2 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors"
              >
                <Plus size={16} />
                Nouveau devoir
              </button>
            )}
          </div>
        </div>

        {error && (
          <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {error}
          </div>
        )}
        {success && (
          <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {success}
          </div>
        )}

        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          {displayedHomeworks.map((homework) => (
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
                {homework.fichier_attachment && (
                  <div className="mt-3 p-2 bg-blue-50 border border-blue-200 rounded-lg">
                    <div className="flex items-center gap-2">
                      <Download size={16} className="text-blue-600" />
                      <span className="text-sm font-medium text-blue-800">
                        Fichier joint : {homework.nom_fichier_original}
                      </span>
                    </div>
                    <button
                      onClick={() => handleDownload(homework)}
                      disabled={!!downloadingIds[homework.id]}
                      className={`mt-1 text-xs underline ${downloadingIds[homework.id] ? 'text-gray-400 cursor-not-allowed' : 'text-blue-600 hover:text-blue-800'}`}
                      aria-busy={!!downloadingIds[homework.id]}
                    >
                      {downloadingIds[homework.id] ? (
                        <span className="inline-flex items-center gap-1">
                          <Loader2 className="h-3 w-3 animate-spin" />
                          Téléchargement…
                        </span>
                      ) : (
                        'Télécharger'
                      )}
                    </button>
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
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Fichier joint (optionnel)
              </label>
              <input
                type="file"
                name="fichier"
                accept=".pdf,.doc,.docx,.txt,.rtf"
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
              />
              <p className="text-xs text-gray-500 mt-1">
                Formats acceptés : PDF, DOC, DOCX, TXT, RTF (max 10MB)
              </p>
              {editingHomework?.fichier_attachment && (
                <div className="mt-2 p-2 bg-gray-50 rounded border">
                  <p className="text-sm text-gray-600">
                    Fichier actuel : {editingHomework.nom_fichier_original}
                  </p>
                </div>
              )}
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