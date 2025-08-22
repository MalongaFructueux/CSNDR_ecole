import React, { useState, useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import Navigation from './components/Navigation';
import MessagingSystem from './components/MessagingSystem';
import EventsManagement from './components/EventsManagement';
import HomeworkManagement from './components/HomeworkManagement';
import GradesManagement from './components/GradesManagement';
import UserManagement from './components/UserManagement';
import ClassManagement from './components/ClassManagement';
import Login from './components/Login';
import Register from './components/Register';
import { login as loginApi, logout as logoutApi, register as registerApi } from './services/api';

/**
 * Composant principal App - Point d'entrée de l'application
 * 
 * Ce composant gère :
 * - La navigation principale de l'application
 * - L'authentification et la gestion des sessions
 * - Le routage conditionnel selon l'état de connexion
 * - L'affichage des composants selon les permissions utilisateur
 * 
 * Architecture de routing :
 * - Routes publiques : /login
 * - Routes protégées : Toutes les autres routes nécessitent une authentification
 * - Redirection automatique vers /login si non authentifié
 * 
 * Gestion des états :
 * - user : Informations de l'utilisateur connecté
 * - isAuthenticated : État de l'authentification
 * - loading : État de chargement
 */
function App() {
  // États locaux pour la gestion de l'authentification
  const [user, setUser] = useState(null);
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [loading, setLoading] = useState(true);

  /**
   * Vérification de l'authentification au montage du composant
   * Récupère les informations utilisateur depuis le localStorage
   */
  useEffect(() => {
    const checkAuth = () => {
      const token = localStorage.getItem('token');
      const userData = localStorage.getItem('user');
      
      if (token && userData) {
        try {
          setUser(JSON.parse(userData));
          setIsAuthenticated(true);
        } catch (error) {
          console.error('Erreur lors du parsing des données utilisateur:', error);
          handleLogout();
        }
      }
      setLoading(false);
    };

    checkAuth();
  }, []);

  /**
   * Gestion de la connexion utilisateur
   * Authentifie l'utilisateur et stocke les informations en localStorage
   * 
   * @param {Object} credentials - Email et mot de passe
   * @returns {Promise} - Résultat de l'authentification
   */
  const handleLogin = async (credentials) => {
    try {
      const response = await loginApi(credentials);
      const { token, user: userData } = response.data;
      
      // Stockage des informations d'authentification
      localStorage.setItem('token', token);
      localStorage.setItem('user', JSON.stringify(userData));
      
      // Mise à jour des états
      setUser(userData);
      setIsAuthenticated(true);
      
      return { success: true };
    } catch (error) {
      console.error('Erreur de connexion:', error);
      return { 
        success: false, 
        error: error.response?.data?.message || 'Erreur de connexion' 
      };
    }
  };

  /**
   * Gestion de l'inscription utilisateur
   * Inscrit l'utilisateur et l'authentifie automatiquement
   * 
   * @param {Object} registrationData - Données d'inscription ou token/user si déjà traité
   * @returns {Promise} - Résultat de l'inscription
   */
  const handleRegister = async (registrationData) => {
    try {
      // Si les données contiennent déjà token et user, les utiliser directement
      if (registrationData.token && registrationData.user) {
        const { token, user: userData } = registrationData;
        
        // Stockage des informations d'authentification
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(userData));
        
        // Mise à jour des états
        setUser(userData);
        setIsAuthenticated(true);
        
        return { success: true };
      }
      
      // Sinon, effectuer l'inscription via l'API
      const response = await registerApi(registrationData);
      const { token, user: userData } = response.data;
      
      // Stockage des informations d'authentification
      localStorage.setItem('token', token);
      localStorage.setItem('user', JSON.stringify(userData));
      
      // Mise à jour des états
      setUser(userData);
      setIsAuthenticated(true);
      
      return { success: true };
    } catch (error) {
      console.error('Erreur d\'inscription:', error);
      return { 
        success: false, 
        error: error.response?.data?.message || 'Erreur d\'inscription' 
      };
    }
  };

  /**
   * Gestion de la déconnexion utilisateur
   * Nettoie les données locales et invalide le token
   */
  const handleLogout = async () => {
    try {
      // Appel API pour invalider le token côté serveur
      if (localStorage.getItem('token')) {
        await logoutApi();
      }
    } catch (error) {
      console.error('Erreur lors de la déconnexion:', error);
    } finally {
      // Nettoyage des données locales
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      
      // Réinitialisation des états
      setUser(null);
      setIsAuthenticated(false);
    }
  };

  // Affichage du loader pendant la vérification de l'authentification
  if (loading) {
    return (
      <div className="flex items-center justify-center h-screen bg-gray-50">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  return (
    <Router>
      <div className="App min-h-screen bg-gray-50">
        {/* Affichage conditionnel selon l'état d'authentification */}
        {isAuthenticated ? (
          <>
            {/* Navigation principale avec informations utilisateur */}
            <Navigation user={user} onLogout={handleLogout} />
            
            {/* Contenu principal avec routage */}
            <main className="min-h-screen">
              <Routes>
                {/* Route par défaut - Redirection vers le dashboard */}
                <Route 
                  path="/" 
                  element={<Navigate to="/dashboard" replace />} 
                />
                
                {/* Route dashboard - Page d'accueil après connexion */}
                <Route 
                  path="/dashboard" 
                  element={
                    <div className="p-6">
                      <h1 className="text-2xl font-bold text-gray-900 mb-4">
                        Tableau de bord - Centre Scolaire Notre Dame du Rosaire
                      </h1>
                      <div className="bg-white rounded-lg shadow-lg p-6">
                        <p className="text-gray-600">
                          Bienvenue {user?.prenom} {user?.nom} ! 
                          Vous êtes connecté en tant que <span className="font-semibold">{user?.role}</span>.
                        </p>
                        <p className="text-gray-500 mt-2">
                          Utilisez la navigation ci-dessus pour accéder aux différentes fonctionnalités.
                        </p>
                      </div>
                    </div>
                  } 
                />
                
                {/* Route de gestion des utilisateurs (Admin uniquement) */}
                {user?.role === 'admin' && (
                  <Route path="/users" element={<UserManagement user={user} />} />
                )}
                
                {/* Route de gestion des classes (Admin uniquement) */}
                {user?.role === 'admin' && (
                  <Route path="/classes" element={<ClassManagement user={user} />} />
                )}
                
                {/* Route de messagerie (Tous les rôles sauf élèves) */}
                {['admin', 'professeur', 'parent'].includes(user?.role) && (
                  <Route path="/messages" element={<MessagingSystem user={user} />} />
                )}
                
                {/* Route de gestion des événements (Lecture pour tous, création pour admin) */}
                <Route path="/events" element={<EventsManagement user={user} />} />
                
                {/* Route de gestion des devoirs (Selon les permissions) */}
                <Route path="/homework" element={<HomeworkManagement user={user} />} />
                
                {/* Route de gestion des notes (Selon les permissions) */}
                <Route path="/grades" element={<GradesManagement user={user} />} />
                
                {/* Route de fallback - Redirection vers le dashboard */}
                <Route path="*" element={<Navigate to="/dashboard" replace />} />
              </Routes>
            </main>
          </>
        ) : (
          /* Pages publiques si non authentifié */
          <Routes>
            <Route path="/login" element={<Login onLogin={handleLogin} />} />
            <Route path="/register" element={<Register onRegister={handleRegister} />} />
            <Route path="*" element={<Navigate to="/login" replace />} />
          </Routes>
        )}
      </div>
    </Router>
  );
}

export default App;