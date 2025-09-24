import React from 'react'

type Props = {
  id: number
  slug?: string | null
  title: string
  image?: string
  excerpt?: string
  onRead: (slug: string | null) => void
}

export default function NewsCard({ id, slug, title, image, excerpt, onRead }: Props) {
  const BASE = (import.meta.env.VITE_APP_URL || import.meta.env.VITE_API_URL || 'http://127.0.0.1:8095') as string
  function normalizeImage(src?: string) {
    if (!src) return '/img/placeholder.svg'
    if (src.startsWith('/img/') || src.startsWith('/assets/')) return src
    if (src.startsWith('/')) return `${BASE}${src}`
    return src
  }

  function handleRead(e?: React.MouseEvent) {
    onRead(slug ?? encodeURIComponent(title.replace(/\s+/g,'-').toLowerCase()))
  }

  return (
    <article className="bg-white rounded-2xl border shadow-sm hover:shadow-md transition p-0 overflow-hidden" aria-labelledby={`news-${id}-title`}>
      <div className="aspect-[16/9] bg-gray-200">
        {image ? (
          <img src={normalizeImage(image)} alt={title} className="w-full h-full object-cover" onError={(e) => (e.currentTarget.src = '/img/placeholder.svg')} />
        ) : (
          <div className="w-full h-full flex items-center justify-center text-gray-400">No image</div>
        )}
      </div>

      <div className="p-4">
        <h3 id={`news-${id}-title`} className="text-lg font-semibold leading-tight line-clamp-2">{title}</h3>
        <p className="mt-2 text-sm text-gray-600 line-clamp-2" dangerouslySetInnerHTML={{ __html: excerpt ?? '' }} />
        <div className="mt-3 flex items-center gap-3">
          <button aria-label={`Leer ${title}`} onClick={handleRead} className="inline-flex items-center px-3 py-1.5 text-sm rounded-lg bg-blue-600 text-white hover:bg-blue-700">Leer</button>
          <a
            onClick={(e) => {
              e.preventDefault()
              const s = encodeURIComponent((slug ?? title).replace(/\s+/g,'-').toLowerCase())
              history.pushState({}, '', `/noticias/${s}`)
              window.dispatchEvent(new PopStateEvent('popstate'))
            }}
            href={`/noticias/${encodeURIComponent((slug ?? title).replace(/\s+/g,'-').toLowerCase())}`}
            className="text-sm underline text-gray-700"
          >
            Leer completa
          </a>
          <time className="ml-auto text-xs text-gray-500" dateTime="">{/* optional date if available */}</time>
        </div>
      </div>
    </article>
  )
}
