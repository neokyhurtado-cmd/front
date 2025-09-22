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
  function handleClick(e: React.MouseEvent) {
    // ripple
    const btn = e.currentTarget as HTMLButtonElement
    const circle = document.createElement('span')
    const rect = btn.getBoundingClientRect()
    const size = Math.max(rect.width, rect.height)
    circle.style.width = circle.style.height = size + 'px'
    circle.style.left = (e.clientX - rect.left - size / 2) + 'px'
    circle.style.top = (e.clientY - rect.top - size / 2) + 'px'
    circle.className = 'ripple'
    btn.appendChild(circle)
    setTimeout(() => circle.remove(), 500)
    onRead(slug ?? encodeURIComponent(title.replace(/\s+/g,'-').toLowerCase()))
  }

  return (
    <article className="bg-white rounded overflow-hidden shadow-lg relative group">
      <div className="h-48 bg-gray-200">
        {image ? (
          <img src={(image.startsWith('/') ? `http://127.0.0.1:8095${image}` : image)} alt="" className="w-full h-full object-cover" onError={(e) => (e.currentTarget.src = '/img/placeholder.svg')} />
        ) : (
          <div className="w-full h-full flex items-center justify-center text-gray-400">No image</div>
        )}
      </div>
      <div className="p-4">
  <h4 className="font-semibold text-lg mb-2"><a href={`/noticias/${encodeURIComponent((slug ?? title).replace(/\s+/g,'-').toLowerCase())}`} className="hover:underline">{title}</a></h4>
        <p className="text-sm text-gray-600">{excerpt}</p>
        <div className="mt-4 text-right">
          <button onClick={handleClick} className="relative overflow-hidden inline-block px-3 py-1 bg-blue-600 text-white rounded">
            Leer
          </button>
          <a
            onClick={(e) => {
              e.preventDefault()
              const s = encodeURIComponent((slug ?? title).replace(/\s+/g,'-').toLowerCase())
              history.pushState({}, '', `/noticias/${s}`)
              window.dispatchEvent(new PopStateEvent('popstate'))
            }}
            href={`/noticias/${encodeURIComponent((slug ?? title).replace(/\s+/g,'-').toLowerCase())}`}
            className="ml-3 text-sm text-blue-600 hover:underline"
          >
            Leer completa
          </a>
        </div>
      </div>
    </article>
  )
}
