import React, { useEffect, useRef, useState } from 'react'
import { SERVICES, Service } from '../data/services'

type Props = {
  batchSize?: number
  intervalMs?: number
}

export default function ServicesGrid({ batchSize = 8, intervalMs = 60_000 }: Props) {
  const services = SERVICES
  const total = services.length

  // visible IDs (keeps order)
  const [visibleIds, setVisibleIds] = useState<number[]>(() => services.slice(0, batchSize).map(s => s.id))
  const cursorRef = useRef(batchSize % total)

  // locked IDs (user reading via hover/focus)
  const lockedRef = useRef(new Set<number>())

  useEffect(() => {
    const t = setInterval(() => {
      // compute next visible set: keep locked ids that are currently visible, fill rest from services starting at cursor
      setVisibleIds(prev => {
        const next: number[] = []
        const prevSet = new Set(prev)
        // keep locked ids that are visible
        for (const id of prev) {
          if (lockedRef.current.has(id)) next.push(id)
        }
        // fill remaining slots
        let attempts = 0
        let cursor = cursorRef.current
        while (next.length < batchSize && attempts < total) {
          const candidate = services[cursor % total].id
          if (!next.includes(candidate) && !prevSet.has(candidate)) {
            next.push(candidate)
          } else if (!next.includes(candidate) && prevSet.has(candidate) && !lockedRef.current.has(candidate)) {
            // candidate is currently visible but not locked; allow rotation by replacing it
            next.push(candidate)
          }
          cursor = (cursor + 1) % total
          attempts++
        }
        cursorRef.current = cursor
        // if still short, pad with any services not already included
        for (let i = 0; next.length < batchSize && i < total; i++) {
          const id = services[i].id
          if (!next.includes(id)) next.push(id)
        }
        return next
      })
    }, intervalMs)
    return () => clearInterval(t)
  }, [batchSize, intervalMs, services, total])

  function handleEnter(id: number) { lockedRef.current.add(id) }
  function handleLeave(id: number) { lockedRef.current.delete(id) }
  function handleFocus(id: number) { lockedRef.current.add(id) }
  function handleBlur(id: number) { lockedRef.current.delete(id) }

  const visible = visibleIds.map(id => services.find(s => s.id === id)!).filter(Boolean)

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 grid-container">
      {visible.map((s) => {
        const iconMap: Record<number,string> = {
          1: 'fa-thin fa-traffic-light-signal',
          2: 'fa-thin fa-robot',
          3: 'fa-thin fa-mobile-screen',
          4: 'fa-thin fa-hard-hat',
          5: 'fa-thin fa-video',
          6: 'fa-thin fa-map-pin',
          7: 'fa-thin fa-sitemap',
          8: 'fa-thin fa-comments',
          9: 'fa-thin fa-clipboard-list',
          10: 'fa-thin fa-road',
        }
        const iconClass = iconMap[s.id] || 'fa-solid fa-tools'
        return (
          <article key={s.id} className="services-card" onMouseEnter={() => handleEnter(s.id)} onMouseLeave={() => handleLeave(s.id)} onFocus={() => handleFocus(s.id)} onBlur={() => handleBlur(s.id)} tabIndex={0}>
            <div className="services-title">{s.title}</div>
            <div className="services-content">
              <p className="services-card-desc">{s.desc || s.promise}</p>
              <ul className="services-card-info">
                <li><strong>Incluye:</strong> {s.incluye.join(' • ')}</li>
                <li><strong>Requisitos:</strong> {s.entrada.join(' • ')}</li>
                <li><strong>Entrega:</strong> {s.entrega}</li>
              </ul>
            </div>
            <div>
              <button
                className="services-cta"
                onClick={() => {
                  const url = `https://wa.me/3058153610?text=${encodeURIComponent('Hola quiero cotizar ' + s.title)}`
                  window.open(url, '_blank')
                }}
              >
                Cotizar
              </button>
            </div>
          </article>
        )
      })}
    </div>
  )
}
