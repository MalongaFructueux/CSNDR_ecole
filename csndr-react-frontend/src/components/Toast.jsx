import React, { useEffect } from 'react';
import { CheckCircle, XCircle, AlertCircle, Info, X } from 'lucide-react';

/**
 * Composant Toast - Notifications toast pour l'utilisateur
 * 
 * Ce composant gère :
 * - L'affichage des notifications de succès, erreur, avertissement et info
 * - La fermeture automatique après un délai
 * - La fermeture manuelle
 * - Les icônes appropriées selon le type
 * 
 * @param {Object} props - Propriétés du composant
 * @param {boolean} props.open - État d'ouverture du toast
 * @param {string} props.type - Type de toast (success, error, warning, info)
 * @param {string} props.message - Message à afficher
 * @param {Function} props.onClose - Fonction de fermeture
 * @param {number} props.duration - Durée d'affichage en ms (défaut: 5000)
 */
const Toast = ({ open, type = 'info', message, onClose, duration = 5000 }) => {
  // Configuration des types de toast
  const toastConfig = {
    success: {
      icon: CheckCircle,
      bgColor: 'bg-green-50',
      borderColor: 'border-green-200',
      textColor: 'text-green-800',
      iconColor: 'text-green-400'
    },
    error: {
      icon: XCircle,
      bgColor: 'bg-red-50',
      borderColor: 'border-red-200',
      textColor: 'text-red-800',
      iconColor: 'text-red-400'
    },
    warning: {
      icon: AlertCircle,
      bgColor: 'bg-yellow-50',
      borderColor: 'border-yellow-200',
      textColor: 'text-yellow-800',
      iconColor: 'text-yellow-400'
    },
    info: {
      icon: Info,
      bgColor: 'bg-blue-50',
      borderColor: 'border-blue-200',
      textColor: 'text-blue-800',
      iconColor: 'text-blue-400'
    }
  };

  const config = toastConfig[type] || toastConfig.info;
  const IconComponent = config.icon;

  // Fermeture automatique après la durée spécifiée
  useEffect(() => {
    if (open && duration > 0) {
      const timer = setTimeout(() => {
        onClose();
      }, duration);

      return () => clearTimeout(timer);
    }
  }, [open, duration, onClose]);

  if (!open) return null;

  return (
    <div className="fixed top-4 right-4 z-50 animate-in slide-in-from-right-2 duration-300">
      <div className={`flex items-start p-4 rounded-lg border ${config.bgColor} ${config.borderColor} shadow-lg max-w-sm`}>
        <IconComponent className={`w-5 h-5 ${config.iconColor} mt-0.5 mr-3 flex-shrink-0`} />
        <div className="flex-1">
          <p className={`text-sm font-medium ${config.textColor}`}>
            {message}
          </p>
        </div>
        <button
          onClick={onClose}
          className={`ml-3 ${config.textColor} hover:opacity-70 transition-opacity`}
        >
          <X className="w-4 h-4" />
        </button>
      </div>
    </div>
  );
};

export default Toast;