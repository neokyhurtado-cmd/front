import { defineConfig } from 'vite'

export default async () => {
  const react = (await import('@vitejs/plugin-react')).default
  return defineConfig({
    plugins: [react()],
    server: {
      host: '127.0.0.1',
      port: 5173,
      proxy: {
        '/api': {
          target: 'http://127.0.0.1:8000',
          changeOrigin: true,
          secure: false,
        },
      },
    },
  })
}
