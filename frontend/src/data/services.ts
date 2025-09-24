export type Service = {
  id: number
  title: string
  promise: string
  desc?: string
  incluye: string[]
  entrada: string[]
  entrega: string
  cta: string
}

export const SERVICES: Service[] = [
  {
    id: 1,
    title: 'Semaforización Online (diseño + auto-programación)',
    promise: 'cálculo de ciclos, fases y offsets óptimos',
    desc: 'Calculamos ciclos, fases y offsets óptimos usando aforos o video. Aplicamos criterios HCM y optimización heurística para reducir demoras, colas y pérdidas de verde. Entregamos archivos listos para el controlador y una simulación corta para validar.',
    incluye: ['Optimización por cruce', 'Simulación 30–60s', 'Export .csv/.xml para controlador'],
    entrada: ['Aforos CSV o videos 1080p', 'Parámetros del controlador/plan actual'],
    entrega: 'Plan de tiempos + video + archivo de carga, 48–72 h',
    cta: 'Optimizar mi cruce'
  },
  {
    id: 2,
    title: 'RPA de Tránsito (automatización de tareas repetitivas)',
    promise: 'automatización de procesos de oficina',
    desc: 'Automatizamos tareas de oficina: numerar planos, consolidar aforos, generar memorias e informes PDF, renombrado de fotos georreferenciadas y checks de consistencia. Disparas los “robots” por carpeta o formulario.',
    incluye: ['Flujos RPA configurados', 'Plantillas editables', 'Validación con IA'],
    entrada: ['Ejemplos de archivos', 'Formatos actuales', 'Carpeta de trabajo'],
    entrega: 'Recetas RPA + manual y video corto, 24–72 h',
    cta: 'Automatizar mi oficina'
  },
  {
    id: 3,
    title: 'PMT Express desde el celular',
    promise: 'radicación sin PC desde campo',
    desc: 'Dibuja el desvío con el dedo y la IA coloca señalización normalizada. Genera PDF/DWG y memoria técnica listos para radicar sin usar PC. Pensado para obra y supervisión en campo.',
    incluye: ['Editor móvil', 'Señalización automática', 'PDF/DWG + memoria'],
    entrada: ['Tipo de obra y ubicación', 'Requisitos de la autoridad local'],
    entrega: 'Carpeta PMT lista para radicar, en minutos (según complejidad)',
    cta: 'Hacer mi PMT'
  },
  {
    id: 4,
    title: 'Control de obra con alertas (menos auxiliares)',
    promise: 'cumplimiento en campo con menos auxiliares',
    desc: 'Checklist móvil con fotos geo y verificación automática de EPP y señalización mínima. Las incidencias generan alertas por WhatsApp y quedan trazadas en tablero con responsables y tiempos.',
    incluye: ['App de campo', 'Tablero de cumplimiento', 'Reportes automáticos'],
    entrada: ['Plan de obra', 'Lista de responsables', 'Umbrales de alerta'],
    entrega: 'Tablero en vivo + actas PDF semanales, setup 48 h',
    cta: 'Activar control'
  },
  {
    id: 5,
    title: 'Conteo y Capacidad Live (cámara + IA)',
    promise: 'aforos y LOS en tiempo real',
    desc: 'Aforos en tiempo real con clasificación vehicular y cálculo de LOS/v-c, demoras y colas. Funciona con cámara IP o celular antiguo. Exporta CSV y clips de validación.',
    incluye: ['Detección/tracking IA', 'Tablero de KPI', 'Export CSV + clips'],
    entrada: ['Stream o videos 1080p', 'Geometría básica del cruce'],
    entrega: 'Tablero operativo 24–48 h + reportes diarios',
    cta: 'Medir ahora'
  },
  {
    id: 6,
    title: 'Inventario de señalización “touch-to-fix”',
    promise: 'foto a reposición priorizada',
    desc: 'Foto georreferenciada → IA reconoce tipo y estado de la señal → se crea ticket y prioridad de reposición. Visualiza todo en mapa y genera lote para impresión/compra.',
    incluye: ['Mapa en línea', 'Priorización por criticidad', 'Paquete de impresión'],
    entrada: ['Fotos con GPS', 'Catálogo/normal de señales vigente'],
    entrega: 'Inventario + plan mensual, 5–10 días (según volumen)',
    cta: 'Levantar inventario'
  },
  {
    id: 7,
    title: 'Gemelo ligero de intersecciones',
    promise: 'simulación rápida para decisiones',
    desc: 'Simulación rápida para probar cambios (fases, giros, canalizaciones) con KPI de demora, cola y LOS. Útil para socialización y decisión pública.',
    incluye: ['Modelo web por cruce', 'Video 30–60s', 'Resumen de KPI'],
    entrada: ['Aforos/volúmenes', 'Geometría y planes actuales'],
    entrega: 'Link compartible + informe PDF, 72 h',
    cta: 'Simular mi cruce'
  },
  {
    id: 8,
    title: 'Bot de permisos y trámites',
    promise: 'expedientes digitales y seguimiento automatizado',
    desc: 'Recopila datos en WhatsApp/Telegram, arma expediente con anexos, valida requisitos y hace seguimiento hasta aprobación con recordatorios automáticos.',
    incluye: ['Formularios conversacionales', 'Checklist IA', 'Panel de estado'],
    entrada: ['Datos del solicitante', 'Requisitos y formatos de la entidad'],
    entrega: 'Expediente digital + tracking, 24–48 h',
    cta: 'Iniciar mi trámite'
  },
  {
    id: 9,
    title: 'Auditoría de Seguridad Vial exprés',
    promise: '10 acciones prioritarias en 72 h',
    desc: 'Recorrido técnico y matriz de riesgo (frecuencia × severidad) para definir 10 acciones inmediatas priorizadas. Enfocado en puntos críticos y quick-wins.',
    incluye: ['Walkthrough con fotos', 'Matriz de riesgo', 'Plano de mejoras'],
    entrada: ['Alcance y priorización', 'Historial de incidentes (si hay)'],
    entrega: 'Informe PDF + checklist operativo, 72 h',
    cta: 'Auditar ahora'
  },
  {
    id: 10,
    title: 'Optimización de semáforos por corredores (IA)',
    promise: 'ondas verdes y menor delay en ejes',
    desc: 'Coordinación de 10–20 intersecciones para ondas verdes y menor delay. Ajustamos offsets y splits con datos reales y validamos en simulación antes de implementar.',
    incluye: ['Plan maestro de tiempos', 'Sim before/after', 'Puesta en marcha'],
    entrada: ['Aforos por tramo', 'Planes actuales y relojes de controladores'],
    entrega: 'Tiempos coordinados + video, 1–2 semanas',
    cta: 'Optimizar corredor'
  },
  {
    id: 11,
    title: 'Diagnóstico con video-analytics (conflictos / near-miss)',
    promise: 'detección temprana de conflictos',
    desc: 'Analizamos trayectorias y conflictos a partir de video para detectar riesgos antes del siniestro. Se generan mapas de calor y medidas de mitigación.',
    incluye: ['Tracking IA', 'Heatmaps', 'Recomendaciones por severidad'],
    entrada: ['Videos 1080p', 'Planta del cruce', 'Horarios pico'],
    entrega: 'Informe PDF + clips marcados, 5 días',
    cta: 'Diagnosticar'
  },
  {
    id: 12,
    title: 'Censo QR de señalización (móvil)',
    promise: 'inventario trazable y rápido',
    desc: 'Etiquetado QR por elemento; captura de estado y foto en campo. Export masivo para plan de reposición y seguimiento por lote.',
    incluye: ['App de campo', 'Base QR', 'Export XLS/PDF con filtros'],
    entrada: ['Tramos/zonas objetivo', 'Catálogo local de señales'],
    entrega: 'Censo completo + plan, días a semanas (según red)',
    cta: 'Censar mi red'
  },
  {
    id: 13,
    title: 'EIV-Lite (Evaluación de Impacto Vial ágil)',
    promise: 'screening de factibilidad rápido',
    desc: 'Screening de factibilidad para etapas tempranas: demanda generada, capacidad/LOS y medidas de mitigación iniciales. Ideal para decidir rápido.',
    incluye: ['Modelo base', 'Escenarios pico', 'Recomendaciones de acceso'],
    entrada: ['Programa del proyecto', 'Accesos propuestos', 'Tráfico de fondo'],
    entrega: 'Informe ejecutivo, 7 días',
    cta: 'Solicitar EIV-Lite'
  },
  {
    id: 14,
    title: 'Plan de Movilidad para Empresas (PME)',
    promise: 'reducción de autos y eficiencia',
    desc: 'Diagnóstico de viajes del personal y plan para reducir autos (carpool, bici, TP, horarios). Metas trimestrales y tablero de seguimiento.',
    incluye: ['Encuestas rápidas', 'Plan de acciones', 'KPI y monitoreo'],
    entrada: ['Nómina y ubicación', 'Políticas internas/turnos'],
    entrega: 'Plan + tablero KPI, 2–4 semanas',
    cta: 'Hacer mi PME'
  },
  {
    id: 15,
    title: 'Estacionamiento inteligente',
    promise: 'rotación óptima y menos congestión',
    desc: 'Sensado simple y análisis de rotación/ocupación para ajustar oferta, zonas y tarifas. Disminuye búsqueda y congestión por estacionamiento.',
    incluye: ['Medición de ocupación', 'Modelos de rotación', 'Panel de control'],
    entrada: ['Mapa de plazas', 'Reglas y tarifas actuales'],
    entrega: 'Informe + tablero con escenarios, 2 semanas',
    cta: 'Optimizar parking'
  },
  {
    id: 16,
    title: 'Tablero de Movilidad en tiempo real',
    promise: 'KPIs unificados en un solo panel',
    desc: 'Integra aforos, IoT y reportes en un panel único con alertas automáticas. Exporta PDF para comités y guarda histórico de KPI.',
    incluye: ['Conexión de fuentes', 'KPI clave', 'Alertas configurables'],
    entrada: ['Acceso a datos', 'Lista de KPI y umbrales'],
    entrega: 'Tablero operativo, 1 semana',
    cta: 'Ver mi tablero'
  },
  {
    id: 17,
    title: 'PMU para eventos (plan de manejo urbano)',
    promise: 'ingreso/egreso seguros en eventos',
    desc: 'Plan para ingreso/egreso seguro en eventos: peatón, transporte público, estacionamientos y señalización temporal. Se incluyen fases por horario y roles.',
    incluye: ['Flujos por fase', 'Señalización IA', 'Centro de control ligero'],
    entrada: ['Aforo esperado', 'Horarios', 'Accesos y proveedores'],
    entrega: 'PMU + briefing de operación, 5–7 días',
    cta: 'Planear mi evento'
  },
  {
    id: 18,
    title: 'Permisos y trámites “sin filas” (servicio gestionado)',
    promise: 'radicación y seguimiento gestionado',
    desc: 'Hacemos pre-chequeo documental, radicación digital y seguimiento hasta aprobación. Notificamos estados y requerimientos.',
    incluye: ['Checklist por autoridad', 'Radicación', 'Reporte de estado'],
    entrada: ['Documentos y firmas', 'Plazos límite'],
    entrega: 'Expediente aprobado (o con ajustes), según entidad',
    cta: 'Gestionar permisos'
  },
  {
    id: 19,
    title: 'Mapa continuo de Riesgo Vial (near-miss)',
    promise: 'heatmap semanal de conflictos',
    desc: 'Integra videos y reportes ciudadanos para crear un heatmap semanal de conflictos. Prioriza puntos negros antes del siniestro y propone acciones.',
    incluye: ['Ingesta de fuentes', 'Heatmap', 'Lista de acciones priorizadas'],
    entrada: ['Videos/app ciudadana', 'Capas GIS y tramos de interés'],
    entrega: 'Mapa web + informe mensual, setup 1 semana',
    cta: 'Activar mapa de riesgo'
  },
  {
    id: 20,
    title: 'API de PMT/Obras para apps públicas',
    promise: 'publicación de PMT y obras en feeds estándar',
    desc: 'Registro único de obras y PMT con publicación en API/feeds estándar (Waze/Google) para informar desvíos y cierres. Reduce sorpresas a usuarios de vía.',
    incluye: ['Backoffice de carga', 'API/feeds (GTFS-like)', 'Panel de publicación'],
    entrada: ['Plantillas de obra/PMT', 'Responsables y flujos de aprobación'],
    entrega: 'Portal + API operativa, 2–3 semanas',
    cta: 'Publicar mis obras'
  }
]
