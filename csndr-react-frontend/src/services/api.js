import axios from 'axios';

const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api', // aligné avec php artisan serve
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Intercepteur pour ajouter le token si disponible
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, error => Promise.reject(error));

export const login = (credentials) => api.post('/auth/login', credentials);
export const logout = () => api.post('/auth/logout');
export const getUsers = () => api.get('/users');
export const saveUser = (data) => api.post('/users', data);
export const deleteUser = (id) => api.delete(`/users/${id}`);
export const getClasses = () => api.get('/classes');
export const saveClass = (data) => api.post('/classes', data);
export const deleteClass = (id) => api.delete(`/classes/${id}`);
// Ajouter d'autres endpoints pour devoirs, notes, événements, messages...

export default api;