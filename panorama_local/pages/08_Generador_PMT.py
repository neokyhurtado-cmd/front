"""
08_Generador_PMT.py - Generador de PMT base para Panorama Ingeniería

Esta página genera el paquete PMT completo con:
- aforos_normalizados.csv
- metadata.json
- resumen.json
- Plantilla base en Markdown
"""

import streamlit as st
import pandas as pd
import json
import zipfile
import io
from datetime import datetime
from typing import Dict, Any, Optional
import logging

# Importar módulos del proyecto
try:
    from reports.templates.pmt.base import render_pmt_base_template
except ImportError:
    # Fallback para desarrollo
    import sys
    sys.path.append('.')
    # Placeholder para template
    def render_pmt_base_template(data): return "# PMT Base Template\n\nDatos: {data}"

logger = logging.getLogger(__name__)

class PMTGenerator:
    """
    Generador de paquetes PMT base
    """

    def __init__(self):
        self.logger = logging.getLogger(f"{__name__}.{self.__class__.__name__}")

    def generate_pmt_package(self, normalized_data: Dict[str, Any],
                           verification_results: Dict[str, Any],
                           project_info: Dict[str, str]) -> bytes:
        """
        Generar paquete ZIP con archivos PMT

        Args:
            normalized_data: Datos normalizados
            verification_results: Resultados de verificación
            project_info: Información del proyecto

        Returns:
            Bytes del archivo ZIP
        """
        zip_buffer = io.BytesIO()

        with zipfile.ZipFile(zip_buffer, 'w', zipfile.ZIP_DEFLATED) as zip_file:
            # 1. Archivo CSV normalizado
            self._add_normalized_csv(zip_file, normalized_data)

            # 2. Metadata JSON
            self._add_metadata_json(zip_file, normalized_data, verification_results, project_info)

            # 3. Resumen JSON
            self._add_summary_json(zip_file, normalized_data, verification_results)

            # 4. Plantilla base Markdown
            self._add_base_template(zip_file, normalized_data, project_info)

        zip_buffer.seek(0)
        return zip_buffer.getvalue()

    def _add_normalized_csv(self, zip_file: zipfile.ZipFile, data: Dict[str, Any]):
        """Agregar archivo CSV normalizado"""
        df = pd.DataFrame(data['data_normalized'])
        csv_content = df.to_csv(index=False, encoding='utf-8-sig')
        zip_file.writestr('aforos_normalizados.csv', csv_content)

    def _add_metadata_json(self, zip_file: zipfile.ZipFile,
                          normalized_data: Dict[str, Any],
                          verification_results: Dict[str, Any],
                          project_info: Dict[str, str]):
        """Agregar archivo de metadatos JSON"""
        metadata = {
            'generacion': {
                'timestamp': datetime.now().isoformat(),
                'generator_version': '1.0.0',
                'panorama_version': 'v0.3.1'
            },
            'proyecto': project_info,
            'normalizacion': normalized_data['metadata'],
            'verificacion': {
                'quality_score': verification_results['quality_score'],
                'can_generate_pmt': verification_results['can_generate_pmt'],
                'findings_count': {
                    'critical': len(verification_results['findings']['critical']),
                    'warning': len(verification_results['findings']['warning']),
                    'info': len(verification_results['findings']['info'])
                }
            }
        }

        metadata_json = json.dumps(metadata, indent=2, ensure_ascii=False)
        zip_file.writestr('metadata.json', metadata_json)

    def _add_summary_json(self, zip_file: zipfile.ZipFile,
                         normalized_data: Dict[str, Any],
                         verification_results: Dict[str, Any]):
        """Agregar archivo de resumen JSON"""
        summary = normalized_data['summary'].copy()
        summary.update({
            'quality_score': verification_results['quality_score'],
            'phf': normalized_data['phf'],
            'hmd': normalized_data['hmd'],
            'generado_en': datetime.now().isoformat()
        })

        summary_json = json.dumps(summary, indent=2, ensure_ascii=False)
        zip_file.writestr('resumen.json', summary_json)

    def _add_base_template(self, zip_file: zipfile.ZipFile,
                          normalized_data: Dict[str, Any],
                          project_info: Dict[str, str]):
        """Agregar plantilla base Markdown"""
        template_data = {
            'proyecto': project_info,
            'resumen': normalized_data['summary'],
            'phf': normalized_data['phf'],
            'hmd': normalized_data['hmd'],
            'fecha_generacion': datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        }

        try:
            template_content = render_pmt_base_template(template_data)
        except Exception as e:
            # Fallback si falla la template
            self.logger.warning(f"Template rendering failed: {e}")
            template_content = f"""# PMT - {project_info.get('nombre', 'Proyecto')}

**Ubicación:** {project_info.get('ubicacion', 'No especificada')}
**Fecha:** {template_data['fecha_generacion']}

## Resumen Ejecutivo

- **PHF (Peak Hour Factor):** {normalized_data['phf']:.3f}
- **HMD (Hora Máximo Diario):** {normalized_data['hmd']}
- **Volumen Pico:** {normalized_data['summary']['peak_hour_volume']:.0f} veh/h

## Datos Normalizados

Los datos normalizados se encuentran en `aforos_normalizados.csv`

---
*Generado por Panorama PMT v0.3.1*
"""

        zip_file.writestr('pmt_base.md', template_content)


def main():
    st.title("🏗️ Generador PMT Base")
    st.markdown("""
    **Generación de paquete PMT completo**

    Esta herramienta crea un ZIP con todos los archivos necesarios para un PMT base:
    - `aforos_normalizados.csv` - Datos normalizados con equivalencias
    - `metadata.json` - Información completa del proceso
    - `resumen.json` - Métricas resumidas (PHF, HMD, etc.)
    - `pmt_base.md` - Plantilla base para reportes
    """)

    # Verificar prerrequisitos
    if not check_prerequisites():
        return

    # Información del proyecto
    st.subheader("📋 Información del Proyecto")
    col1, col2 = st.columns(2)

    with col1:
        project_name = st.text_input(
            "Nombre del proyecto:",
            placeholder="Ej: Avenida Principal - Calle 123"
        )
        project_location = st.text_input(
            "Ubicación:",
            placeholder="Ej: Bogotá, Colombia"
        )

    with col2:
        project_entity = st.text_input(
            "Entidad responsable:",
            placeholder="Ej: Secretaría de Movilidad"
        )
        project_date = st.date_input(
            "Fecha del aforo:",
            value=datetime.now().date()
        )

    # Validar información del proyecto
    project_info = {
        'nombre': project_name,
        'ubicacion': project_location,
        'entidad': project_entity,
        'fecha_aforo': project_date.isoformat()
    }

    if not all([project_name, project_location]):
        st.warning("⚠️ Completa al menos nombre y ubicación del proyecto")
        can_generate = False
    else:
        can_generate = True

    # Mostrar resumen de datos
    display_data_summary()

    # Generar PMT
    if st.button("🚀 Generar PMT", type="primary", disabled=not can_generate):
        if not can_generate:
            st.error("Completa la información del proyecto")
            return

        with st.spinner("Generando paquete PMT..."):
            try:
                generator = PMTGenerator()

                normalized_data = st.session_state.aforos_normalized
                verification_results = st.session_state.aforos_verification

                # Generar ZIP
                zip_data = generator.generate_pmt_package(
                    normalized_data,
                    verification_results,
                    project_info
                )

                # Preparar descarga
                zip_filename = f"pmt_{project_name.replace(' ', '_').lower()}_{datetime.now().strftime('%Y%m%d_%H%M%S')}.zip"

                st.success("✅ Paquete PMT generado exitosamente")

                # Botón de descarga
                st.download_button(
                    label="📦 Descargar PMT Completo",
                    data=zip_data,
                    file_name=zip_filename,
                    mime="application/zip",
                    type="primary"
                )

                # Mostrar contenido del ZIP
                display_zip_contents(zip_data)

            except Exception as e:
                st.error(f"❌ Error generando PMT: {str(e)}")
                logger.error(f"PMT generation error: {e}")


def check_prerequisites() -> bool:
    """Verificar que se cumplan los prerrequisitos"""
    issues = []

    if 'aforos_normalized' not in st.session_state or st.session_state.aforos_normalized is None:
        issues.append("No hay datos normalizados - ve a '06_Cargar_Aforos'")

    if 'aforos_verification' not in st.session_state or st.session_state.aforos_verification is None:
        issues.append("No hay resultados de verificación - ve a '07_Verificador'")

    if issues:
        st.error("❌ Prerrequisitos no cumplidos:")
        for issue in issues:
            st.write(f"• {issue}")
        return False

    # Verificar que los datos sean aptos para PMT
    verification = st.session_state.aforos_verification
    if not verification.get('can_generate_pmt', False):
        st.error("❌ Los datos no pasan verificación - corrige los problemas críticos")
        return False

    return True


def display_data_summary():
    """Mostrar resumen de los datos a incluir"""
    if 'aforos_normalized' in st.session_state and st.session_state.aforos_normalized:
        st.subheader("📊 Resumen de Datos")

        data = st.session_state.aforos_normalized
        summary = data['summary']

        col1, col2, col3, col4 = st.columns(4)

        with col1:
            st.metric("Total Vehículos", f"{summary['total_vehicles']:.0f}")

        with col2:
            st.metric("Pico Horario", f"{summary['peak_hour_volume']:.0f} veh/h")

        with col3:
            st.metric("PHF", f"{data['phf']:.3f}")

        with col4:
            st.metric("HMD", data['hmd'])

        # Score de calidad
        if 'aforos_verification' in st.session_state:
            verification = st.session_state.aforos_verification
            score = verification['quality_score']

            if score >= 80:
                st.success(f"✅ Calidad de datos: {score}/100")
            elif score >= 60:
                st.warning(f"⚠️ Calidad de datos: {score}/100")
            else:
                st.error(f"❌ Calidad de datos: {score}/100")


def display_zip_contents(zip_data: bytes):
    """Mostrar contenido del ZIP generado"""
    st.subheader("📁 Contenido del Paquete PMT")

    # Simular contenido (en producción usar zipfile para leer)
    files_info = [
        ("aforos_normalizados.csv", "Datos normalizados con equivalencias vehiculares"),
        ("metadata.json", "Información completa del proceso de normalización"),
        ("resumen.json", "Métricas resumidas (PHF, HMD, volúmenes)"),
        ("pmt_base.md", "Plantilla base para reportes y documentación")
    ]

    for filename, description in files_info:
        col1, col2 = st.columns([2, 3])
        with col1:
            st.code(filename)
        with col2:
            st.write(description)

    st.info("""
    **💡 Próximos pasos:**

    1. **Extrae el ZIP** en tu directorio de trabajo
    2. **Revisa los archivos** generados
    3. **Usa `pmt_base.md`** como base para tu reporte final
    4. **Integra con herramientas** de documentación (Word, PDF)
    """)


if __name__ == "__main__":
    main()