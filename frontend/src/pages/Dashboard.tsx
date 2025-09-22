import React from 'react'
import NewsList from '../components/NewsList'

export default function Dashboard() {
  return (
    <div>
      <h2 className="text-2xl font-semibold">Noticias - servicios - alianzas</h2>
      <p className="mt-2 text-gray-600">Lista de noticias de movilidad (demo).</p>
      <div className="mt-6">
        <NewsList />
      </div>
    </div>
  )
}
