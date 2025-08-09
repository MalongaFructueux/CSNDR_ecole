import React, { useState, useEffect } from 'react';
import { BrowserRouter as Router, Route, Routes, Navigate } from 'react-router-dom';
import Navigation from './components/Navigation';
import UserManagement from './components/UserManagement';
import ClassManagement from './components/ClassManagement';
import { login, logout, getUsers, getClasses } from './services/api';

const App = () => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [data, setData] = useState({ users: [], classes: [] });
  const [busy, setBusy] = useState(false);

  useEffect(() => {
    const token = localStorage.getItem('token');
    if (token) {
      setUser(JSON.parse(localStorage.getItem('user')));
      // fetch initial data if already logged in
      refreshAll();
    }
    setLoading(false);
  }, []);

  const refreshUsers = async () => {
    const res = await getUsers();
    setData(prev => ({ ...prev, users: res.data }));
  };

  const refreshClasses = async () => {
    const res = await getClasses();
    setData(prev => ({ ...prev, classes: res.data }));
  };

  const refreshAll = async () => {
    try {
      setBusy(true);
      await Promise.all([refreshUsers(), refreshClasses()]);
    } finally {
      setBusy(false);
    }
  };

  const handleLogin = async (credentials) => {
    const response = await login(credentials);
    localStorage.setItem('token', response.data.token);
    localStorage.setItem('user', JSON.stringify(response.data.user));
    setUser(response.data.user);
    await refreshAll();
  };

  const handleLogout = async () => {
    await logout();
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    setUser(null);
    setData({ users: [], classes: [] });
  };

  if (loading) return <div>Chargement...</div>;

  return (
    <Router>
      <div className="min-h-screen bg-[#FFFFFF]">
        {user ? (
          <>
            <Navigation user={user} onLogout={handleLogout} />
            <main className="container mx-auto p-4 sm:p-6">
              {busy && <div className="mb-4 text-sm text-[var(--gris-neutre)]">Chargement des données…</div>}
              <Routes>
                <Route path="/users" element={<UserManagement data={data} setData={setData} refreshUsers={refreshUsers} />} />
                <Route path="/classes" element={<ClassManagement data={data} setData={setData} refreshClasses={refreshClasses} />} />
                <Route path="/" element={<Navigate to="/users" />} />
              </Routes>
            </main>
          </>
        ) : (
          <div className="flex items-center justify-center min-h-screen">
            <form onSubmit={(e) => { e.preventDefault(); handleLogin({ email: e.target.email.value, password: e.target.password.value }); }} className="bg-[#FFFFFF] p-6 rounded-lg shadow-md">
              <h2 className="text-2xl font-semibold text-title mb-4">Connexion</h2>
              <input type="email" name="email" className="input mb-4" placeholder="Email" required />
              <input type="password" name="password" className="input mb-4" placeholder="Mot de passe" required />
              <button type="submit" className="btn-primary w-full">Se connecter</button>
            </form>
          </div>
        )}
      </div>
    </Router>
  );
};

export default App;