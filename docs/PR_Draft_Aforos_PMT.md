# Aforos + Verificador + Generador PMT (base)

## 🎯 Objetivo

Implementar sistema completo de ingestión, validación y generación de PMT base con enfoque en:
- **Ingestión robusta** desde Google Apps Script y archivos locales
- **Verificación de calidad** sin exposición de datos numéricos
- **Generación de paquetes PMT** con archivos normalizados y metadatos

## 📋 Cambios Implementados

### 🔧 Funcionalidades Core

#### 1. Normalizador de Aforos (`panorama_local/aforos/normalize.py`)
- **Equivalencias vehiculares** configurables por entidad/ciudad
- **Cálculo VPH** (Vehículos Por Hora) con intervalos temporales
- **Factor PHF** (Peak Hour Factor) automático
- **Hora HMD** (Máximo Diario) con distribución completa
- **Metadatos completos** del proceso de normalización
- **Soporte múltiple** de fuentes: Apps Script, CSV, Excel

#### 2. Verificador de Calidad (`panorama_local/pages/07_Verificador.py`)
- **Validación sin exposición** de valores numéricos
- **Hallazgos por severidad**: Críticos → Advertencias → Información
- **Score de calidad** 0-100 con recomendaciones automáticas
- **Bloqueo inteligente** de generación PMT con datos críticos
- **Verificaciones específicas**:
  - Estructura básica y completitud
  - Cobertura horaria (mínimo 12h, foco en picos)
  - Consistencia de patrones de tráfico
  - Calidad del PHF (0.7-1.0 típico)
  - Distribución por tipo de vehículo

#### 3. Generador PMT Base (`panorama_local/pages/08_Generador_PMT.py`)
- **Paquete ZIP completo** con 4 archivos:
  - `aforos_normalizados.csv` - Datos con equivalencias
  - `metadata.json` - Proceso completo + calidad
  - `resumen.json` - Métricas PHF/HMD/volúmenes
  - `pmt_base.md` - Plantilla base para reportes
- **Información de proyecto** integrada
- **Validación de prerrequisitos** antes de generar

#### 4. Carga de Aforos (`panorama_local/pages/06_Cargar_Aforos.py`)
- **Google Apps Script** integration (placeholder preparado)
- **Archivos locales** CSV/XLSX con preview inteligente
- **Detección automática** de columnas vehiculares
- **Configuración flexible** de equivalencias
- **Vista previa completa** antes de normalizar

### 🏗️ Arquitectura

```
panorama_local/
├── aforos/
│   └── normalize.py          # Core normalization logic
├── pages/
│   ├── 06_Cargar_Aforos.py   # Data ingestion UI
│   ├── 07_Verificador.py     # Quality verification UI
│   └── 08_Generador_PMT.py   # PMT package generation UI
└── reports/
    └── templates/
        └── pmt/
            └── base.py       # Base template rendering
```

### 🔒 Seguridad y Calidad

- **No exposición de datos**: Verificador solo muestra hallazgos/correcciones
- **Validaciones robustas**: Múltiples checks de integridad
- **Logging estructurado**: Compatible con sistema existente
- **Session state management**: Flujo coherente entre páginas
- **Error handling**: Mensajes claros para debugging

### 📊 Métricas y KPIs

- **PHF**: Factor de hora pico (0.7-1.0 ideal)
- **HMD**: Hora de máximo flujo diario
- **VPH**: Vehículos por hora por intervalo
- **Score de calidad**: 0-100 basado en severidad de hallazgos
- **Cobertura horaria**: Validación de horas pico incluidas

## 🧪 Testing

### Smoke Tests Incluidos
- ✅ Normalización con datos de ejemplo
- ✅ Verificación de calidad completa
- ✅ Generación de ZIP funcional
- ✅ Templates rendering sin errores

### Casos de Prueba
- Datos completos 24h con picos realistas
- Datos incompletos (cobertura horaria baja)
- PHF anormal (muy alto/bajo)
- Distribución vehicular inconsistente

## 🔌 Integraciones Futuras

### Próximos increments (post-merge)
1. **Google Apps Script real** - Endpoint completo con auth
2. **Comparador de velocidades** - Google Distance Matrix + Waze
3. **Templates avanzadas** - Docx con `docxtpl` + PDF con `reportlab`
4. **Perfil normativo** - Equivalencias por ciudad/entidad
5. **Versionado automático** - Renovación inmediata con cambios

### APIs Externas Preparadas
- **Google Maps Distance Matrix**: Para velocidades actuales
- **Waze**: Para tiempos reales de viaje
- **Google Apps Script**: Para ingestión desde Drive

## 📈 Beneficios

- **Eficiencia**: Normalización automática vs manual
- **Calidad**: Verificación sistemática antes de PMT
- **Escalabilidad**: Múltiples fuentes de datos
- **Confidencialidad**: No exposición de datos sensibles
- **Reproducibilidad**: Metadatos completos del proceso

## 🚀 Deployment

- **Páginas Streamlit**: Integradas en navegación principal
- **Dependencias**: Agregadas a `requirements.txt`
- **Configuración**: Secrets preparados en `.streamlit/secrets.toml`
- **CI/CD**: Tests incluidos en pipeline existente

## ✅ Checklist de Aceptación

- [x] Normalización calcula equivalencias, VPH, PHF, HMD correctamente
- [x] Verificador NO expone valores numéricos, solo hallazgos
- [x] ZIP incluye `aforos_normalizados.csv`, `metadata.json`, `resumen.json`
- [x] Template base renderiza variables mínimas (proyecto, PHF, HMD)
- [x] Sin secrets hardcodeados (URLs configurables)
- [x] Tests básicos pasan en CI
- [x] Navegación automática con prefijos numéricos

---

**Labels**: `enhancement`, `feature`, `aforos`, `pmt`, `backend`
**Tamaño**: M (3 archivos nuevos, ~800 líneas)
**Riesgo**: Bajo (funcionalidad autocontenida)
**Testing**: Unit tests + smoke tests incluidos