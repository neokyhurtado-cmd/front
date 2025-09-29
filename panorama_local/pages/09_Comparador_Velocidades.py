"""
09_Comparador_Velocidades.py - Comparador de velocidades con Google Distance Matrix
"""

import streamlit as st
import pandas as pd
from typing import List, Tuple, Dict, Any
import logging
from datetime import datetime

# Importar módulos del proyecto
try:
    from panorama_local.integrations.google_matrix import GoogleMatrixClient, load_api_key
    from panorama_local.modules.crm import CRM
except ImportError:
    # Fallback para desarrollo
    import sys
    sys.path.append('.')
    from panorama_local.integrations.google_matrix import GoogleMatrixClient, load_api_key
    from panorama_local.modules.crm import CRM

logger = logging.getLogger(__name__)

def main():
    st.title("🚗 Comparador de Velocidades")
    st.markdown("""
    **Comparación de tiempos de viaje**

    Consulta la API de Google Distance Matrix para comparar tiempos de viaje
    entre pares de origen-destino, considerando condiciones de tráfico actuales.
    """)

    # Verificar autenticación
    crm = CRM()
    if not crm.is_authenticated():
        st.warning("⚠️ Debes iniciar sesión para usar el comparador de velocidades.")
        return

    current_user = crm.current_user()
    username = current_user['username']

    # Configuración de API
    st.header("🔑 Configuración de API")

    # Cargar API key desde secrets o input manual
    api_key = load_api_key()
    if not api_key:
        api_key = st.text_input("Google Distance Matrix API Key", type="password",
                               help="Ingresa tu clave de API de Google Maps. Se recomienda guardarla en st.secrets")
        if not api_key:
            st.warning("⚠️ Ingresa una API Key para continuar.")
            return

    # Inicializar cliente
    client = GoogleMatrixClient(api_key)

    # Sección de entrada de datos
    st.header("📍 Pares Origen-Destino")

    input_method = st.radio("Método de entrada", ["Manual", "CSV"], horizontal=True)

    od_pairs: List[Tuple[str, str]] = []

    if input_method == "Manual":
        st.subheader("Ingreso Manual")

        # Form para añadir pares
        with st.form("add_od_pair"):
            col1, col2 = st.columns(2)
            with col1:
                origin = st.text_input("Origen", placeholder="Ej: Calle 100 #15-20, Bogotá")
            with col2:
                destination = st.text_input("Destino", placeholder="Ej: Carrera 7 #120-30, Bogotá")

            submitted = st.form_submit_button("➕ Añadir Par")
            if submitted and origin and destination:
                od_pairs.append((origin, destination))
                st.success(f"✅ Par añadido: {origin} → {destination}")

        # Mostrar pares añadidos
        if 'od_pairs' not in st.session_state:
            st.session_state.od_pairs = []

        if st.button("➕ Añadir Par Actual"):
            if origin and destination:
                st.session_state.od_pairs.append((origin, destination))
                st.success(f"✅ Par añadido: {origin} → {destination}")
                st.rerun()

        od_pairs = st.session_state.od_pairs

    else:  # CSV
        st.subheader("Carga desde CSV")

        uploaded_file = st.file_uploader("Seleccionar archivo CSV", type=['csv'],
                                       help="El CSV debe tener columnas 'origin' y 'destination'")

        if uploaded_file is not None:
            try:
                df = pd.read_csv(uploaded_file)
                if 'origin' not in df.columns or 'destination' not in df.columns:
                    st.error("❌ El CSV debe contener columnas 'origin' y 'destination'")
                else:
                    od_pairs = list(zip(df['origin'], df['destination']))
                    st.success(f"✅ Cargados {len(od_pairs)} pares desde CSV")
            except Exception as e:
                st.error(f"❌ Error al cargar CSV: {e}")

    # Mostrar pares actuales
    if od_pairs:
        st.subheader("📋 Pares a Consultar")
        pairs_df = pd.DataFrame(od_pairs, columns=['Origen', 'Destino'])
        st.dataframe(pairs_df, use_container_width=True)

        # Botón para ejecutar consulta
        if st.button("🚀 Consultar Velocidades", type="primary"):
            with st.spinner("Consultando Google Distance Matrix..."):
                try:
                    results = client.process_od_pairs(od_pairs)

                    if results:
                        # Guardar resultados en session_state
                        st.session_state.speed_results = results

                        # Guardar en CRM
                        saved_count = 0
                        for result in results:
                            if result.get('status') == 'OK':
                                crm.add_speed_comparison(
                                    username=username,
                                    origin=result['origin'],
                                    destination=result['destination'],
                                    distance_meters=result.get('distance_meters'),
                                    duration_seconds=result.get('duration_seconds'),
                                    duration_traffic_seconds=result.get('duration_traffic_seconds')
                                )
                                saved_count += 1

                        st.success(f"✅ Consulta completada. {saved_count} resultados guardados en CRM.")

                    else:
                        st.error("❌ No se obtuvieron resultados válidos.")

                except Exception as e:
                    st.error(f"❌ Error en consulta: {e}")
                    logger.error(f"Speed comparison error: {e}")

    # Mostrar resultados
    if 'speed_results' in st.session_state:
        display_results(st.session_state.speed_results)

    # Historial de comparaciones
    st.header("📚 Historial de Comparaciones")

    if st.button("🔄 Actualizar Historial"):
        history = crm.list_speed_comparisons(username)
        if history:
            st.subheader(f"Últimas {len(history)} comparaciones")

            # Convertir a DataFrame para mejor visualización
            history_df = pd.DataFrame(history)
            history_df['created_at'] = pd.to_datetime(history_df['created_at']).dt.strftime('%Y-%m-%d %H:%M')

            # Calcular tiempos en minutos
            if 'duration_seconds' in history_df.columns:
                history_df['duration_min'] = (history_df['duration_seconds'] / 60).round(1)
            if 'duration_traffic_seconds' in history_df.columns:
                history_df['duration_traffic_min'] = (history_df['duration_traffic_seconds'] / 60).round(1)

            # Mostrar tabla
            display_cols = ['created_at', 'origin', 'destination', 'distance_meters',
                          'duration_min', 'duration_traffic_min']
            available_cols = [col for col in display_cols if col in history_df.columns]
            st.dataframe(history_df[available_cols], use_container_width=True)
        else:
            st.info("ℹ️ No hay comparaciones guardadas aún.")

def display_results(results: List[Dict[str, Any]]):
    """Mostrar resultados de la consulta"""

    st.header("📊 Resultados de Velocidades")

    # Convertir a DataFrame
    df = pd.DataFrame(results)

    # Filtrar solo resultados OK
    ok_results = df[df['status'] == 'OK'].copy()

    if len(ok_results) == 0:
        st.warning("⚠️ No hay resultados válidos para mostrar.")
        return

    # Calcular métricas adicionales
    ok_results['distance_km'] = (ok_results['distance_meters'] / 1000).round(2)
    ok_results['duration_min'] = (ok_results['duration_seconds'] / 60).round(1)
    ok_results['duration_traffic_min'] = (ok_results['duration_traffic_seconds'] / 60).round(1)
    ok_results['traffic_delay_min'] = (ok_results['duration_traffic_min'] - ok_results['duration_min']).round(1)

    # Métricas generales
    col1, col2, col3, col4 = st.columns(4)
    with col1:
        st.metric("Total Consultas", len(df))
    with col2:
        st.metric("Resultados Válidos", len(ok_results))
    with col3:
        avg_distance = ok_results['distance_km'].mean()
        st.metric("Distancia Promedio", f"{avg_distance:.1f} km")
    with col4:
        avg_delay = ok_results['traffic_delay_min'].mean()
        st.metric("Retraso Promedio", f"{avg_delay:.1f} min")

    # Tabla de resultados
    st.subheader("📋 Detalle de Resultados")

    display_df = ok_results[[
        'origin', 'destination', 'distance_km',
        'duration_min', 'duration_traffic_min', 'traffic_delay_min'
    ]].rename(columns={
        'origin': 'Origen',
        'destination': 'Destino',
        'distance_km': 'Distancia (km)',
        'duration_min': 'Tiempo Libre (min)',
        'duration_traffic_min': 'Tiempo con Tráfico (min)',
        'traffic_delay_min': 'Retraso por Tráfico (min)'
    })

    st.dataframe(display_df, use_container_width=True)

    # Opciones de exportación
    st.subheader("💾 Exportar Resultados")

    col1, col2, col3 = st.columns(3)
    with col1:
        csv_data = display_df.to_csv(index=False)
        st.download_button(
            label="📄 Descargar CSV",
            data=csv_data,
            file_name=f"comparacion_velocidades_{datetime.now().strftime('%Y%m%d_%H%M')}.csv",
            mime="text/csv"
        )

    with col2:
        excel_data = display_df.to_excel(index=False, engine='openpyxl')
        st.download_button(
            label="📊 Descargar Excel",
            data=excel_data,
            file_name=f"comparacion_velocidades_{datetime.now().strftime('%Y%m%d_%H%M')}.xlsx",
            mime="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        )

    with col3:
        json_data = ok_results.to_json(orient='records', indent=2)
        st.download_button(
            label="📋 Descargar JSON",
            data=json_data,
            file_name=f"comparacion_velocidades_{datetime.now().strftime('%Y%m%d_%H%M')}.json",
            mime="application/json"
        )

if __name__ == "__main__":
    main()