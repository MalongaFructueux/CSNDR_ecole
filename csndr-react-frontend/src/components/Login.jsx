import React, { useState } from 'react';
import logo from '../assets/logo.PNG';

const Login = ({ onLogin }) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [formErrors, setFormErrors] = useState({});

  // Validation du formulaire
  const validateForm = () => {
    const errors = {};
    
    if (!email.trim()) {
      errors.email = 'L\'email est requis';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim())) {
      errors.email = 'Format d\'email invalide';
    }
    
    if (!password) {
      errors.password = 'Le mot de passe est requis';
    } else if (password.length < 6) {
      errors.password = 'Le mot de passe doit contenir au moins 6 caractères';
    }
    
    setFormErrors(errors);
    return Object.keys(errors).length === 0;
  };

  // Gestion de la connexion avec l'API Laravel
  const handleSubmit = async (e) => {
    e.preventDefault();
    
    // Validation du formulaire
    if (!validateForm()) {
      return;
    }
    
    try {
      setLoading(true);
      setError('');
      setFormErrors({});
      
      // Appel à l'API Laravel via la fonction onLogin
      const result = await onLogin({ email: email.trim(), password });
      
      if (!result.success) {
        setError(result.error || 'Erreur de connexion');
      }
    } catch (error) {
      console.error('Erreur de connexion:', error);
      setError(error.message || 'Erreur de connexion au serveur');
    } finally {
      setLoading(false);
    }
  };

  // Réinitialisation des erreurs lors de la saisie
  const handleEmailChange = (e) => {
    setEmail(e.target.value);
    if (formErrors.email) {
      setFormErrors(prev => ({ ...prev, email: '' }));
    }
  };

  const handlePasswordChange = (e) => {
    setPassword(e.target.value);
    if (formErrors.password) {
      setFormErrors(prev => ({ ...prev, password: '' }));
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-[var(--bleu-clair)] to-[var(--vert-clair)] flex items-center justify-center p-4 sm:p-6">
      <div className="bg-[var(--blanc-pur)] rounded-2xl shadow-xl p-6 sm:p-8 w-full max-w-md">
        <div className="text-center mb-8">
          <div className="w-24 h-24 sm:w-28 sm:h-28 rounded-full mx-auto mb-4 flex items-center justify-center overflow-hidden shadow-md">
            <img src={logo} alt="CSNDR Logo" className="w-full h-full object-contain bg-white" />
          </div>
          <h1 className="text-2xl sm:text-3xl font-semibold text-title">École Primaire</h1>
          <p className="text-body">Plateforme scolaire</p>
        </div>
        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label className="block text-sm font-medium text-body mb-2">Email *</label>
            <input
              type="email"
              value={email}
              onChange={handleEmailChange}
              className={`input ${formErrors.email ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''}`}
              placeholder="Votre email"
              disabled={loading}
            />
            {formErrors.email && (
              <p className="text-red-500 text-xs mt-1">{formErrors.email}</p>
            )}
          </div>
          <div>
            <label className="block text-sm font-medium text-body mb-2">Mot de passe *</label>
            <input
              type="password"
              value={password}
              onChange={handlePasswordChange}
              className={`input ${formErrors.password ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''}`}
              placeholder="Votre mot de passe"
              disabled={loading}
            />
            {formErrors.password && (
              <p className="text-red-500 text-xs mt-1">{formErrors.password}</p>
            )}
          </div>
          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
              <div className="flex items-center">
                <svg className="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                </svg>
                {error}
              </div>
            </div>
          )}
          <button 
            type="submit" 
            className={`w-full btn-primary ${loading ? 'opacity-50 cursor-not-allowed' : ''}`}
            disabled={loading}
          >
            {loading ? (
              <div className="flex items-center justify-center">
                <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                Connexion...
              </div>
            ) : (
              'Se connecter'
            )}
          </button>
        </form>
        
        {/* Informations de test
        <div className="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
          <h3 className="text-sm font-medium text-blue-800 mb-2">Comptes de test :</h3>
          <div className="text-xs text-blue-700 space-y-1">
            <p><strong>Admin:</strong> admin@csndr.test / Password123!</p>
            <p><strong>Professeur:</strong> prof@csndr.test / Password123!</p>
            <p><strong>Parent:</strong> parent@csndr.test / Password123!</p>
            <p><strong>Élève:</strong> eleve@csndr.test / Password123!</p>
          </div>
        </div> */}
      </div>
    </div>
  );
};

export default Login;