import React, { useEffect, useState } from 'react'
import NewsCard from './NewsCard'
import NewsModal from './NewsModal'

type Item = { id: number; slug?: string | null; title: string; image?: string; excerpt?: string }

const MOCK_NEWS: Item[] = [
  { id: 1001, slug: 'movilidad-inteligente', title: 'Movilidad inteligente: prueba piloto en la ciudad', image: '/img/placeholder.svg', excerpt: 'Iniciativa para probar rutas inteligentes con sensores en tiempo real.' },
  { id: 1002, slug: 'infraestructura-ciclovias', title: 'Nuevas ciclovías conectarán 8 barrios', image: '/img/placeholder.svg', excerpt: 'Plan de 12 km de ciclovías para conectar zonas residenciales y comerciales.' },
  { id: 1003, slug: 'transporte-publico', title: 'Mejoras en transporte público: más frecuencias', image: '/img/placeholder.svg', excerpt: 'Aumento de frecuencias en horas pico para reducir tiempos de espera.' },
  { id: 1004, slug: 'estacionamiento-inteligente', title: 'Estacionamientos inteligentes se implementan', image: '/img/placeholder.svg', excerpt: 'Sensores ayudan a encontrar lugar disponible y pagar con la app.' },
  { id: 1005, slug: 'electromovilidad', title: 'Electromovilidad: estaciones de carga en crecimiento', image: '/img/placeholder.svg', excerpt: 'Se inauguran 20 estaciones de carga rápida en puntos estratégicos.' },
  { id: 1006, slug: 'seguridad-vial', title: 'Campaña de seguridad vial para conductores', image: '/img/placeholder.svg', excerpt: 'Educación y campañas para reducir siniestros en intersecciones.' },
  { id: 1007, slug: 'datos-abiertos', title: 'Portal de datos abiertos con información de movilidad', image: '/img/placeholder.svg', excerpt: 'Dataset con viajes y tiempos que permitirá nuevas aplicaciones.' },
  { id: 1008, slug: 'plan-peatonal', title: 'Plan peatonal: calles céntricas con prioridad peatonal', image: '/img/placeholder.svg', excerpt: 'Zonas del centro se transforman para priorizar caminantes.' }
]

export default function NewsList() {
  const [items, setItems] = useState<Item[]>([])
  const [openSlug, setOpenSlug] = useState<string | null>(null)
  const [current, setCurrent] = useState<any | null>(null)
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    const API = (import.meta.env.VITE_API_URL || import.meta.env.VITE_APP_URL || 'http://127.0.0.1:8095') as string
    setLoading(true)
    fetch(`${API}/api/mobility/news`)
      .then(r => r.json())
      .then(d => {
        const got = d?.data || []
        if (Array.isArray(got) && got.length > 0) {
          const base = (import.meta.env.VITE_APP_URL || import.meta.env.VITE_API_URL || 'http://127.0.0.1:8095') as string
          const normalized = got.map((it: any) => ({
            ...it,
            image: (it.image && (it.image.startsWith('/img/') || it.image.startsWith('/assets/'))) ? it.image : (it.image && it.image.startsWith('/') ? `${base}${it.image}` : it.image)
          }))
          setItems(normalized)
        } else setItems(MOCK_NEWS)
      })
      .catch(() => setItems(MOCK_NEWS))
      .finally(() => setLoading(false))
  }, [])

  useEffect(() => {
    function onPop() {
      const m = window.location.pathname.match(/\/noticias\/(.+)$/)
      const slug = m ? decodeURIComponent(m[1]) : null
      if (slug) openBySlug(slug)
      else close()
    }
    window.addEventListener('popstate', onPop)
    // on mount check if url has slug
    onPop()
    return () => window.removeEventListener('popstate', onPop)
  }, [items])

  async function openBySlug(slug: string) {
    // normalize slug to avoid double-encoding issues
    let clean = slug || ''
    try {
      clean = decodeURIComponent(clean)
    } catch (er) {
      // ignore decode errors
    }

    setOpenSlug(clean)
    setLoading(true)
    try {
      const API = (import.meta.env.VITE_API_URL || import.meta.env.VITE_APP_URL || 'http://127.0.0.1:8095') as string
      const url = `${API}/api/news/slug/${encodeURIComponent(clean)}`
      console.debug('[NewsList] Fetching detail:', url)
      const res = await fetch(url, { headers: { Accept: 'application/json' } })
      if (!res.ok) {
        console.error('[NewsList] detail fetch failed', res.status)
        setCurrent({ title: 'Error', content: 'No se pudo cargar la noticia (detalle no encontrado).' })
        return
      }
      const data = await res.json()
      if (!data || !data.data) {
        setCurrent({ title: 'Sin contenido', content: 'No se ha encontrado contenido para esta noticia.' })
      } else {
        const base = API
        const detail = data.data
        if (detail && detail.image) {
          const img = String(detail.image)
          detail.image = (img.startsWith('/img/') || img.startsWith('/assets/')) ? img : (img.startsWith('/') ? `${base}${img}` : img)
        }
        setCurrent(detail)
      }
    } catch (e) {
      console.error('[NewsList] openBySlug error', e)
      setCurrent({ title: 'Error', content: 'Error cargando la noticia: ' + String(e) })
    } finally {
      setLoading(false)
    }
  }

  function open(slug: string | null) {
    if (!slug) return
    history.pushState({}, '', `/noticias/${encodeURIComponent(slug)}`)
    openBySlug(slug)
  }

  function close() {
    setOpenSlug(null)
    setCurrent(null)
    // return to root path
    history.replaceState({}, '', '/')
  }

  return (
    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 className="text-2xl font-bold mb-4">Noticias</h1>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <aside className="lg:col-span-3 space-y-4 lg:sticky lg:top-16 self-start">
          <div className="bg-white rounded-lg p-4 shadow-sm">
            <h2 className="text-base font-semibold">Categorías</h2>
            <div className="mt-3 space-y-2 text-sm text-gray-600">{/* example items */}
              <div className="inline-flex gap-2 flex-wrap"><span className="px-2 py-1 bg-gray-100 rounded">Movilidad</span> <span className="px-2 py-1 bg-gray-100 rounded">Transporte</span></div>
            </div>
          </div>
        </aside>

        <main className="lg:col-span-6 space-y-6">
          {loading ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
              {Array.from({ length: 6 }).map((_, i) => (
                <div key={i} className="bg-white rounded-2xl border shadow-sm p-0 overflow-hidden">
                  <div className="aspect-[16/9] bg-gray-200 animate-pulse" />
                  <div className="p-4">
                    <div className="h-4 bg-gray-200 rounded w-3/4 animate-pulse" />
                    <div className="mt-2 h-4 bg-gray-200 rounded w-5/6 animate-pulse" />
                    <div className="mt-4 h-8 w-24 bg-gray-200 rounded animate-pulse" />
                  </div>
                </div>
              ))}
            </div>
          ) : items.length === 0 ? (
            <div className="bg-white rounded-lg p-6 text-center text-gray-700">No hay noticias disponibles. <button onClick={() => window.location.reload()} className="ml-3 px-3 py-1 bg-blue-600 text-white rounded">Reintentar</button></div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
              {items.map(it => (
                <NewsCard key={it.id} id={it.id} slug={it.slug} title={it.title} image={it.image} excerpt={it.excerpt} onRead={(s) => open(s)} />
              ))}
            </div>
          )}
        </main>

        <aside className="lg:col-span-3 space-y-4 lg:sticky lg:top-16 self-start">
          <div className="bg-white rounded-lg p-4 shadow-sm">
            <h2 className="text-base font-semibold">Recientes</h2>
            <ul className="mt-3 text-sm text-gray-600 space-y-2">
              {items.slice(0,5).map(i => (
                <li key={i.id} className="truncate"><a className="hover:underline" href="#" onClick={(e)=>{e.preventDefault(); open(i.slug ?? i.title)}}>{i.title}</a></li>
              ))}
            </ul>
          </div>
        </aside>
      </div>

      <NewsModal open={!!openSlug} onClose={close} title={current?.title} image={current?.image} content={current?.content || current?.excerpt} />
    </div>
  )
}
