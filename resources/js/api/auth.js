import api from './index';
// Chứa các hàm cụ thể liên quan đến auth
// Giúp component gọi API dễ hiểu hơn
export const login = (email, password) =>
  api.post('/login', { email, password });

export const getUser = () =>
  api.get('/me');

export const logout = () =>
  api.post('/logout');