// Client API basé sur fetch (sessions via cookies)
const BASE_URL = (typeof process !== 'undefined' && process.env && process.env.REACT_APP_API_URL)
  ? process.env.REACT_APP_API_URL
  : 'http://127.0.0.1:8000/api';

const buildHeaders = (body) => {
  const headers = { Accept: 'application/json' };
  if (body && !(body instanceof FormData)) {
    headers['Content-Type'] = 'application/json';
  }
  return headers;
};

async function request(method, path, body) {
  const isForm = body instanceof FormData;
  const options = {
    method,
    credentials: 'include',
    headers: buildHeaders(body),
    body: body ? (isForm ? body : JSON.stringify(body)) : undefined,
  };

  let response;
  try {
    response = await fetch(`${BASE_URL}${path}`, options);
  } catch (networkErr) {
    // Harmoniser avec Axios: lever une erreur avec code et message
    const error = new Error('Network Error');
    error.code = 'ERR_NETWORK';
    throw error;
  }

  const contentType = response.headers.get('content-type') || '';
  const isJson = contentType.includes('application/json');
  const data = isJson ? await response.json().catch(() => null) : await response.text();

  if (!response.ok) {
    // Gestion similaire à l'intercepteur Axios
    if (response.status === 401) {
      // Rediriger vers /login pour les requêtes protégées
      if (typeof window !== 'undefined') window.location.href = '/login';
    }
    const error = new Error((data && data.message) || `HTTP ${response.status}`);
    error.response = { status: response.status, data };
    throw error;
  }

  // Retourner un objet type Axios { data, status, headers }
  return {
    data,
    status: response.status,
    headers: response.headers,
  };
}

const api = {
  get: (path) => request('GET', path),
  post: (path, body) => request('POST', path, body),
  put: (path, body) => request('PUT', path, body),
  delete: (path) => request('DELETE', path),
};

// ============================================================================
// ROUTES D'AUTHENTIFICATION
// ============================================================================

/**
 * Authentification d'un utilisateur
 * @param {Object} credentials - Email et mot de passe
 * @returns {Promise} - Token JWT et informations utilisateur
 */
export const login = (credentials) => api.post('/auth/login', credentials);

/**
 * Déconnexion d'un utilisateur
 * @returns {Promise} - Confirmation de déconnexion
 */
export const logout = () => api.post('/auth/logout');

// ============================================================================
// ROUTES DE GESTION DES UTILISATEURS
// ============================================================================

/**
 * Récupération de tous les utilisateurs
 * @returns {Promise} - Liste des utilisateurs
 */
export const getUsers = () => api.get('/users');

/**
 * Recherche des utilisateurs par nom ou email
 * @param {string} query - Terme de recherche
 * @returns {Promise} - Liste des utilisateurs correspondants
 */
export const searchUsers = (query) => api.get(`/users/search?query=${encodeURIComponent(query)}`);

/**
 * Création d'un nouvel utilisateur
 * @param {Object} userData - Données de l'utilisateur
 * @returns {Promise} - Utilisateur créé
 */
export const createUser = (userData) => api.post('/users', userData);

/**
 * Suppression d'un utilisateur
 * @param {number} id - ID de l'utilisateur
 * @returns {Promise} - Confirmation de suppression
 */
export const deleteUser = (id) => api.delete(`/users/${id}`);

/**
 * Modification d'un utilisateur existant
 * @param {number} id - ID de l'utilisateur
 * @param {Object} userData - Données mises à jour
 * @returns {Promise} - Utilisateur modifié
 */
export const updateUser = (id, userData) => api.put(`/users/${id}`, userData);

/**
 * Récupération des enfants d'un parent (auth parent lui-même ou admin)
 * @param {number} parentId - ID du parent
 * @returns {Promise} - Liste des enfants ({id, nom, prenom, email, classe_id, created_at})
 */
export const getParentChildren = (parentId) => api.get(`/users/parent/${parentId}/children`);

// ============================================================================
// ROUTES DE GESTION DES CLASSES
// ============================================================================

/**
 * Récupération de toutes les classes
 * @returns {Promise} - Liste des classes
 */
export const getClasses = () => api.get('/classes');

/**
 * Création d'une nouvelle classe
 * @param {Object} classData - Données de la classe
 * @returns {Promise} - Classe créée
 */
export const createClass = (classData) => api.post('/classes', classData);

/**
 * Suppression d'une classe
 * @param {number} id - ID de la classe
 * @returns {Promise} - Confirmation de suppression
 */
export const deleteClass = (id) => api.delete(`/classes/${id}`);

/**
 * Modification d'une classe existante
 * @param {number} id - ID de la classe
 * @param {Object} classData - Données mises à jour
 * @returns {Promise} - Classe modifiée
 */
export const updateClass = (id, classData) => api.put(`/classes/${id}`, classData);

// ============================================================================
// ROUTES DE GESTION DES MESSAGES
// ============================================================================

/**
 * Récupération de tous les messages de l'utilisateur connecté
 * @returns {Promise} - Messages envoyés et reçus
 */
export const getMessages = () => api.get('/messages');

/**
 * Récupération des conversations de l'utilisateur connecté
 * @returns {Promise} - Conversations groupées par utilisateur
 */
export const getConversations = () => api.get('/messages/conversations');

/**
 * Récupération des utilisateurs disponibles pour la discussion
 * @returns {Promise} - Liste des utilisateurs avec qui on peut discuter
 */
export const getAvailableUsers = () => api.get('/messages/available-users');

/**
 * Envoi d'un nouveau message
 * @param {Object} messageData - Données du message (destinataire_id, contenu)
 * @returns {Promise} - Message envoyé
 */
export const sendMessage = (messageData) => api.post('/messages', messageData);

/**
 * Récupération des messages d'une conversation spécifique
 * @param {number} id - ID de l'utilisateur avec qui on converse
 * @returns {Promise} - Messages de la conversation
 */
export const getConversationMessages = (id) => api.get(`/messages/conversations/${id}`);

/**
 * Marque les messages d'une conversation comme lus
 * @param {number} conversationId - ID de l'autre utilisateur dans la conversation
 * @returns {Promise} - Confirmation
 */
export const markMessagesAsRead = (conversationId) => api.post(`/messages/read/${conversationId}`);

// ============================================================================
// ROUTES DE GESTION DES ÉVÉNEMENTS
// ============================================================================

/**
 * Récupération de tous les événements
 * @returns {Promise} - Liste des événements avec les informations des auteurs
 */
export const getEvents = () => api.get('/events');

/**
 * Création d'un nouvel événement (Admin uniquement)
 * @param {Object} eventData - Données de l'événement (titre, description, date)
 * @returns {Promise} - Événement créé
 */
export const createEvent = (eventData) => api.post('/events', eventData);

/**
 * Récupération d'un événement spécifique
 * @param {number} id - ID de l'événement
 * @returns {Promise} - Détails de l'événement
 */
export const getEvent = (id) => api.get(`/events/${id}`);

/**
 * Modification d'un événement (Admin uniquement)
 * @param {number} id - ID de l'événement
 * @param {Object} eventData - Données mises à jour
 * @returns {Promise} - Événement modifié
 */
export const updateEvent = (id, eventData) => api.put(`/events/${id}`, eventData);

/**
 * Suppression d'un événement (Admin uniquement)
 * @param {number} id - ID de l'événement
 * @returns {Promise} - Confirmation de suppression
 */
export const deleteEvent = (id) => api.delete(`/events/${id}`);

// ============================================================================
// ROUTES DE GESTION DES DEVOIRS
// ============================================================================

/**
 * Récupération des devoirs selon le rôle
 * @returns {Promise} - Devoirs filtrés selon le rôle de l'utilisateur
 */
export const getHomework = () => api.get('/homework');

/**
 * Création d'un nouveau devoir (Admin et Professeur uniquement)
 * @param {Object} homeworkData - Données du devoir (titre, description, date_limite, classe_id)
 * @returns {Promise} - Devoir créé
 */
export const createHomework = (homeworkData) => api.post('/homework', homeworkData);

/**
 * Récupération d'un devoir spécifique
 * @param {number} id - ID du devoir
 * @returns {Promise} - Détails du devoir avec les relations
 */
export const getHomeworkById = (id) => api.get(`/homework/${id}`);

/**
 * Modification d'un devoir (Admin et Professeur créateur uniquement)
 * @param {number} id - ID du devoir
 * @param {Object} homeworkData - Données mises à jour
 * @returns {Promise} - Devoir modifié
 */
export const updateHomework = (id, homeworkData) => api.put(`/homework/${id}`, homeworkData);

/**
 * Suppression d'un devoir (Admin et Professeur créateur uniquement)
 * @param {number} id - ID du devoir
 * @returns {Promise} - Confirmation de suppression
 */
export const deleteHomework = (id) => api.delete(`/homework/${id}`);

// ============================================================================
// ROUTES DE GESTION DES NOTES
// ============================================================================

/**
 * Récupération des notes selon le rôle
 * @returns {Promise} - Notes filtrées selon le rôle de l'utilisateur
 */
export const getGrades = () => api.get('/grades');

/**
 * Création d'une nouvelle note (Admin et Professeur uniquement)
 * @param {Object} gradeData - Données de la note (note, matiere, commentaire, eleve_id)
 * @returns {Promise} - Note créée
 */
export const createGrade = (gradeData) => api.post('/grades', gradeData);

/**
 * Récupération d'une note spécifique
 * @param {number} id - ID de la note
 * @returns {Promise} - Détails de la note avec les relations
 */
export const getGradeById = (id) => api.get(`/grades/${id}`);

/**
 * Modification d'une note (Admin et Professeur créateur uniquement)
 * @param {number} id - ID de la note
 * @param {Object} gradeData - Données mises à jour
 * @returns {Promise} - Note modifiée
 */
export const updateGrade = (id, gradeData) => api.put(`/grades/${id}`, gradeData);

/**
 * Suppression d'une note (Admin et Professeur créateur uniquement)
 * @param {number} id - ID de la note
 * @returns {Promise} - Confirmation de suppression
 */
export const deleteGrade = (id) => api.delete(`/grades/${id}`);

export default api;