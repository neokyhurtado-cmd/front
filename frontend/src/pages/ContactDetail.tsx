import React from 'react'
import { useParams } from 'react-router-dom'

export default function ContactDetail() {
  const { id } = useParams()
  return (
    <div>
      <h2 className="text-2xl font-semibold">Contacto {id}</h2>
      <p className="mt-2 text-gray-600">Detalle del contacto (placeholder).</p>
    </div>
  )
}
