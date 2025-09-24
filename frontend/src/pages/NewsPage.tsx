import React from 'react'
import NewsList from '../components/NewsList'
import ServicesList from '../components/ServicesList'
import PartnersList from '../components/PartnersList'

export default function NewsPage() {
  return (
    <div className="container p-4">
      <div className="flex items-center justify-between mb-4">
        <h1 className="text-xl font-semibold text-center w-full">Noticias - servicios - alianzas</h1>
      </div>

      <div className="mb-4 flex justify-center">
        <nav className="inline-flex bg-white shadow rounded">
          {/* The tabs inside NewsList will handle filtering; keep this header minimal */}
        </nav>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <aside className="hidden lg:block lg:col-span-2">
          <div className="sticky top-4 space-y-4">
            <div className="p-4 bg-white shadow rounded">
              <h3 className="font-semibold mb-2">Información</h3>
              <p className="text-sm text-gray-600">Panel con datos de la aplicación, precios y FAQs.</p>
            </div>
            <div className="p-4 bg-white shadow rounded">
              <h4 className="font-medium">Categorías</h4>
              <ul className="mt-2 text-sm text-gray-600 space-y-1">
                <li><a href="#" className="text-blue-600">Movilidad</a></li>
                <li><a href="#" className="text-blue-600">Transporte</a></li>
                <li><a href="#" className="text-blue-600">Política</a></li>
              </ul>
            </div>
          </div>
        </aside>

        <main className="col-span-1 lg:col-span-8">
          <NewsList />
          <div className="mt-8">
            <h2 className="text-lg font-semibold mb-4">Servicios</h2>
            <ServicesList />
          </div>
        </main>

        <aside className="hidden lg:block lg:col-span-2">
          <div className="sticky top-4 space-y-4">
            <div className="p-4 bg-white shadow rounded">
              <h3 className="font-semibold mb-2">Panel Derecho</h3>
              <p className="text-sm text-gray-600">Widgets, anuncios o contenido contextual (precios, contacto).</p>
            </div>
            <div className="p-4 bg-white shadow rounded">
              <h4 className="font-medium">Recientes</h4>
              <ul className="mt-2 text-sm text-gray-600 space-y-1">
                <li><a href="#" className="text-blue-600">Noticia reciente A</a></li>
                <li><a href="#" className="text-blue-600">Noticia reciente B</a></li>
              </ul>
            </div>
            <div className="mt-4">
              <PartnersList />
            </div>
          </div>
        </aside>
      </div>

      <footer className="bg-white border-t mt-8">
        <div className="max-w-6xl mx-auto p-6 flex flex-col md:flex-row items-start md:items-center justify-between">
          <div>
            <h4 className="font-semibold">Contacto</h4>
            <p className="text-sm text-gray-600">Email: <a href="mailto:tu@correo.com" className="text-blue-600">tu@correo.com</a> · Tel: <a href="tel:+34123456789" className="text-blue-600">+34 123 456 789</a></p>
          </div>
          <div className="mt-4 md:mt-0 text-sm text-gray-500">© {new Date().getFullYear()} Tu Nombre. Todos los derechos reservados.</div>
        </div>
      </footer>
    </div>
  )
}
