import { defineConfig } from 'vite';

export default defineConfig(async () => {
  // Load the plugin dynamically to avoid ESM/CJS resolution issues on some setups
  const reactPlugin = (await import('@vitejs/plugin-react')).default;

  return {
    plugins: [reactPlugin()],
    server: {
      port: 3000,
      open: true,
    },
    build: {
      outDir: 'dist',
    },
    resolve: {
      alias: {
        '@': '/src',
      },
    },
  };
});