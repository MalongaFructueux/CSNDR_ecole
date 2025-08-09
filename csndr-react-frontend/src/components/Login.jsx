import React, { useState } from 'react';
import { mockData } from '../mockData';
import logo from '../assets/logo.PNG';

const Login = ({ onLogin }) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  // Gestion de la connexion
  const handleSubmit = (e) => {
    e.preventDefault();
    const user = mockData.users.find(u => u.email === email && u.password === password);
    if (user) {
      onLogin(user);
      setError('');
    } else {
      setError('Email ou mot de passe incorrect');
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
        <div onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label className="block text-sm font-medium text-body mb-2">Email</label>
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="input"
              placeholder="Votre email"
              required
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-body mb-2">Mot de passe</label>
            <input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="input"
              placeholder="Votre mot de passe"
              required
            />
          </div>
          {error && <p className="text-[var(--rouge-erreur)] text-sm">{error}</p>}
          <button onClick={handleSubmit} className="w-full btn-primary">
            Se connecter
          </button>
        </div>
      </div>
    </div>
  );
};

export default Login;