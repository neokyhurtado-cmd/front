"""
Plantilla base para reportes PMT - Panorama Ingeniería

Esta plantilla genera el contenido base en Markdown para reportes PMT.
"""

from typing import Dict, Any
import logging

logger = logging.getLogger(__name__)

def render_pmt_base_template(data: Dict[str, Any]) -> str:
    """
    Renderizar plantilla base de PMT

    Args:
        data: Diccionario con datos del proyecto y normalización

    Returns:
        String con contenido Markdown
    """
    try:
        proyecto = data.get('proyecto', {})
        resumen = data.get('resumen', {})
        phf = data.get('phf', 0)
        hmd = data.get('hmd', 'N/A')
        fecha_generacion = data.get('fecha_generacion', 'N/A')

        template = f"""# PMT - {proyecto.get('nombre', 'Proyecto Sin Nombre')}

**Ubicación:** {proyecto.get('ubicacion', 'No especificada')}
**Entidad:** {proyecto.get('entidad', 'No especificada')}
**Fecha del Aforo:** {proyecto.get('fecha_aforo', 'No especificada')}
**Generado:** {fecha_generacion}

---

## Resumen Ejecutivo

### Métricas Principales
- **PHF (Peak Hour Factor):** {phf:.3f}
- **HMD (Hora del Máximo Diario):** {hmd}
- **Volumen de Pico Horario:** {resumen.get('peak_hour_volume', 0):.0f} veh/h
- **Volumen Total:** {resumen.get('total_vehicles', 0):.0f} veh
- **Horas Analizadas:** {resumen.get('hours_analyzed', 0)}

### Distribución Horaria
- **Promedio por Hora:** {resumen.get('avg_hourly_volume', 0):.0f} veh/h

---

## Datos Normalizados

Los datos normalizados se encuentran en el archivo `aforos_normalizados.csv` incluido en este paquete.

### Columnas Incluidas
- `hora`: Hora del día (formato HH:MM)
- `auto`, `camion`, `bus`, `moto`: Conteos por tipo de vehículo
- `auto_equiv`, `camion_equiv`, `bus_equiv`, `moto_equiv`: Equivalencias vehiculares
- `total_equiv`: Total equivalente vehicular

### Equivalencias Aplicadas
- Auto/Camioneta: 1.0
- Camión: 2.0
- Bus: 2.5
- Moto: 0.5
- Bicicleta: 0.2
- Peatón: 0.0

---

## Metadatos del Proceso

Los metadatos completos se encuentran en `metadata.json`.

### Información de Calidad
- **Score de Calidad:** Verificar en `metadata.json`
- **Hallazgos Críticos:** Revisar antes de usar en producción
- **Cobertura Horaria:** Validar horas pico incluidas

---

## Próximos Pasos

1. **Revisar Datos:** Validar que los datos normalizados sean correctos
2. **Generar Reporte:** Usar esta plantilla como base para reportes finales
3. **Documentación Adicional:** Incluir mapas, fotografías y contexto del sitio
4. **Aprobación:** Obtener visto bueno de la entidad responsable

---

*Generado por Panorama PMT v0.3.1 - Panorama Ingeniería*
*Fecha de generación: {fecha_generacion}*
"""

        return template

    except Exception as e:
        logger.error(f"Error rendering PMT base template: {e}")
        return f"""# Error en Plantilla PMT

Error generando plantilla: {str(e)}

Datos recibidos: {data}
"""