import React from 'react';

const ConfirmDialog = ({ open = false, title = 'Confirmation', message = '', confirmText = 'Confirmer', cancelText = 'Annuler', onConfirm = () => {}, onCancel = () => {} }) => {
  if (!open) return null;
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div className="bg-white rounded-lg shadow-xl w-full max-w-md transition transform duration-200 ease-out">
        <div className="p-4 sm:p-6">
          <h3 className="text-lg font-semibold text-title mb-2">{title}</h3>
          <p className="text-body mb-6">{message}</p>
          <div className="flex gap-2 justify-end">
            <button onClick={onCancel} className="px-4 py-2 border rounded hover:bg-[var(--bleu-clair)] text-body">{cancelText}</button>
            <button onClick={onConfirm} className="btn-primary">{confirmText}</button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ConfirmDialog;
