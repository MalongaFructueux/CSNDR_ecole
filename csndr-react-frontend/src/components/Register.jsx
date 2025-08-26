import React, { useState, useEffect } from 'react';
import { User, Mail, Lock, UserCheck, School, Users } from 'lucide-react';
import api from '../services/api';

/**
 * Composant d'inscription pour nouveaux utilisateurs
 * 
 * Ce composant gère :
 * - L'inscription des parents et élèves
 * - La validation des données en temps réel
 * - La vérification de disponibilité des emails
 * - La sélection des classes et parents pour les élèves
 * - L'authentification automatique après inscription
 */
const Register = ({ onRegister }) => {
  // États du formulaire
  const [formData, setFormData] = useState({
    nom: '',
    prenom: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'parent',
    classe_id: '',
    parent_id: ''
  });

  // États de l'interface
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState({});
  const [emailChecking, setEmailChecking] = useState(false);
  const [emailAvailable, setEmailAvailable] = useState(null);
  
  // Données pour les sélections
  const [classes, setClasses] = useState([]);
  const [parents, setParents] = useState([]);

  /**
   * Chargement des données nécessaires au montage
   */
  useEffect(() => {
    loadAvailableData();
  }, []);

  /**
   * Vérification de l'email en temps réel
   */
  useEffect(() => {
    if (formData.email && formData.email.includes('@')) {
      checkEmailAvailability();
    }
  }, [formData.email]);

  /**
   * Chargement des classes et parents disponibles
   */
  const loadAvailableData = async () => {
    try {
      const [classesRes, parentsRes] = await Promise.all([
        api.get('/auth/available-classes'),
        api.get('/auth/available-parents')
      ]);

      setClasses(classesRes.data || []);
      setParents(parentsRes.data || []);
    } catch (error) {
      console.error('Erreur lors du chargement des données:', error);
    }
  };

  /**
   * Vérification de la disponibilité de l'email
   */
  const checkEmailAvailability = async () => {
    setEmailChecking(true);
    try {
      const response = await api.post('/auth/check-email', { email: formData.email });
      setEmailAvailable(response.available);
      
      if (!response.available) {
        setErrors(prev => ({ ...prev, email: response.message }));
      } else {
        setErrors(prev => ({ ...prev, email: '' }));
      }
    } catch (error) {
      console.error('Erreur lors de la vérification de l\'email:', error);
    } finally {
      setEmailChecking(false);
    }
  };

  /**
   * Gestion des changements dans le formulaire
   */
  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));

    // Réinitialiser les erreurs pour ce champ
    if (errors[name]) {
      setErrors(prev => ({ ...prev, [name]: '' }));
    }

    // Réinitialiser les champs dépendants lors du changement de rôle
    if (name === 'role') {
      setFormData(prev => ({
        ...prev,
        classe_id: '',
        parent_id: ''
      }));
    }
  };

  /**
   * Validation du formulaire
   */
  const validateForm = () => {
    const newErrors = {};

    if (!formData.nom.trim()) newErrors.nom = 'Le nom est requis';
    if (!formData.prenom.trim()) newErrors.prenom = 'Le prénom est requis';
    if (!formData.email.trim()) newErrors.email = 'L\'email est requis';
    if (!formData.password) newErrors.password = 'Le mot de passe est requis';
    if (formData.password.length < 6) newErrors.password = 'Le mot de passe doit contenir au moins 6 caractères';
    if (formData.password !== formData.password_confirmation) {
      newErrors.password_confirmation = 'Les mots de passe ne correspondent pas';
    }
    if (!emailAvailable) newErrors.email = 'Cet email n\'est pas disponible';

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  /**
   * Soumission du formulaire
   */
  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (!validateForm()) return;

    setLoading(true);
    try {
      const response = await api.post('/auth/register', formData);

      // Inscription réussie, appeler la fonction de callback
      await onRegister({
        user: response.user,
        message: response.message
      });
    } catch (error) {
      console.error('Erreur lors de l\'inscription:', error);
      // Gérer les erreurs de validation du serveur
      if (error.response?.errors) {
        setErrors(error.response.errors);
      } else {
        setErrors({ general: error.response?.message || error.message || 'Erreur lors de l\'inscription' });
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-md w-full space-y-8">
        {/* En-tête */}
        <div className="text-center">
          <div className="mx-auto h-12 w-12 bg-primary-600 rounded-full flex items-center justify-center">
            <UserCheck className="h-6 w-6 text-white" />
          </div>
          <h2 className="mt-6 text-3xl font-extrabold text-gray-900">
            Créer un compte
          </h2>
          <p className="mt-2 text-sm text-gray-600">
            Centre Scolaire Notre Dame du Rosaire
          </p>
        </div>

        {/* Formulaire */}
        <form className="mt-8 space-y-6" onSubmit={handleSubmit}>
          {/* Erreur générale */}
          {errors.general && (
            <div className="bg-red-50 border border-red-200 rounded-md p-4">
              <p className="text-sm text-red-600">{errors.general}</p>
            </div>
          )}

          <div className="space-y-4">
            {/* Sélection du rôle */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Type de compte
              </label>
              <div className="grid grid-cols-2 gap-3">
                <button
                  type="button"
                  onClick={() => handleChange({ target: { name: 'role', value: 'parent' } })}
                  className={`p-3 border rounded-lg text-sm font-medium transition-colors ${
                    formData.role === 'parent'
                      ? 'border-primary-500 bg-primary-50 text-primary-700'
                      : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                  }`}
                >
                  <Users className="h-4 w-4 mx-auto mb-1" />
                  Parent
                </button>
                <button
                  type="button"
                  onClick={() => handleChange({ target: { name: 'role', value: 'eleve' } })}
                  className={`p-3 border rounded-lg text-sm font-medium transition-colors ${
                    formData.role === 'eleve'
                      ? 'border-primary-500 bg-primary-50 text-primary-700'
                      : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                  }`}
                >
                  <School className="h-4 w-4 mx-auto mb-1" />
                  Élève
                </button>
              </div>
            </div>

            {/* Nom et Prénom */}
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label htmlFor="nom" className="block text-sm font-medium text-gray-700">
                  Nom
                </label>
                <div className="mt-1 relative">
                  <input
                    id="nom"
                    name="nom"
                    type="text"
                    required
                    value={formData.nom}
                    onChange={handleChange}
                    className={`appearance-none relative block w-full px-3 py-2 pl-10 border ${
                      errors.nom ? 'border-red-300' : 'border-gray-300'
                    } placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm`}
                    placeholder="Nom"
                  />
                  <User className="h-5 w-5 text-gray-400 absolute left-3 top-2.5" />
                </div>
                {errors.nom && <p className="mt-1 text-xs text-red-600">{errors.nom}</p>}
              </div>

              <div>
                <label htmlFor="prenom" className="block text-sm font-medium text-gray-700">
                  Prénom
                </label>
                <div className="mt-1 relative">
                  <input
                    id="prenom"
                    name="prenom"
                    type="text"
                    required
                    value={formData.prenom}
                    onChange={handleChange}
                    className={`appearance-none relative block w-full px-3 py-2 pl-10 border ${
                      errors.prenom ? 'border-red-300' : 'border-gray-300'
                    } placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm`}
                    placeholder="Prénom"
                  />
                  <User className="h-5 w-5 text-gray-400 absolute left-3 top-2.5" />
                </div>
                {errors.prenom && <p className="mt-1 text-xs text-red-600">{errors.prenom}</p>}
              </div>
            </div>

            {/* Email */}
            <div>
              <label htmlFor="email" className="block text-sm font-medium text-gray-700">
                Adresse email
              </label>
              <div className="mt-1 relative">
                <input
                  id="email"
                  name="email"
                  type="email"
                  required
                  value={formData.email}
                  onChange={handleChange}
                  className={`appearance-none relative block w-full px-3 py-2 pl-10 pr-10 border ${
                    errors.email ? 'border-red-300' : emailAvailable === false ? 'border-red-300' : emailAvailable === true ? 'border-green-300' : 'border-gray-300'
                  } placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm`}
                  placeholder="email@exemple.com"
                />
                <Mail className="h-5 w-5 text-gray-400 absolute left-3 top-2.5" />
                {emailChecking && (
                  <div className="absolute right-3 top-2.5">
                    <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
                  </div>
                )}
                {!emailChecking && emailAvailable === true && (
                  <div className="absolute right-3 top-2.5 text-green-500">✓</div>
                )}
                {!emailChecking && emailAvailable === false && (
                  <div className="absolute right-3 top-2.5 text-red-500">✗</div>
                )}
              </div>
              {errors.email && <p className="mt-1 text-xs text-red-600">{errors.email}</p>}
            </div>

            {/* Mots de passe */}
            <div>
              <label htmlFor="password" className="block text-sm font-medium text-gray-700">
                Mot de passe
              </label>
              <div className="mt-1 relative">
                <input
                  id="password"
                  name="password"
                  type="password"
                  required
                  value={formData.password}
                  onChange={handleChange}
                  className={`appearance-none relative block w-full px-3 py-2 pl-10 border ${
                    errors.password ? 'border-red-300' : 'border-gray-300'
                  } placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm`}
                  placeholder="Mot de passe"
                />
                <Lock className="h-5 w-5 text-gray-400 absolute left-3 top-2.5" />
              </div>
              {errors.password && <p className="mt-1 text-xs text-red-600">{errors.password}</p>}
            </div>

            <div>
              <label htmlFor="password_confirmation" className="block text-sm font-medium text-gray-700">
                Confirmer le mot de passe
              </label>
              <div className="mt-1 relative">
                <input
                  id="password_confirmation"
                  name="password_confirmation"
                  type="password"
                  required
                  value={formData.password_confirmation}
                  onChange={handleChange}
                  className={`appearance-none relative block w-full px-3 py-2 pl-10 border ${
                    errors.password_confirmation ? 'border-red-300' : 'border-gray-300'
                  } placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm`}
                  placeholder="Confirmer le mot de passe"
                />
                <Lock className="h-5 w-5 text-gray-400 absolute left-3 top-2.5" />
              </div>
              {errors.password_confirmation && <p className="mt-1 text-xs text-red-600">{errors.password_confirmation}</p>}
            </div>

            {/* Champs spécifiques aux élèves */}
            {formData.role === 'eleve' && (
              <>
                <div>
                  <label htmlFor="classe_id" className="block text-sm font-medium text-gray-700">
                    Classe
                  </label>
                  <select
                    id="classe_id"
                    name="classe_id"
                    value={formData.classe_id}
                    onChange={handleChange}
                    className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  >
                    <option value="">Sélectionner une classe</option>
                    {classes.map(classe => (
                      <option key={classe.id} value={classe.id}>
                        {classe.nom}
                      </option>
                    ))}
                  </select>
                </div>

                <div>
                  <label htmlFor="parent_id" className="block text-sm font-medium text-gray-700">
                    Parent (optionnel)
                  </label>
                  <select
                    id="parent_id"
                    name="parent_id"
                    value={formData.parent_id}
                    onChange={handleChange}
                    className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                  >
                    <option value="">Aucun parent spécifique</option>
                    {parents.map(parent => (
                      <option key={parent.id} value={parent.id}>
                        {parent.prenom} {parent.nom} ({parent.email})
                      </option>
                    ))}
                  </select>
                  <p className="mt-1 text-xs text-gray-500">
                    Si aucun parent n'est sélectionné, un parent sera assigné automatiquement
                  </p>
                </div>
              </>
            )}
          </div>

          {/* Bouton de soumission */}
          <div>
            <button
              type="submit"
              disabled={loading || emailChecking || !emailAvailable}
              className="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {loading ? (
                <div className="flex items-center">
                  <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                  Inscription en cours...
                </div>
              ) : (
                'S\'inscrire'
              )}
            </button>
          </div>

          {/* Lien vers la connexion */}
          <div className="text-center">
            <p className="text-sm text-gray-600">
              Vous avez déjà un compte ?{' '}
              <a
                href="/login"
                className="font-medium text-primary-600 hover:text-primary-500"
              >
                Se connecter
              </a>
            </p>
          </div>
        </form>
      </div>
    </div>
  );
};

export default Register;
