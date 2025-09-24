import React, { useEffect, useState } from 'react'

type Partner = { card_id?: string; name?: string; title?: string; description?: string; image?: string }

export default function PartnersList(){
  const [items, setItems] = useState<Partner[]>([])
  useEffect(()=>{
    const API = (import.meta.env.VITE_API_URL || import.meta.env.VITE_APP_URL || 'http://127.0.0.1:8095') as string
    fetch(`${API}/api/partners`).then(r=>r.json()).then(d=>{ 
      if(Array.isArray(d)){
        const base = API
        const normalized = d.slice(0,8).map((p:any)=>({
          ...p,
          image: p?.image ? ((p.image.startsWith('/img/') || p.image.startsWith('/assets/')) ? p.image : (p.image.startsWith('/') ? `${base}${p.image}` : p.image)) : '/img/placeholder.svg'
        }))
        setItems(normalized)
      }
    }).catch(()=>setItems([]))
  },[])
  return (
    <div>
      <h2 className="text-xl font-semibold mb-4">Alianzas</h2>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {items.map((p,idx)=> (
          <article key={idx} className="bg-white rounded overflow-hidden shadow-md">
            <div className="h-40 bg-gray-100">
              <img src={p.image || '/img/placeholder.svg'} alt={p.title||p.name} className="w-full h-full object-cover" onError={(e:any)=>e.currentTarget.src='/img/placeholder.svg'} />
            </div>
            <div className="p-4">
              <h3 className="font-semibold">{p.title || p.name || p.card_id}</h3>
              <p className="text-sm text-gray-600">{p.description || ''}</p>
            </div>
          </article>
        ))}
      </div>
    </div>
  )
}
