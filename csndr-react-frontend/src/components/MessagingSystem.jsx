import React, { useState, useEffect } from 'react';
import { Plus, Send, MessageCircle, User, Search, Download } from 'lucide-react';
import Modal from './Modal';
import RoleBadge from './RoleBadge';
import { getConversations, getAvailableUsers, sendMessage as sendMessageApi, getConversationMessages, searchUsers, markMessagesAsRead } from '../services/api';

/**
 * Composant MessagingSystem - Système de messagerie complet avec gestion des conversations
 * 
 * Ce composant gère :
 * - L'affichage des conversations existantes
 * - L'envoi de nouveaux messages
 * - La gestion des conversations en temps réel
 * - Les restrictions selon les rôles utilisateurs
 * - La recherche d'utilisateurs pour créer de nouvelles conversations
 * 
 * Fonctionnalités principales :
 * - Interface de chat moderne avec design responsive
 * - Gestion des conversations groupées par utilisateur
 * - Envoi et réception de messages en temps réel
 * - Modal pour créer de nouvelles conversations avec recherche
 * - Affichage des badges de rôle des utilisateurs
 * 
 * @param {Object} user - Objet utilisateur connecté
 */
const MessagingSystem = ({ user }) => {
  // États locaux pour la gestion des données
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedConversation, setSelectedConversation] = useState(null);
  const [newMessage, setNewMessage] = useState('');
  const [conversations, setConversations] = useState([]);
  const [availableUsers, setAvailableUsers] = useState([]);
  const [currentMessages, setCurrentMessages] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [searchResults, setSearchResults] = useState([]);
  const [searching, setSearching] = useState(false);
  const [selectedUserId, setSelectedUserId] = useState(null);
  const [newConversationMessage, setNewConversationMessage] = useState('');
  const [error, setError] = useState(null);

  /**
   * Chargement initial des conversations et utilisateurs disponibles
   * Exécuté au montage du composant
   */
  useEffect(() => {
    loadConversations();
    loadAvailableUsers();
  }, [user]);



  /**
   * Charge toutes les conversations de l'utilisateur connecté
   * Récupère les conversations depuis l'API et les formate
   */
  const loadConversations = async () => {
    try {
      const response = await getConversations();
      setConversations(response.data);
    } catch (error) {
      console.error('Erreur lors du chargement des conversations:', error);
    } finally {
      setLoading(false);
    }
  };

  /**
   * Charge la liste des utilisateurs avec qui on peut discuter
   * Les utilisateurs disponibles dépendent du rôle de l'utilisateur connecté
   */
  const loadAvailableUsers = async () => {
    try {
      const response = await getAvailableUsers();
      setAvailableUsers(response.data);
    } catch (error) {
      console.error('Erreur lors du chargement des utilisateurs:', error);
    }
  };



  /**
   * Recherche d'utilisateurs par nom/prénom/email
   * @param {string} query - Terme de recherche
   */
  const handleSearchUsers = async (query) => {
    if (query.length < 2) {
      setSearchResults([]);
      return;
    }

    try {
      setSearching(true);
      const response = await searchUsers(query);
      setSearchResults(response.data);
    } catch (error) {
      console.error('Erreur lors de la recherche:', error);
      setSearchResults([]);
    } finally {
      setSearching(false);
    }
  };

  /**
   * Envoie un nouveau message dans la conversation sélectionnée
   * Met à jour automatiquement les conversations et messages
   * @param {Event} e - Événement du formulaire
   */
  const handleSendMessage = async (e) => {
    e.preventDefault();
    if (!newMessage.trim() || !selectedConversation) return;

    try {
      await sendMessageApi({
        destinataire_id: selectedConversation.user.id,
        contenu: newMessage.trim()
      });
      
      setNewMessage('');
      
      // Recharger les messages de la conversation actuelle
      const response = await getConversationMessages(selectedConversation.user.id);
      setCurrentMessages(response.data);
      
      // Recharger la liste des conversations pour mettre à jour le dernier message
      const conversationsResponse = await getConversations();
      setConversations(conversationsResponse.data);
      
    } catch (error) {
      console.error('Erreur lors de l\'envoi du message:', error);
      setError('Impossible d\'envoyer le message.');
    }
  };

  /**
   * Crée une nouvelle conversation avec un utilisateur
   * @param {Event} e - Événement du formulaire
   */
  const handleNewConversation = async (e) => {
    e.preventDefault();

    if (!selectedUserId || !newConversationMessage.trim()) {
      console.error('Aucun utilisateur sélectionné ou message vide.');
      return;
    }

    try {
      await sendMessageApi({
        destinataire_id: selectedUserId,
        contenu: newConversationMessage.trim()
      });
      
      setIsModalOpen(false);
      setSearchQuery('');
      setSearchResults([]);
      setSelectedUserId(null);
      setNewConversationMessage(''); // Clear message on send
      // Recharger les conversations
      await loadConversations();
    } catch (error) {
      console.error('Erreur lors de la création de la conversation:', error);
    }
  };

  /**
   * Sélectionne une conversation et marque les messages comme lus si nécessaire
   * @param {Object} conversation - Objet de conversation
   */
  const handleSelectConversation = async (conversation) => {
    // Si la conversation est déjà sélectionnée, ne rien faire
    if (selectedConversation?.user.id === conversation.user.id) return;

    setSelectedConversation(conversation);
    try {
      // Marquer les messages comme lus si la conversation a des messages non lus
      if (conversation.unread_count > 0) {
        await markMessagesAsRead(conversation.user.id);
        // Mettre à jour l'état des conversations pour refléter que les messages sont lus
        setConversations(prevConversations =>
          prevConversations.map(c =>
            c.user.id === conversation.user.id ? { ...c, unread_count: 0 } : c
          )
        );
      }
      // Récupérer les messages de la conversation sélectionnée
      const response = await getConversationMessages(conversation.user.id);
      setCurrentMessages(response.data);
    } catch (error) {
      console.error('Erreur lors de la sélection de la conversation:', error);
      setError('Impossible de charger les messages.');
    }
  };

  /**
   * Formate une date pour l'affichage
   * @param {string} dateString - Date au format ISO
   * @returns {string} - Date et heure formatées
   */
  const formatDate = (dateString) => {
    const date = new Date(dateString);
    
    return date.toLocaleString('fr-FR', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
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
    <div className="p-6 h-screen flex flex-col bg-gray-50">
      {/* Affichage des messages d'erreur */}
      {error && (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
          <strong className="font-bold">Erreur :</strong>
          <span className="block sm:inline"> {error}</span>
          <button onClick={() => setError(null)} className="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg className="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Fermer</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
          </button>
        </div>
      )}
      {/* Header avec titre et bouton nouveau message */}
      <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Messagerie</h2>
          <p className="text-gray-600">Communiquez avec les autres utilisateurs</p>
        </div>
        <button 
          onClick={() => setIsModalOpen(true)} 
          className="flex items-center gap-2 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors mt-4 sm:mt-0"
        >
          <Plus size={16} />
          Nouveau message
        </button>
      </div>

      {/* Interface principale de messagerie */}
      <div className="flex-1 flex flex-col sm:flex-row gap-6 overflow-hidden">
        {/* Liste des conversations - Panel de gauche */}
        <div className="w-full sm:w-1/3 bg-white rounded-lg shadow-lg overflow-hidden">
          <div className="bg-gradient-to-r from-primary-600 to-primary-700 text-white p-4">
            <h3 className="font-semibold text-lg">Conversations</h3>
          </div>
          <div className="divide-y max-h-96 overflow-y-auto">
            {conversations.length === 0 ? (
              <div className="p-4 text-center text-gray-500">
                Aucune conversation
              </div>
            ) : (
              conversations.map((conversation) => {
                const isSelected = selectedConversation?.user.id === conversation.user.id;
                return (
                  <li
                    key={conversation.user.id}
                    onClick={() => handleSelectConversation(conversation)}
                    className={`flex items-center justify-between p-4 cursor-pointer transition-colors hover:bg-gray-100 ${
                      isSelected ? 'bg-blue-100 border-r-4 border-blue-500' : ''
                    }`}
                  >
                    <div className="flex items-center w-full">
                      <div className="w-12 h-12 rounded-full bg-gray-300 flex-shrink-0 mr-4 flex items-center justify-center">
                        <User className="w-6 h-6 text-gray-600" />
                      </div>
                      <div className="flex-grow overflow-hidden">
                        <div className="flex items-center">
                          <p className="font-semibold text-gray-800 truncate">{`${conversation.user.prenom} ${conversation.user.nom}`}</p>
                          <RoleBadge role={conversation.user.role} />
                        </div>
                        <p className="text-sm text-gray-500 truncate">
                          {conversation.last_message?.contenu || 'Aucun message'}
                        </p>
                      </div>
                      {conversation.unread_count > 0 && (
                        <div className="ml-4 flex-shrink-0">
                          <span className="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                            {conversation.unread_count}
                          </span>
                        </div>
                      )}
                    </div>
                  </li>
                );
              })
            )}
          </div>
        </div>

        {/* Zone de messages - Panel de droite */}
        <div className="flex-1 bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
          {selectedConversation ? (
            <>
              {/* Header de la conversation */}
              <div className="bg-gradient-to-r from-primary-600 to-primary-700 text-white p-4">
                <h3 className="font-semibold text-lg">
                  {conversations.find(c => c.user.id === selectedConversation)?.user.prenom} {conversations.find(c => c.user.id === selectedConversation)?.user.nom}
                </h3>
              </div>
              
              {/* Liste des messages */}
              <div className="flex-1 overflow-y-auto p-4 space-y-4">
                {currentMessages.map((message) => {
                  const isOwnMessage = message.expediteur_id === user.id;
                  return (
                    <div
                      key={message.id}
                      className={`flex ${isOwnMessage ? 'justify-end' : 'justify-start'}`}
                    >
                      <div
                        className={`max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
                          isOwnMessage
                            ? 'bg-primary-600 text-white'
                            : 'bg-gray-100 text-gray-900'
                        }`}
                      >
                        <p className="text-sm">{message.contenu}</p>
                        <p className={`text-xs mt-1 ${
                          isOwnMessage ? 'text-primary-100' : 'text-gray-500'
                        }`}>
                          {formatDate(message.date_envoi)}
                        </p>
                      </div>
                    </div>
                  );
                })}
              </div>
              
              {/* Formulaire d'envoi de message */}
              <div className="p-4 border-t">
                <form onSubmit={handleSendMessage} className="flex gap-2">
                  <input
                    type="text"
                    value={newMessage}
                    onChange={(e) => setNewMessage(e.target.value)}
                    placeholder="Tapez votre message..."
                    className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  />
                  <button
                    type="submit"
                    disabled={!newMessage.trim()}
                    className="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  >
                    <Send size={16} />
                  </button>
                </form>
              </div>
            </>
          ) : (
            <div className="flex-1 flex items-center justify-center text-gray-500">
              <div className="text-center">
                <MessageCircle size={48} className="mx-auto mb-4 text-gray-300" />
                <p>Sélectionnez une conversation pour commencer</p>
              </div>
            </div>
          )}
        </div>
      </div>

      {/* Modal pour nouvelle conversation */}
      <Modal isOpen={isModalOpen} onClose={() => setIsModalOpen(false)}>
        <div className="p-6">
          <h3 className="text-lg font-semibold mb-4">Nouveau message</h3>
          <form onSubmit={handleNewConversation} className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Rechercher un destinataire
              </label>
              <div className="relative">
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => {
                    setSearchQuery(e.target.value);
                    setSelectedUserId(null); // Reset selection when query changes
                    handleSearchUsers(e.target.value);
                  }}
                  placeholder="Tapez le nom, prénom ou email..."
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent pr-10"
                />
                <Search className="absolute right-3 top-2.5 h-5 w-5 text-gray-400" />
              </div>
              
              {/* Résultats de recherche */}
              {searching && (
                <div className="mt-2 text-sm text-gray-500">
                  Recherche en cours...
                </div>
              )}
              
              {searchResults.length > 0 && (
                <div className="mt-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg">
                  {searchResults.map((user) => (
                    <li 
                      key={user.id}
                      onClick={() => {
                        if (user && user.prenom && user.nom) {
                          setSearchQuery(`${user.prenom} ${user.nom}`);
                        }
                        setSelectedUserId(user.id);
                        setSearchResults([]);
                      }}
                      className="p-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                    >
                      <div className="flex items-center justify-between">
                        <span className="font-medium">{user.prenom} {user.nom}</span>
                        <RoleBadge role={user.role} />
                        <span className="text-sm text-gray-500">{user.email}</span>
                      </div>
                    </li>
                  ))}
                </div>
              )}
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Message
              </label>
              <textarea
                name="contenu"
                required
                rows={4}
                value={newConversationMessage}
                onChange={(e) => setNewConversationMessage(e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Tapez votre message..."
              ></textarea>
            </div>
            
            <div className="flex justify-end gap-2">
              <button
                type="button"
                onClick={() => {
                  setIsModalOpen(false);
                  setSearchQuery('');
                  setSearchResults([]);
                  setNewConversationMessage(''); // Also clear message on cancel
                }}
                className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Annuler
              </button>
              <button
                type="submit"
                disabled={!selectedUserId || !newConversationMessage.trim()}
                className="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                Envoyer
              </button>
            </div>
          </form>
        </div>
      </Modal>
    </div>
  );
};

export default MessagingSystem;