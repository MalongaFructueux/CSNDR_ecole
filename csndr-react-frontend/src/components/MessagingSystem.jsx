import React, { useState, useEffect } from 'react';
import { Plus, Send, MessageCircle, User } from 'lucide-react';
import Modal from './Modal';
import RoleBadge from './RoleBadge';
import { getConversations, getAvailableUsers, sendMessage as sendMessageApi, getConversationMessages } from '../services/api';

/**
 * Composant MessagingSystem - Système de messagerie complet avec gestion des conversations
 * 
 * Ce composant gère :
 * - L'affichage des conversations existantes
 * - L'envoi de nouveaux messages
 * - La gestion des conversations en temps réel
 * - Les restrictions selon les rôles utilisateurs
 * 
 * Fonctionnalités principales :
 * - Interface de chat moderne avec design responsive
 * - Gestion des conversations groupées par utilisateur
 * - Envoi et réception de messages en temps réel
 * - Modal pour créer de nouvelles conversations
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

  /**
   * Chargement initial des conversations et utilisateurs disponibles
   * Exécuté au montage du composant
   */
  useEffect(() => {
    loadConversations();
    loadAvailableUsers();
  }, [user]);

  /**
   * Chargement des messages d'une conversation spécifique
   * Exécuté quand une conversation est sélectionnée
   */
  useEffect(() => {
    if (selectedConversation) {
      loadConversationMessages(selectedConversation);
    }
  }, [selectedConversation]);

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
   * Charge les messages d'une conversation spécifique
   * @param {number} conversationId - ID de la conversation
   */
  const loadConversationMessages = async (conversationId) => {
    try {
      const response = await getConversationMessages(conversationId);
      setCurrentMessages(response.data);
    } catch (error) {
      console.error('Erreur lors du chargement des messages:', error);
    }
  };

  /**
   * Envoie un nouveau message dans la conversation sélectionnée
   * Met à jour automatiquement les conversations et messages
   * @param {Event} e - Événement du formulaire
   */
  const handleSendMessage = async (e) => {
    e.preventDefault();
    if (selectedConversation && newMessage.trim()) {
      try {
        await sendMessageApi({
          destinataire_id: selectedConversation,
          contenu: newMessage
        });
        setNewMessage('');
        // Rechargement des messages et conversations
        loadConversationMessages(selectedConversation);
        loadConversations();
      } catch (error) {
        console.error('Erreur lors de l\'envoi du message:', error);
      }
    }
  };

  /**
   * Crée une nouvelle conversation avec un utilisateur
   * @param {Event} e - Événement du formulaire
   */
  const handleNewConversation = async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const destinataireId = formData.get('destinataire_id');
    const contenu = formData.get('contenu');

    if (destinataireId && contenu.trim()) {
      try {
        await sendMessageApi({
          destinataire_id: parseInt(destinataireId),
          contenu: contenu.trim()
        });
        setIsModalOpen(false);
        loadConversations();
        e.target.reset();
      } catch (error) {
        console.error('Erreur lors de l\'envoi du message:', error);
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
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
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
                const otherUser = conversation.user;
                const lastMessage = conversation.last_message;
                const isSelected = selectedConversation === otherUser.id;
                
                return (
                  <div
                    key={otherUser.id}
                    onClick={() => setSelectedConversation(otherUser.id)}
                    className={`p-4 cursor-pointer hover:bg-gray-50 transition-colors ${
                      isSelected ? 'bg-primary-50 border-r-4 border-primary-600' : ''
                    }`}
                  >
                    <div className="flex items-center gap-3">
                      {/* Avatar de l'utilisateur */}
                      <div className="w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-sm font-medium">
                        {otherUser.prenom[0]}{otherUser.nom[0]}
                      </div>
                      <div className="flex-1 min-w-0">
                        <div className="flex items-center justify-between">
                          <p className="font-medium text-gray-900 truncate">
                            {otherUser.prenom} {otherUser.nom}
                          </p>
                          <RoleBadge role={otherUser.role} />
                        </div>
                        {lastMessage && (
                          <p className="text-sm text-gray-500 truncate">
                            {lastMessage.contenu}
                          </p>
                        )}
                      </div>
                    </div>
                  </div>
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
                Destinataire
              </label>
              <select
                name="destinataire_id"
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              >
                <option value="">Sélectionnez un destinataire</option>
                {availableUsers.map((user) => (
                  <option key={user.id} value={user.id}>
                    {user.prenom} {user.nom} - <RoleBadge role={user.role} />
                  </option>
                ))}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Message
              </label>
              <textarea
                name="contenu"
                required
                rows={4}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="Tapez votre message..."
              ></textarea>
            </div>
            <div className="flex justify-end gap-2">
              <button
                type="button"
                onClick={() => setIsModalOpen(false)}
                className="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
              >
                Annuler
              </button>
              <button
                type="submit"
                className="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
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