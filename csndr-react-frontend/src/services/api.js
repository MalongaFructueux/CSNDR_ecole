import axios from 'axios';

/**
 * Configuration de base pour Axios
 * 
 * Cette configuration définit :
 * - L'URL de base de l'API Laravel
 * - Les headers par défaut
 * - La gestion des erreurs
 * - La gestion des tokens d'authentification
 */
const api = axios.create({
    // URL de base de l'API Laravel - Configuration pour production et développement
    baseURL: process.env.REACT_APP_API_URL || (
        process.env.NODE_ENV === 'production' 
            ? 'https://csndr-gestion.com/api'  // En production, utiliser le chemin relatif
            : 'http://localhost:8000/api'  // En développement local
    ),
    
    // Headers par défaut
    headers: {
        // Ne pas forcer le Content-Type ici pour permettre à FormData de définir multipart/form-data automatiquement
        'Accept': 'application/json',
    },
    
    // Timeout de 10 secondes pour les requêtes
    timeout: 10000,
});

/**
 * Intercepteur pour ajouter le token d'authentification
 * 
 * Cet intercepteur ajoute automatiquement le token JWT
 * à toutes les requêtes si il existe dans le localStorage
 */
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        // S'assurer que FormData est correctement envoyé en multipart/form-data
        // Axios définira automatiquement le bon Content-Type (avec boundary) si on ne le force pas
        if (config.data instanceof FormData) {
            delete config.headers['Content-Type'];
        } else if (config.method && config.method.toLowerCase() !== 'get') {
            // Pour les payloads JSON (non-FormData), définir le Content-Type JSON
            config.headers['Content-Type'] = 'application/json';
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

/**
 * Intercepteur pour gérer les erreurs de réponse
 * 
 * Cet intercepteur gère :
 * - Les erreurs 401 (non authentifié)
 * - Les erreurs 403 (accès refusé)
 * - Les erreurs de réseau
 * - La redirection vers la page de connexion si nécessaire
 */
api.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        // Gestion des erreurs d'authentification
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }
        
        // Gestion des erreurs d'accès refusé
        if (error.response?.status === 403) {
            console.error('Accès refusé:', error.response.data.message);
        }
        
        return Promise.reject(error);
    }
);

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
 * Inscription d'un nouvel utilisateur
 * @param {Object} userData - Données d'inscription
 * @returns {Promise} - Token JWT et informations utilisateur
 */
export const register = (userData) => api.post('/auth/register', userData);

/**
 * Vérification de disponibilité d'un email
 * @param {string} email - Email à vérifier
 * @returns {Promise} - Disponibilité de l'email
 */
export const checkEmail = (email) => api.post('/auth/check-email', { email });

/**
 * Récupération des parents disponibles pour inscription
 * @returns {Promise} - Liste des parents
 */
export const getAvailableParents = () => api.get('/auth/available-parents');

/**
 * Récupération des classes disponibles pour inscription
 * @returns {Promise} - Liste des classes
 */
export const getAvailableClasses = () => api.get('/auth/available-classes');

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
export const searchUsers = (query) => api.get(`/users/search?query=${query}`);

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