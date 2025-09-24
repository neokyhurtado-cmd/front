import React, { useEffect, useRef } from 'react'

type Props = {
  open: boolean
  onClose: () => void
  title?: string
  image?: string
  content?: string
}

export default function NewsModal({ open, onClose, title, image, content }: Props) {
  const modalRef = useRef<HTMLDivElement | null>(null)
  const closeBtnRef = useRef<HTMLButtonElement | null>(null)
  const lastActive = useRef<HTMLElement | null>(null)

  useEffect(() => {
    function onKey(e: KeyboardEvent) {
      if (e.key === 'Escape') onClose()
    }

    if (open) {
      // save previous focus and set focus into modal
      lastActive.current = document.activeElement as HTMLElement | null
      document.addEventListener('keydown', onKey)
      // focus the close button after render
      setTimeout(() => closeBtnRef.current?.focus(), 50)
    }

    return () => document.removeEventListener('keydown', onKey)
  }, [open, onClose])

  useEffect(() => {
    if (!open) {
      // restore focus
      lastActive.current?.focus()
    }
  }, [open])

  // focus trap: keep focus inside modal
  function handleTrap(e: React.KeyboardEvent) {
    if (e.key !== 'Tab' || !modalRef.current) return
    const focusable = modalRef.current.querySelectorAll<HTMLElement>(
      'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])'
    )
    if (focusable.length === 0) return
    const first = focusable[0]
    const last = focusable[focusable.length - 1]

    if (e.shiftKey && document.activeElement === first) {
      e.preventDefault()
      last.focus()
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault()
      first.focus()
    }
  }

  if (!open) return null

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center">
      <div className="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" onClick={onClose} />

      <div
        role="dialog"
        aria-modal="true"
        ref={modalRef}
        onKeyDown={handleTrap}
        className="relative max-w-3xl w-full mx-4 bg-white rounded-lg shadow-xl overflow-hidden transform transition-all modal-pop"
      >
        <div className="md:flex">
          {image && (
            <div className="md:w-1/3 bg-gray-100">
                {
                  (() => {
                    const BASE = (import.meta.env.VITE_APP_URL || import.meta.env.VITE_API_URL || 'http://127.0.0.1:8095') as string
                    const src = (image.startsWith('/img/') || image.startsWith('/assets/')) ? image : (image.startsWith('/') ? `${BASE}${image}` : image)
                    return <img src={src} alt="" className="w-full h-48 md:h-full object-cover" onError={(e) => (e.currentTarget.src = '/img/placeholder.svg')} />
                  })()
                }
              </div>
          )}
          <div className="p-6 md:flex-1">
            <h3 className="text-xl font-semibold mb-3">{title}</h3>
            <div className="prose max-w-none text-gray-700">{content}</div>
            <div className="mt-4 text-right">
              <button ref={closeBtnRef} className="px-4 py-2 bg-blue-600 text-white rounded" onClick={onClose}>
                Cerrar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
