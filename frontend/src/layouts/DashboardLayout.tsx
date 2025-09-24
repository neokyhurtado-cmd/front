import React from 'react'
import { Link, useLocation } from 'react-router-dom'

const DashboardLayout: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const loc = useLocation()
  const isHome = loc.pathname === '/' || loc.pathname === ''

  return (
    <div className="min-h-screen bg-gray-100 text-gray-900 flex flex-col">
      {!isHome && (
        <header className="bg-white shadow p-4 flex items-center justify-between">
          <h1 className="text-xl font-bold">CRM — Panel</h1>
          <div className="text-sm text-gray-600">Usuario</div>
        </header>
      )}
      <div className="flex flex-1">
        {!isHome && (
          <aside className="w-64 bg-gray-50 border-r p-4">
            <nav className="space-y-2">
              <Link to="/" className="block py-2 px-3 rounded hover:bg-gray-100">Dashboard</Link>
              <Link to="/contacts" className="block py-2 px-3 rounded hover:bg-gray-100">Contactos</Link>
              <Link to="/deals" className="block py-2 px-3 rounded hover:bg-gray-100">Negocios</Link>
              <Link to="/settings" className="block py-2 px-3 rounded hover:bg-gray-100">Ajustes</Link>
            </nav>
          </aside>
        )}
        <main className={`${isHome ? 'w-full' : 'flex-1'} p-6 container`}>{children}</main>
      </div>
    </div>
  )
}

export default DashboardLayout
