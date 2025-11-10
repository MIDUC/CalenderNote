import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.js', 'resources/css/app.css'],
      refresh: true,
    }),
    vue(),
  ],
  server: {
    port: 3000,
    host: 'localhost', // hoặc '0.0.0.0' nếu muốn truy cập từ mạng LAN
  },
})
