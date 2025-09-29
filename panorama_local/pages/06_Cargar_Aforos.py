"""
06_Cargar_Aforos.py - Página de carga de aforos para Panorama PMT

Esta página permite:
- Cargar datos desde Google Apps Script (Drive)
- Subir archivos CSV/XLSX locales
- Mostrar preview de datos normalizados
- Guardar en session_state para siguientes pasos
"""

import streamlit as st
import pandas as pd
import requests
import json
from typing import Optional, Dict, Any
import logging

# Importar módulos del proyecto
try:
    from aforos.normalize import AforosNormalizer, load_aforos_from_csv, load_aforos_from_excel
except ImportError:
    # Fallback para desarrollo
    import sys
    sys.path.append('.')
    from panorama_local.aforos.normalize import AforosNormalizer, load_aforos_from_csv, load_aforos_from_excel

logger = logging.getLogger(__name__)

def main():
    st.title("📊 Cargar Aforos")
    st.markdown("""
    **Carga y normalización inicial de datos de aforos**

    Esta herramienta lee datos de conteos de tráfico desde:
    - Google Apps Script (Drive)
    - Archivos CSV/XLSX locales
    """)

    # Inicializar session_state
    if 'aforos_data' not in st.session_state:
        st.session_state.aforos_data = None
    if 'aforos_normalized' not in st.session_state:
        st.session_state.aforos_normalized = None

    # Opciones de carga
    load_option = st.radio(
        "Seleccionar fuente de datos:",
        ["Google Apps Script", "Archivo local (CSV/XLSX)"],
        key="load_option"
    )

    # Contenedor para resultados
    results_container = st.container()

    if load_option == "Google Apps Script":
        load_from_apps_script(results_container)
    else:
        load_from_file(results_container)

    # Mostrar resultados si existen
    display_results(results_container)


def load_from_apps_script(container):
    """Cargar datos desde Google Apps Script"""
    with container:
        st.subheader("📡 Google Apps Script")

        script_url = st.text_input(
            "URL del Apps Script:",
            placeholder="https://script.google.com/macros/s/SCRIPT_ID/exec",
            help="URL del script desplegado como aplicación web"
        )

        if st.button("🔗 Conectar y cargar", type="primary"):
            if not script_url:
                st.error("⚠️ Ingresa la URL del Apps Script")
                return

            with st.spinner("Conectando a Apps Script..."):
                try:
                    # TODO: Implementar conexión real a Apps Script
                    # Por ahora, mostrar mensaje
                    st.info("🔧 Conexión a Apps Script - Funcionalidad en desarrollo")
                    st.warning("Esta funcionalidad requiere implementación del endpoint en Apps Script")

                    # Placeholder para datos de ejemplo
                    sample_data = generate_sample_data()
                    st.session_state.aforos_data = sample_data

                    # Normalizar
                    normalizer = AforosNormalizer()
                    normalized = normalizer.normalize_from_dataframe(
                        pd.DataFrame(sample_data),
                        time_col='hora',
                        vehicle_cols=['auto', 'camion', 'bus', 'moto']
                    )
                    st.session_state.aforos_normalized = normalized

                    st.success("✅ Datos cargados desde Apps Script (simulado)")

                except Exception as e:
                    st.error(f"❌ Error conectando a Apps Script: {str(e)}")
                    logger.error(f"Apps Script connection error: {e}")


def load_from_file(container):
    """Cargar datos desde archivo local"""
    with container:
        st.subheader("📁 Archivo Local")

        uploaded_file = st.file_uploader(
            "Seleccionar archivo:",
            type=['csv', 'xlsx', 'xls'],
            help="Formatos soportados: CSV, Excel (.xlsx, .xls)"
        )

        if uploaded_file is not None:
            try:
                # Detectar tipo de archivo y cargar
                if uploaded_file.name.endswith('.csv'):
                    df = pd.read_csv(uploaded_file)
                else:
                    df = pd.read_excel(uploaded_file)

                st.success(f"✅ Archivo cargado: {uploaded_file.name}")
                st.info(f"📊 Dimensiones: {df.shape[0]} filas × {df.shape[1]} columnas")

                # Preview de datos
                st.subheader("👀 Preview de datos")
                st.dataframe(df.head(10), use_container_width=True)

                # Configuración de columnas
                st.subheader("⚙️ Configuración")
                col1, col2 = st.columns(2)

                with col1:
                    time_col = st.selectbox(
                        "Columna temporal:",
                        options=df.columns.tolist(),
                        help="Columna que contiene la hora/día del conteo"
                    )

                with col2:
                    # Detectar columnas de vehículos automáticamente
                    potential_vehicle_cols = [col for col in df.columns
                                            if col.lower() in ['auto', 'camion', 'bus', 'moto', 'bicicleta', 'camioneta', 'peaton']]
                    vehicle_cols = st.multiselect(
                        "Columnas de vehículos:",
                        options=df.columns.tolist(),
                        default=potential_vehicle_cols,
                        help="Columnas que contienen conteos por tipo de vehículo"
                    )

                # Normalizar
                if st.button("🔄 Normalizar datos", type="primary"):
                    with st.spinner("Normalizando datos..."):
                        try:
                            normalizer = AforosNormalizer()
                            normalized = normalizer.normalize_from_dataframe(
                                df, time_col=time_col, vehicle_cols=vehicle_cols
                            )

                            # Guardar en session_state
                            st.session_state.aforos_data = df.to_dict('records')
                            st.session_state.aforos_normalized = normalized

                            st.success("✅ Datos normalizados exitosamente")

                        except Exception as e:
                            st.error(f"❌ Error en normalización: {str(e)}")
                            logger.error(f"Normalization error: {e}")

            except Exception as e:
                st.error(f"❌ Error leyendo archivo: {str(e)}")
                logger.error(f"File reading error: {e}")


def display_results(container):
    """Mostrar resultados de normalización"""
    if st.session_state.aforos_normalized is not None:
        with container:
            st.divider()
            st.subheader("📈 Resultados de Normalización")

            normalized = st.session_state.aforos_normalized

            # Métricas principales
            col1, col2, col3, col4 = st.columns(4)

            with col1:
                st.metric("PHF", f"{normalized['phf']:.3f}")

            with col2:
                st.metric("HMD", normalized['hmd'])

            with col3:
                summary = normalized['summary']
                st.metric("Pico Horario", f"{summary['peak_hour_volume']:.0f} veh/h")

            with col4:
                st.metric("Total Vehículos", f"{summary['total_vehicles']:.0f}")

            # Tabs para diferentes vistas
            tab1, tab2, tab3 = st.tabs(["📊 Datos Normalizados", "📈 VPH por Hora", "📋 Metadatos"])

            with tab1:
                df_normalized = pd.DataFrame(normalized['data_normalized'])
                st.dataframe(df_normalized, use_container_width=True)

                # Botón para descargar CSV normalizado
                csv_data = df_normalized.to_csv(index=False).encode('utf-8')
                st.download_button(
                    label="📥 Descargar CSV Normalizado",
                    data=csv_data,
                    file_name="aforos_normalizados.csv",
                    mime="text/csv"
                )

            with tab2:
                vph_data = normalized['vph_by_hour']
                if isinstance(vph_data, dict):
                    vph_df = pd.DataFrame(list(vph_data.items()), columns=['Hora', 'VPH'])
                else:
                    vph_df = pd.DataFrame(vph_data)

                st.bar_chart(vph_df.set_index('Hora') if 'Hora' in vph_df.columns else vph_df)

            with tab3:
                st.json(normalized['metadata'])

            # Información para siguientes pasos
            st.info("""
            ✅ **Datos normalizados listos para verificación**

            Ve a la página **"07_Verificador"** para validar la calidad de los datos
            antes de generar el PMT.
            """)


def generate_sample_data() -> list:
    """Generar datos de ejemplo para desarrollo"""
    import random
    from datetime import time

    data = []
    vehicle_types = ['auto', 'camion', 'bus', 'moto']

    # Generar datos para 24 horas
    for hour in range(24):
        record = {'hora': f"{hour:02d}:00"}

        # Generar conteos con patrón realista (pico en horas pico)
        base_multiplier = 1.0
        if 7 <= hour <= 9 or 17 <= hour <= 19:  # Horas pico
            base_multiplier = 2.5
        elif 10 <= hour <= 16:  # Horas valle
            base_multiplier = 1.2
        else:  # Noche/madrugada
            base_multiplier = 0.3

        for vehicle in vehicle_types:
            # Distribución realista por tipo de vehículo
            if vehicle == 'auto':
                count = int(random.gauss(100, 20) * base_multiplier)
            elif vehicle == 'camion':
                count = int(random.gauss(10, 3) * base_multiplier)
            elif vehicle == 'bus':
                count = int(random.gauss(5, 2) * base_multiplier)
            else:  # moto
                count = int(random.gauss(15, 5) * base_multiplier)

            record[vehicle] = max(0, count)  # No valores negativos

        data.append(record)

    return data


if __name__ == "__main__":
    main()