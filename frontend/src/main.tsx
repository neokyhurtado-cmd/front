import React from 'react'
import { createRoot } from 'react-dom/client'
import App from './App'
import RobustFront from './components/RobustFront'
import './index.css'
import './styles/cards.css'

createRoot(document.getElementById('root') as HTMLElement).render(
  <React.StrictMode>
  <RobustFront title="Portal" baseUrl={(import.meta.env.VITE_BASE_URL || '') as string} paths={{ cards: '/api/cards', services: '/api/services', news: '/api/mobility/news' }} />
  </React.StrictMode>
)
