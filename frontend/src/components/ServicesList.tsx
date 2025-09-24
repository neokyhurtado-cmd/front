import React from 'react'
import ServicesGrid from './ServicesGrid'

export default function ServicesList(){
  return (
    <div>
      <ServicesGrid batchSize={8} intervalMs={60_000} />
    </div>
  )
}
