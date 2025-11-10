import axios from 'axios';

// Tạo instance axios với cấu hình cơ bản
const api = axios.create({
  baseURL: '/api',
  headers: { 'Content-Type': 'application/json' }
});

// Thêm token tự động nếu có
//Tạo axios instance có cấu hình sẵn (baseURL: '/api', header JSON,...).
//Thêm interceptor: trước khi gửi request, nó tự động thêm Authorization: Bearer <token> nếu user đã đăng nhập.
//=> Mọi API đều được bảo vệ tự động, không cần thêm header thủ công.

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

export default api;