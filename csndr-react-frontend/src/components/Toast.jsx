import React, { useEffect } from 'react';

const Toast = ({ type = 'success', message = '', open = false, onClose = () => {} }) => {
  useEffect(() => {
    if (!open) return;
    const t = setTimeout(onClose, 3000);
    return () => clearTimeout(t);
  }, [open, onClose]);

  if (!open || !message) return null;

  const colors = type === 'error'
    ? 'bg-[var(--rouge-erreur)] text-[var(--blanc-pur)]'
    : 'bg-[var(--vert-accent)] text-[var(--blanc-pur)]';

  return (
    <div className="fixed top-4 right-4 z-50 animate-toast-enter">
      <div className={`px-4 py-3 rounded-lg shadow-lg ${colors} transition-opacity duration-300`}>
        {message}
      </div>
    </div>
  );
};

export default Toast;