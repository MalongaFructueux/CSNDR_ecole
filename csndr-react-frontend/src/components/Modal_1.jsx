import React from 'react';

const Modal = ({ isOpen, onClose, title, children }) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-opacity duration-300">
      <div className="bg-[var(--blanc-pur)] rounded-xl p-4 sm:p-6 w-full max-w-md sm:max-w-2xl max-h-[90vh] overflow-y-auto transform scale-95 animate-modal-enter" role="dialog" aria-labelledby="modal-title">
        <div className="flex justify-between items-center mb-4">
          <h2 id="modal-title" className="text-lg sm:text-xl font-semibold text-title">{title}</h2>
          <button
            onClick={onClose}
            className="text-[var(--gris-neutre)] hover:text-[var(--bleu-principal)] transition-colors"
            aria-label="Fermer la modale"
          >
            ✕
          </button>
        </div>
        {children}
      </div>
    </div>
  );
};

export default Modal;