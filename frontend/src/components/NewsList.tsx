import React, { useEffect, useState } from 'react'
import NewsCard from './NewsCard'
import NewsModal from './NewsModal'

type Item = { id: number; slug?: string | null; title: string; image?: string; excerpt?: string }

export default function NewsList() {
  const [items, setItems] = useState<Item[]>([])
  const [openSlug, setOpenSlug] = useState<string | null>(null)
  const [current, setCurrent] = useState<any | null>(null)
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    fetch('http://127.0.0.1:8095/api/mobility/news')
      .then(r => r.json())
      .then(d => setItems(d.data || []))
      .catch(() => setItems([]))
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
      const url = `http://127.0.0.1:8095/api/news/slug/${encodeURIComponent(clean)}`
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
        setCurrent(data.data)
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
    <div>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
        {items.map(it => (
          <NewsCard key={it.id} id={it.id} slug={it.slug} title={it.title} image={it.image} excerpt={it.excerpt} onRead={(s) => open(s)} />
        ))}
      </div>

      <NewsModal open={!!openSlug} onClose={close} title={current?.title} image={current?.image} content={current?.content || current?.excerpt} />
    </div>
  )
}
