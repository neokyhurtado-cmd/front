import React, { useEffect, useState } from 'react';

type Kpi = { label: string; value: string };
type Step = { id: number; title: string; desc: string };

type Props = {
  title?: string;
  subtitle?: string;
  kpis?: Kpi[];
  steps?: Step[];
  integrations?: string[];
  ctaHref?: string;
  ctaText?: string;
};

export default function AllianceStepper({
  title = 'Cómo operamos con aliados',
  subtitle = 'Proceso claro, avisos automáticos y trazabilidad total.',
  kpis = [
    { label: 'Aprobaciones', value: '+95%' },
    { label: 'TME cotización', value: '<24h' },
    { label: 'Arranque campo', value: '48–72h' }
  ],
  steps = [
    { id: 1, title: 'Alta de aliado', desc: 'Formulario + docs. Activación en 24–48h.' },
    { id: 2, title: 'Kit de inicio', desc: 'Plantillas, precios y canal directo con UTM.' },
    { id: 3, title: 'Solicitud', desc: 'Panel / WhatsApp / Email. Plantillas por obra/evento.' },
    { id: 4, title: 'Cronograma', desc: 'Hitos, entregables, fechas y aprobaciones.' },
    { id: 5, title: 'Ejecución', desc: 'Evidencias, fotos, actas e informes descargables.' },
    { id: 6, title: 'Cierre', desc: 'NPS, factura consolidada y programación recurrente.' }
  ],
  integrations = ['Calendario compartido', 'Carpeta de entregables', 'Tablero de estado'],
  ctaHref = '#agenda-demo',
  ctaText = 'Ver demo de autogestión'
}: Props) {
  const [progress, setProgress] = useState(0);

  useEffect(() => {
    // animate progress line to full width on mount
    const t = setTimeout(() => setProgress(100), 100);
    return () => clearTimeout(t);
  }, []);

  const icons: Record<number, string> = {
    1: '📝',
    2: '📦',
    3: '📩',
    4: '📅',
    5: '📸',
    6: '✅'
  };
  return (
    <section className="mx-auto max-w-6xl p-4 md:p-8">
      <div className="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
        <div>
          <h2 className="text-2xl md:text-3xl font-semibold tracking-tight">{title}</h2>
          <p className="text-slate-600 mt-1">{subtitle}</p>
        </div>
        <div className="grid grid-cols-3 gap-3">
          {kpis.map((k, idx) => (
            <div key={idx} className="rounded-2xl border border-slate-200 p-3 text-center">
              <div className="text-xs uppercase text-slate-500">{k.label}</div>
              <div className="text-xl font-bold">{k.value}</div>
            </div>
          ))}
        </div>
      </div>

      <ol className="relative md:flex md:items-center md:justify-between">
        {/* background line */}
        <div className="hidden md:block absolute left-0 right-0 h-0.5 bg-slate-200 top-5"></div>
        {/* animated progress overlay */}
        <div className="hidden md:block absolute left-0 top-5 h-0.5 bg-blue-600" style={{ width: `${progress}%`, transition: 'width 900ms ease-out' }}></div>
        {steps.map((s) => (
          <li key={s.id} className="relative mb-8 md:mb-0 md:flex-1">
            <div className="flex items-start md:flex-col md:items-center gap-3">
              <span className="shrink-0 w-10 h-10 rounded-full bg-blue-600 text-white grid place-content-center font-bold ring-8 ring-white shadow-md">{icons[s.id] || s.id}</span>
              <div className="md:text-center">
                <h3 className="font-semibold">{s.title}</h3>
                <p className="text-sm text-slate-600">{s.desc}</p>
              </div>
            </div>
          </li>
        ))}
      </ol>

      <div className="mt-6 flex flex-wrap gap-2">
        {integrations.map((i, idx) => (
          <span key={idx} className="px-3 py-1 rounded-full border text-sm text-slate-700">{i}</span>
        ))}
      </div>

      <div className="mt-6">
        <a href={ctaHref} className="inline-flex items-center gap-2 rounded-2xl bg-blue-600 text-white px-5 py-3 shadow hover:bg-blue-700">
          {ctaText}
          <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>
        </a>
      </div>
    </section>
  );
}

/*
Usage:
import AllianceStepper from './components/AllianceStepper';

function App(){
  return <AllianceStepper />;
}
*/
