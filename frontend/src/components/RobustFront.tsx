import React, { useEffect, useState } from 'react'

type Paths = { cards?: string; services?: string; news?: string }

function timeoutFetch(url: string, opts: RequestInit = {}, ms = 5000) {
  const controller = new AbortController()
  const id = setTimeout(() => controller.abort(), ms)
  return fetch(url, { ...opts, signal: controller.signal }).finally(() => clearTimeout(id))
}

async function fetchWithRetry(url: string, attempts = 2, delay = 400, ms = 5000) {
  for (let i = 0; i < attempts; i++) {
    try {
      const res = await timeoutFetch(url, {}, ms)
      if (!res.ok) throw new Error(`status ${res.status}`)
      const json = await res.json()
      return json
    } catch (e) {
      if (i === attempts - 1) throw e
      await new Promise(r => setTimeout(r, delay))
    }
  }
}

function normalizeList(d: any): any[] {
  if (!d) return []
  if (Array.isArray(d)) return d
  if (Array.isArray(d.data)) return d.data
  if (Array.isArray(d.items)) return d.items
  if (Array.isArray(d.results)) return d.results
  // try to find a first array
  for (const k of Object.keys(d)) if (Array.isArray(d[k])) return d[k]
  return []
}

export default function RobustFront({ title = 'Portal', baseUrl = '', paths = {} as Paths }: { title?: string; baseUrl?: string; paths?: Paths }) {
  const [cards, setCards] = useState<any[]>([])
  const [services, setServices] = useState<any[]>([])
  const [news, setNews] = useState<any[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    let mounted = true
    async function loadAll() {
      setLoading(true)
      setError(null)
      try {
        const base = baseUrl.replace(/\/$/, '')
        const endpoints = {
          cards: paths.cards ? (paths.cards.startsWith('http') ? paths.cards : base + paths.cards) : undefined,
          services: paths.services ? (paths.services.startsWith('http') ? paths.services : base + paths.services) : undefined,
          news: paths.news ? (paths.news.startsWith('http') ? paths.news : base + paths.news) : undefined
        }

        const [cardsRes, servicesRes, newsRes] = await Promise.all([
          endpoints.cards ? fetchWithRetry(endpoints.cards, 2, 300, 4000).catch(() => null) : Promise.resolve(null),
          endpoints.services ? fetchWithRetry(endpoints.services, 2, 300, 4000).catch(() => null) : Promise.resolve(null),
          endpoints.news ? fetchWithRetry(endpoints.news, 2, 300, 4000).catch(() => null) : Promise.resolve(null)
        ])

        if (!mounted) return
        const c = normalizeList(cardsRes) || []
        const s = normalizeList(servicesRes) || []
        const n = normalizeList(newsRes) || []

        setCards(c)
        setServices(s)
        setNews(n)
        // cache in localStorage for fallback
        try { localStorage.setItem('robust.cards', JSON.stringify(c.slice(0,50))) } catch {}
        try { localStorage.setItem('robust.services', JSON.stringify(s.slice(0,50))) } catch {}
        try { localStorage.setItem('robust.news', JSON.stringify(n.slice(0,50))) } catch {}
      } catch (e: any) {
        setError(String(e?.message || e))
        // try cache
        try { setCards(JSON.parse(localStorage.getItem('robust.cards') || '[]')) } catch {}
        try { setServices(JSON.parse(localStorage.getItem('robust.services') || '[]')) } catch {}
        try { setNews(JSON.parse(localStorage.getItem('robust.news') || '[]')) } catch {}
      } finally {
        setLoading(false)
      }
    }
    loadAll()
    return () => { mounted = false }
  }, [baseUrl, JSON.stringify(paths)])

  return (
    <div className="max-w-6xl mx-auto p-6">
      <h1 className="text-2xl font-bold mb-4">{title}</h1>
      {loading && <div className="p-6 bg-white rounded shadow">Cargando contenido…</div>}
      {error && <div className="p-4 bg-red-50 text-red-700 rounded mb-4">Error: {error}. Se muestran datos en caché si hay.</div>}

      <section className="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div className="col-span-2">
          <h2 className="text-lg font-semibold mb-2">Noticias</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {news.length === 0 && <div className="p-4 bg-gray-50 rounded">Sin noticias</div>}
            {news.map((n,i) => (
              <article key={n.id || n.slug || i} className="bg-white rounded shadow p-4">
                <div className="h-40 bg-gray-100 mb-2 overflow-hidden rounded">
                  <img src={n.image || n.img || n.picture || '/img/placeholder.svg'} alt={n.title || n.titulo || n.name} className="w-full h-full object-cover" onError={(e:any)=>e.currentTarget.src='/img/placeholder.svg'} />
                </div>
                <h3 className="font-semibold">{n.title || n.titulo || n.name}</h3>
                <p className="text-sm text-gray-600">{n.excerpt || n.description || n.descripcion || ''}</p>
              </article>
            ))}
          </div>
        </div>

        <aside>
          <h2 className="text-lg font-semibold mb-2">Servicios</h2>
          <div className="space-y-3">
            {services.length === 0 && <div className="p-3 bg-gray-50 rounded">Sin servicios</div>}
            {services.map((s,i) => (
              <div key={s.id || s.title || i} className="p-3 bg-white rounded shadow">
                <div className="font-semibold">{s.title || s.name || s.titulo}</div>
                <div className="text-sm text-gray-600">{s.desc || s.description || s.descripcion || s.promise || ''}</div>
                <div className="mt-2">
                  <a className="inline-block px-3 py-1 bg-blue-600 text-white rounded" href={`https://wa.me/3058153610?text=${encodeURIComponent('Hola quiero cotizar ' + (s.title || s.name || ''))}`} target="_blank">Cotizar</a>
                </div>
              </div>
            ))}
          </div>
        </aside>
      </section>

      <section>
        <h2 className="text-lg font-semibold mb-2">Tarjetas</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          {cards.length === 0 && <div className="p-4 bg-gray-50 rounded">Sin tarjetas</div>}
          {cards.map((c,i) => (
            <div key={c.id || c.title || i} className="p-4 bg-white rounded shadow">
              <div className="font-semibold">{c.title || c.name || c.titulo}</div>
              <div className="text-sm text-gray-600">{c.desc || c.description || c.descripcion || ''}</div>
            </div>
          ))}
        </div>
      </section>
    </div>
  )
}
