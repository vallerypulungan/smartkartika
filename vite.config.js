import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    vue(),
    laravel({
      input: ['resources/js/app.js'], // sesuai file Vue utama kamu
      refresh: true,
    }),
  ],
  resolve: {
    alias: {
      '@': '/resources/js', // agar bisa import '@/components/xxx.vue'
    },
  },
});
