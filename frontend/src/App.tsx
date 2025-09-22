import React from 'react'
import { BrowserRouter, Routes, Route } from 'react-router-dom'
import DashboardLayout from './layouts/DashboardLayout'
import NewsPage from './pages/NewsPage'

export default function App() {
  return (
    <BrowserRouter>
      <DashboardLayout>
        <Routes>
          <Route path="/" element={<NewsPage />} />
          <Route path="/noticias/:slug" element={<NewsPage />} />
        </Routes>
      </DashboardLayout>
    </BrowserRouter>
  )
}
