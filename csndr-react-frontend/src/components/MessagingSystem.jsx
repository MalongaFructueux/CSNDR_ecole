import React, { useState } from 'react';
import { Plus, MessageCircle } from 'lucide-react';
import Modal from './Modal';

const MessagingSystem = ({ data, setData, user }) => {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedConversation, setSelectedConversation] = useState(null);
  const [newMessage, setNewMessage] = useState('');
  const [newConversation, setNewConversation] = useState({
    destinataire_id: '',
    contenu: ''
  });

  // Filtrer les messages
  const getUserMessages = () => {
    return data.messages.filter(msg => msg.expediteur_id === user.id || msg.destinataire_id === user.id);
  };

  // Grouper par conversation
  const getConversations = () => {
    const messages = getUserMessages();
    const conversations = {};
    messages.forEach(msg => {
      const otherUserId = msg.expediteur_id === user.id ? msg.destinataire_id : msg.expediteur_id;
      if (!conversations[otherUserId]) conversations[otherUserId] = [];
      conversations[otherUserId].push(msg);
    });
    Object.keys(conversations).forEach(userId => {
      conversations[userId].sort((a, b) => new Date(a.date_envoi) - new Date(b.date_envoi));
    });
    return conversations;
  };

  // Envoi d'un message
  const sendMessage = (destinataireId, contenu) => {
    const newMsg = {
      id: Date.now(),
      expediteur_id: user.id,
      destinataire_id: destinataireId,
      contenu,
      date_envoi: new Date().toISOString()
    };
    setData(prev => ({
      ...prev,
      messages: [...prev.messages, newMsg]
    }));
  };

  // Gestion envoi message
  const handleSendMessage = (e) => {
    e.preventDefault();
    if (selectedConversation && newMessage.trim()) {
      sendMessage(selectedConversation, newMessage);
      setNewMessage('');
    }
  };

  // Gestion nouvelle conversation
  const handleNewConversation = (e) => {
    e.preventDefault();
    if (newConversation.destinataire_id && newConversation.contenu.trim()) {
      sendMessage(parseInt(newConversation.destinataire_id), newConversation.contenu);
      setNewConversation({ destinataire_id: '', contenu: '' });
      setIsModalOpen(false);
    }
  };

  const conversations = getConversations();
  const availableUsers = data.users.filter(u => u.id !== user.id);

  return (
    <div className="p-4 sm:p-6 h-screen flex flex-col">
      <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <h2 className="text-xl sm:text-2xl font-semibold text-title">Messagerie</h2>
        <button onClick={() => setIsModalOpen(true)} className="flex items-center gap-2 btn-secondary mt-4 sm:mt-0">
          <Plus size={16} />
          Nouveau message
        </button>
      </div>
      <div className="flex-1 flex flex-col sm:flex-row gap-4 sm:gap-6 overflow-hidden">
        <div className="w-full sm:w-1/3 card border-[var(--bleu-principal)]">
          <div className="bg-[var(--vert-accent)] text-[var(--blanc-pur)] p-4">
            <h3 className="font-semibold text-title">Conversations</h3>
          </div>
          <div className="divide-y max-h-96 overflow-y-auto">
            {Object.entries(conversations).map(([userId, messages]) => {
              const otherUser = data.users.find(u => u.id === parseInt(userId));
              const lastMessage = messages[messages.length - 1];
              const isSelected = selectedConversation === parseInt(userId);
              return (
                <div
                  key={userId}
                  onClick={() => setSelectedConversation(parseInt(userId))}
                  className={`p-4 cursor-pointer hover:bg-[var(--bleu-clair)] transition-colors ${isSelected ? 'bg-[var(--bleu-clair)]' : ''}`}
                >
                  <div className="flex items-center gap-2">
                    <div className="w-8 h-8 rounded-full bg-[var(--bleu-principal)] text-[var(--blanc-pur)] flex items-center justify-center text-sm font-medium">
                      {otherUser?.prenom[0]}
                    </div>
                    <div>
                      <p className="font-medium text-title">{otherUser?.prenom} {otherUser?.nom}</p>
                      <p className="text-xs text-[var(--gris-neutre)] truncate">{lastMessage.contenu}</p>
                    </div>
                  </div>
                </div>
              );
            })}
            {Object.keys(conversations).length === 0 && (
              <div className="p-4 text-center text-body">Aucune conversation</div>
            )}
          </div>
        </div>
        <div className="flex-1 card border-[var(--bleu-principal)] flex flex-col">
          {selectedConversation ? (
            <>
              <div className="bg-[var(--bleu-principal)] text-[var(--blanc-pur)] p-4">
                <h3 className="font-semibold text-title">
                  Conversation avec {data.users.find(u => u.id === selectedConversation)?.prenom} {data.users.find(u => u.id === selectedConversation)?.nom}
                </h3>
              </div>
              <div className="flex-1 p-4 overflow-y-auto max-h-[60vh]">
                {conversations[selectedConversation]?.map(msg => (
                  <div
                    key={msg.id}
                    className={`mb-4 flex ${msg.expediteur_id === user.id ? 'justify-end' : 'justify-start'}`}
                  >
                    <div className="flex items-start gap-2 max-w-[70%]">
                      {msg.expediteur_id !== user.id && (
                        <div className="w-8 h-8 rounded-full bg-[var(--bleu-principal)] text-[var(--blanc-pur)] flex items-center justify-center text-sm font-medium">
                          {data.users.find(u => u.id === msg.expediteur_id)?.prenom[0]}
                        </div>
                      )}
                      <div
                        className={`rounded-lg p-3 text-sm sm:text-base shadow-md ${msg.expediteur_id === user.id ? 'bg-[var(--bleu-principal)] text-[var(--blanc-pur)]' : 'bg-[var(--bleu-clair)] text-[var(--bleu-principal)]'}`}
                      >
                        <p>{msg.contenu}</p>
                        <p className="text-xs mt-1 opacity-70">
                          {new Date(msg.date_envoi).toLocaleString('fr-FR')}
                        </p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
              <div className="p-4 border-t">
                <div className="flex gap-2">
                  <input
                    type="text"
                    value={newMessage}
                    onChange={(e) => setNewMessage(e.target.value)}
                    className="input flex-1"
                    placeholder="Écrivez un message..."
                    aria-label="Écrire un message"
                  />
                  <button onClick={handleSendMessage} className="btn-primary">
                    Envoyer
                  </button>
                </div>
              </div>
            </>
          ) : (
            <div className="flex-1 flex items-center justify-center text-body">
              Sélectionnez une conversation
            </div>
          )}
        </div>
      </div>
      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title="Nouveau message"
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-body mb-1">Destinataire</label>
            <select
              value={newConversation.destinataire_id}
              onChange={(e) => setNewConversation({...newConversation, destinataire_id: e.target.value})}
              className="input"
              required
              aria-label="Sélectionner un destinataire"
            >
              <option value="">Sélectionner un destinataire</option>
              {availableUsers.map(u => (
                <option key={u.id} value={u.id}>{u.prenom} {u.nom} ({u.role})</option>
              ))}
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-body mb-1">Message</label>
            <textarea
              value={newConversation.contenu}
              onChange={(e) => setNewConversation({...newConversation, contenu: e.target.value})}
              className="input h-32"
              required
              aria-label="Contenu du message"
            />
          </div>
          <div className="flex gap-2 justify-end">
            <button onClick={() => setIsModalOpen(false)} className="px-4 py-2 border rounded hover:bg-[var(--bleu-clair)] text-body text-sm sm:text-base">
              Annuler
            </button>
            <button onClick={handleNewConversation} className="btn-primary">
              Envoyer
            </button>
          </div>
        </div>
      </Modal>
    </div>
  );
};

export default MessagingSystem;