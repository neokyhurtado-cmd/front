"""
Plantilla base para reportes PMT - Panorama Ingeniería

Esta plantilla genera el contenido base en Markdown para reportes PMT.
"""

from typing import Dict, Any
import logging

logger = logging.getLogger(__name__)

"""
Plantilla base para reportes PMT - Panorama Ingeniería

Esta plantilla genera contenido completo para reportes PMT en múltiples formatos.
"""

from typing import Dict, Any, Optional
import logging
from datetime import datetime
from pathlib import Path
import json

logger = logging.getLogger(__name__)

def render_pmt_base_template(data: Dict[str, Any]) -> str:
    """
    Renderizar plantilla base completa de PMT en Markdown

    Args:
        data: Diccionario con datos del proyecto y normalización

    Returns:
        String con contenido Markdown completo
    """
    try:
        proyecto = data.get('proyecto', {})
        resumen = data.get('resumen', {})
        phf = data.get('phf', 0)
        hmd = data.get('hmd', 'N/A')
        fecha_generacion = data.get('fecha_generacion', datetime.now().strftime('%Y-%m-%d %H:%M:%S'))

        # Portada
        portada = f"""# PMT - {proyecto.get('nombre', 'Proyecto Sin Nombre')}

## Estudio de Tránsito y Movilidad

**Ubicación:** {proyecto.get('ubicacion', 'No especificada')}  
**Entidad:** {proyecto.get('entidad', 'No especificada')}  
**Fecha del Aforo:** {proyecto.get('fecha_aforo', 'No especificada')}  
**Consultor:** Panorama Ingeniería  
**Generado:** {fecha_generacion}

---

"""

        # Índice
        indice = """## Índice

1. [Introducción](#introducción)
2. [Marco Normativo](#marco-normativo)
3. [Metodología](#metodología)
4. [Análisis de Datos](#análisis-de-datos)
5. [Resultados](#resultados)
6. [Conclusiones y Recomendaciones](#conclusiones-y-recomendaciones)
7. [Anexos](#anexos)

---

"""

        # Introducción
        introduccion = f"""## Introducción

### Objetivo del Estudio
El presente estudio tiene como objetivo analizar las condiciones de tránsito en el sector de {proyecto.get('ubicacion', 'la zona de estudio')}, con el fin de determinar los volúmenes vehiculares, identificar las horas pico de tránsito y calcular los parámetros necesarios para el diseño de intersecciones semaforizadas y otros elementos del sistema de tránsito.

### Alcance del Trabajo
- Recolección y procesamiento de datos de aforos vehiculares
- Análisis de patrones horarios de tránsito
- Cálculo de factores de hora pico (PHF)
- Determinación de la hora del máximo diario (HMD)
- Generación de volúmenes equivalentes vehiculares

### Información General del Proyecto
- **Tipo de Proyecto:** {proyecto.get('tipo', 'PMT')}
- **Categoría:** {proyecto.get('categoria', 'No especificada')}
- **Fecha de Ejecución:** {proyecto.get('fecha_ejecucion', 'No especificada')}

---

"""

        # Marco Normativo
        marco_normativo = """## Marco Normativo

### Normativa Aplicada
- **Decreto 1079 de 2015:** Por el cual se modifica la Resolución 000084 de 2014 y se dictan otras disposiciones en materia de tránsito.
- **Resolución 000084 de 2014:** Manual de Señalización Vial.
- **Norma Colombiana 1480:** Señalización Vial.
- **HCM (Highway Capacity Manual):** Metodología para análisis de capacidad de carreteras.

### Criterios de Diseño
- **Nivel de Servicio (LOS):** Asegurar operaciones en condiciones aceptables
- **Factor de Hora Pico (PHF):** Entre 0.70 y 0.95 para condiciones típicas
- **Volúmenes de Diseño:** Basados en hora pico ajustada

---

"""

        # Metodología
        metodologia = """## Metodología

### Recolección de Datos
Los datos de aforos vehiculares fueron recolectados mediante conteos manuales en sitio, clasificando los vehículos por tipo y registrando los volúmenes por intervalos de tiempo.

### Clasificación Vehicular
- **Automóvil/Camioneta:** Vehículos de pasajeros con capacidad ≤ 9 personas
- **Camión:** Vehículos de carga con peso bruto vehicular > 3.5 ton
- **Bus/Buseta:** Vehículos de transporte público de pasajeros
- **Motocicleta:** Vehículos de dos ruedas motorizados
- **Otros:** Bicicletas, peatones, etc.

### Equivalencias Vehiculares
- Automóvil/Camioneta: 1.0
- Camión: 2.0
- Bus/Buseta: 2.5
- Motocicleta: 0.5
- Bicicleta: 0.2
- Peatón: 0.0

### Procesamiento de Datos
1. Validación de datos crudos
2. Aplicación de equivalencias vehiculares
3. Cálculo de totales horarios
4. Determinación de hora del máximo diario (HMD)
5. Cálculo del factor de hora pico (PHF)

---

"""

        # Análisis de Datos
        analisis_datos = f"""## Análisis de Datos

### Resumen Ejecutivo

#### Métricas Principales
- **PHF (Peak Hour Factor):** {phf:.3f}
- **HMD (Hora del Máximo Diario):** {hmd}
- **Volumen de Pico Horario:** {resumen.get('peak_hour_volume', 0):.0f} veh/h
- **Volumen Total:** {resumen.get('total_vehicles', 0):.0f} veh
- **Horas Analizadas:** {resumen.get('hours_analyzed', 0)}
- **Promedio por Hora:** {resumen.get('avg_hourly_volume', 0):.0f} veh/h

### Distribución por Tipo de Vehículo
Los datos detallados por tipo de vehículo se encuentran en el archivo `aforos_normalizados.csv`.

### Patrones Horarios
La distribución horaria muestra un patrón típico de tránsito urbano, con picos matutinos y vespertinos correspondientes a los horarios de ingreso y salida de actividades laborales.

---

"""

        # Resultados
        resultados = f"""## Resultados

### Volúmenes Equivalentes
Los volúmenes vehiculares expresados en equivalentes PCE (Passenger Car Equivalent) permiten comparar flujos de tránsito heterogéneos de manera consistente.

### Factor de Hora Pico
El PHF calculado de {phf:.3f} indica {'condiciones de flujo relativamente uniforme' if phf > 0.9 else 'presencia de picos pronunciados de tránsito'}.

### Hora del Máximo Diario
La hora del máximo diario se registra a las {hmd}, {'coincidiendo con horarios pico típicos' if hmd in ['07:00', '08:00', '17:00', '18:00'] else 'mostrando un patrón atípico que requiere análisis adicional'}.

### Recomendaciones de Diseño
Basado en los volúmenes registrados, se recomienda:
- Considerar el volumen de diseño de {resumen.get('peak_hour_volume', 0):.0f} veh/h para dimensionamiento
- Aplicar PHF de {phf:.3f} para ajustes de capacidad
- Programar señales considerando la HMD a las {hmd}

---

"""

        # Conclusiones
        conclusiones = """## Conclusiones y Recomendaciones

### Conclusiones Principales
1. Los volúmenes de tránsito registrados son consistentes con el uso del suelo y la actividad económica de la zona.
2. Los patrones horarios siguen el comportamiento típico esperado para un corredor urbano.
3. Los parámetros calculados (PHF, HMD) permiten el diseño adecuado de elementos de control de tránsito.

### Recomendaciones
1. **Monitoreo Continuo:** Se recomienda implementar un sistema de monitoreo continuo para validar las tendencias de crecimiento del tránsito.
2. **Medidas de Gestión:** Considerar la implementación de medidas de gestión de la demanda durante horas pico.
3. **Señalización:** Actualizar la señalización vial para mejorar la seguridad y eficiencia del tránsito.
4. **Mantenimiento:** Establecer un programa de mantenimiento preventivo de la infraestructura vial.

### Próximos Pasos
1. Revisar y aprobar los resultados del estudio
2. Incorporar los parámetros calculados en el diseño de intersecciones
3. Implementar las recomendaciones de gestión de tránsito
4. Monitorear la evolución del tránsito post-implementación

---

"""

        # Anexos
        anexos = """## Anexos

### Anexo A: Datos Normalizados
Archivo: `aforos_normalizados.csv`
Contiene los datos de aforos procesados con equivalencias vehiculares aplicadas.

### Anexo B: Metadatos del Proceso
Archivo: `metadata.json`
Incluye información detallada sobre el proceso de normalización, parámetros aplicados y métricas de calidad.

### Anexo C: Resumen Ejecutivo
Archivo: `resumen.json`
Contiene las métricas principales y estadísticas del análisis.

### Anexo D: Comparaciones de Velocidades (si aplica)
Archivo: `comparaciones_velocidades.xlsx`
Resultados de consultas a servicios de mapas para comparación de tiempos de viaje.

---

*Generado por Panorama PMT v0.3.1 - Panorama Ingeniería*  
*Fecha de generación: {fecha_generacion}*  
*Este documento es confidencial y propiedad de Panorama Ingeniería*
"""

        # Combinar todas las secciones
        full_content = portada + indice + introduccion + marco_normativo + metodologia + analisis_datos + resultados + conclusiones + anexos

        return full_content

    except Exception as e:
        logger.error(f"Error rendering PMT base template: {e}")
        return f"""# Error en Plantilla PMT

Error generando plantilla: {str(e)}

Datos recibidos: {data}
"""

def generate_pmt_docx(data: Dict[str, Any], output_path: str) -> bool:
    """
    Generar reporte PMT en formato Docx

    Args:
        data: Datos del proyecto
        output_path: Ruta donde guardar el archivo .docx

    Returns:
        True si se generó exitosamente
    """
    try:
        from docxtpl import DocxTemplate
        from docx.shared import Inches
        import pandas as pd

        # Por ahora, crear un documento básico con docxtpl
        # En una implementación completa, se usaría una plantilla .docx existente

        # Crear datos para la plantilla
        context = {
            'titulo': f"PMT - {data.get('proyecto', {}).get('nombre', 'Proyecto')}",
            'ubicacion': data.get('proyecto', {}).get('ubicacion', 'No especificada'),
            'entidad': data.get('proyecto', {}).get('entidad', 'No especificada'),
            'fecha_aforo': data.get('proyecto', {}).get('fecha_aforo', 'No especificada'),
            'fecha_generacion': data.get('fecha_generacion', datetime.now().strftime('%Y-%m-%d')),
            'phf': f"{data.get('phf', 0):.3f}",
            'hmd': data.get('hmd', 'N/A'),
            'volumen_pico': f"{data.get('resumen', {}).get('peak_hour_volume', 0):.0f}",
            'volumen_total': f"{data.get('resumen', {}).get('total_vehicles', 0):.0f}",
            'horas_analizadas': data.get('resumen', {}).get('hours_analyzed', 0),
        }

        # Nota: Para una implementación completa, necesitaríamos una plantilla .docx
        # Por ahora, devolver False indicando que no está implementado
        logger.warning("Docx generation not fully implemented - needs template file")
        return False

    except ImportError:
        logger.error("docxtpl not installed - cannot generate Docx")
        return False
    except Exception as e:
        logger.error(f"Error generating Docx: {e}")
        return False

def generate_pmt_pdf(data: Dict[str, Any], output_path: str) -> bool:
    """
    Generar reporte PMT en formato PDF usando reportlab

    Args:
        data: Datos del proyecto
        output_path: Ruta donde guardar el archivo .pdf

    Returns:
        True si se generó exitosamente
    """
    try:
        from reportlab.lib import colors
        from reportlab.lib.pagesizes import letter, A4
        from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
        from reportlab.lib.units import inch
        from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle
        from reportlab.lib.enums import TA_CENTER, TA_LEFT

        # Crear documento
        doc = SimpleDocTemplate(output_path, pagesize=A4)
        styles = getSampleStyleSheet()

        # Estilos personalizados
        title_style = ParagraphStyle(
            'CustomTitle',
            parent=styles['Heading1'],
            fontSize=16,
            spaceAfter=30,
            alignment=TA_CENTER
        )

        subtitle_style = ParagraphStyle(
            'CustomSubtitle',
            parent=styles['Heading2'],
            fontSize=14,
            spaceAfter=20,
        )

        normal_style = styles['Normal']

        # Contenido del documento
        story = []

        # Portada
        proyecto = data.get('proyecto', {})
        story.append(Paragraph(f"PMT - {proyecto.get('nombre', 'Proyecto Sin Nombre')}", title_style))
        story.append(Spacer(1, 0.5*inch))
        story.append(Paragraph("Estudio de Tránsito y Movilidad", subtitle_style))
        story.append(Spacer(1, 0.3*inch))

        # Información del proyecto
        info_data = [
            ["Ubicación:", proyecto.get('ubicacion', 'No especificada')],
            ["Entidad:", proyecto.get('entidad', 'No especificada')],
            ["Fecha del Aforo:", proyecto.get('fecha_aforo', 'No especificada')],
            ["Consultor:", "Panorama Ingeniería"],
            ["Generado:", data.get('fecha_generacion', datetime.now().strftime('%Y-%m-%d %H:%M:%S'))]
        ]

        info_table = Table(info_data, colWidths=[2*inch, 4*inch])
        info_table.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (0, -1), colors.lightgrey),
            ('TEXTCOLOR', (0, 0), (0, -1), colors.black),
            ('ALIGN', (0, 0), (-1, -1), 'LEFT'),
            ('FONTNAME', (0, 0), (-1, -1), 'Helvetica'),
            ('FONTSIZE', (0, 0), (-1, -1), 10),
            ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
            ('GRID', (0, 0), (-1, -1), 1, colors.black)
        ]))
        story.append(info_table)
        story.append(Spacer(1, 0.5*inch))

        # Resumen Ejecutivo
        story.append(Paragraph("Resumen Ejecutivo", subtitle_style))

        resumen = data.get('resumen', {})
        metrics_data = [
            ["Métrica", "Valor"],
            ["PHF (Peak Hour Factor)", f"{data.get('phf', 0):.3f}"],
            ["HMD (Hora del Máximo Diario)", data.get('hmd', 'N/A')],
            ["Volumen de Pico Horario", f"{resumen.get('peak_hour_volume', 0):.0f} veh/h"],
            ["Volumen Total", f"{resumen.get('total_vehicles', 0):.0f} veh"],
            ["Horas Analizadas", str(resumen.get('hours_analyzed', 0))],
            ["Promedio por Hora", f"{resumen.get('avg_hourly_volume', 0):.0f} veh/h"]
        ]

        metrics_table = Table(metrics_data, colWidths=[3*inch, 2*inch])
        metrics_table.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'LEFT'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, -1), 10),
            ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
            ('GRID', (0, 0), (-1, -1), 1, colors.black)
        ]))
        story.append(metrics_table)

        # Generar PDF
        doc.build(story)
        return True

    except Exception as e:
        logger.error(f"Error generating PDF: {e}")
        return False

def create_pmt_package(data: Dict[str, Any], output_dir: str) -> str:
    """
    Crear paquete completo PMT con todos los archivos

    Args:
        data: Datos del proyecto
        output_dir: Directorio donde crear el paquete

    Returns:
        Ruta al archivo ZIP creado
    """
    import zipfile
    import os
    from pathlib import Path

    try:
        # Crear directorio si no existe
        Path(output_dir).mkdir(parents=True, exist_ok=True)

        # Nombre del paquete
        proyecto = data.get('proyecto', {})
        nombre_proyecto = proyecto.get('nombre', 'proyecto').replace(' ', '_').lower()
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        zip_name = f"PMT_{nombre_proyecto}_{timestamp}.zip"
        zip_path = os.path.join(output_dir, zip_name)

        with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
            # Generar y añadir archivos

            # 1. Markdown completo
            md_content = render_pmt_base_template(data)
            zipf.writestr('PMT_Reporte.md', md_content)

            # 2. Datos normalizados CSV
            if 'data_normalized' in data:
                import pandas as pd
                df = pd.DataFrame(data['data_normalized'])
                csv_content = df.to_csv(index=False)
                zipf.writestr('aforos_normalizados.csv', csv_content)

            # 3. Metadatos JSON
            metadata = data.get('metadata', {})
            metadata_json = json.dumps(metadata, indent=2, ensure_ascii=False)
            zipf.writestr('metadata.json', metadata_json)

            # 4. Resumen JSON
            resumen = data.get('resumen', {})
            resumen_json = json.dumps(resumen, indent=2, ensure_ascii=False)
            zipf.writestr('resumen.json', resumen_json)

            # 5. PDF (si se puede generar)
            pdf_path = os.path.join(output_dir, 'temp_pmt_report.pdf')
            if generate_pmt_pdf(data, pdf_path):
                zipf.write(pdf_path, 'PMT_Reporte.pdf')
                os.remove(pdf_path)  # Limpiar archivo temporal

        logger.info(f"PMT package created: {zip_path}")
        return zip_path

    except Exception as e:
        logger.error(f"Error creating PMT package: {e}")
        return ""