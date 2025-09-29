"""
07_Verificador.py - Verificador de calidad de aforos para Panorama PMT

Esta página valida la calidad de los datos normalizados SIN mostrar valores numéricos.
Solo indica qué está mal y cómo corregirlo, manteniendo confidencialidad.
"""

import streamlit as st
import pandas as pd
from typing import List, Dict, Any, Tuple
import logging

# Importar módulos del proyecto
try:
    from aforos.normalize import AforosNormalizer
    from aforos.verifier import AforosVerifier
except ImportError:
    # Fallback para desarrollo
    import sys
    sys.path.append('.')
    from panorama_local.aforos.normalize import AforosNormalizer
    from panorama_local.aforos.verifier import AforosVerifier

logger = logging.getLogger(__name__)


def main():
    st.title("🔍 Verificador de Aforos")
    st.markdown("""
    **Validación de calidad de datos**

    Esta herramienta verifica la calidad de los aforos normalizados **sin exponer valores numéricos**.
    Solo indica hallazgos y recomendaciones para corrección.
    """)

    # Verificar que hay datos normalizados
    if 'aforos_normalized' not in st.session_state or st.session_state.aforos_normalized is None:
        st.warning("⚠️ No hay datos normalizados. Ve a **'06_Cargar_Aforos'** primero.")
        return

    # Botón para ejecutar verificación
    if st.button("🔍 Ejecutar Verificación", type="primary"):
        with st.spinner("Verificando calidad de datos..."):
            try:
                verifier = AforosVerifier()
                normalized_data = st.session_state.aforos_normalized
                results = verifier.verify_data(normalized_data)

                # Guardar resultados en session_state
                st.session_state.aforos_verification = results

                st.success("✅ Verificación completada")

            except Exception as e:
                st.error(f"❌ Error en verificación: {str(e)}")
                logger.error(f"Verification error: {e}")
                return

    # Mostrar resultados si existen
    if 'aforos_verification' in st.session_state:
        display_verification_results(st.session_state.aforos_verification)


def display_verification_results(results: Dict[str, Any]):
    """Mostrar resultados de verificación"""

    # Score general
    score = results['quality_score']
    can_generate = results['can_generate_pmt']

    # Color del score
    if score >= 80:
        score_color = "🟢"
    elif score >= 60:
        score_color = "🟡"
    else:
        score_color = "🔴"

    st.header(f"📊 Score de Calidad: {score_color} {score}/100")

    # Estado de generación PMT
    if can_generate:
        st.success("✅ **Datos aptos para generar PMT**")
    else:
        st.error("❌ **Datos NO aptos para generar PMT - resolver problemas críticos**")

    # Hallazgos por severidad
    findings = results['findings']

    if findings['critical']:
        st.subheader("🔴 Problemas Críticos")
        for finding in findings['critical']:
            st.error(f"• {finding}")

    if findings['warning']:
        st.subheader("🟡 Advertencias")
        for finding in findings['warning']:
            st.warning(f"• {finding}")

    if findings['info']:
        st.subheader("ℹ️ Información")
        for finding in findings['info']:
            st.info(f"• {finding}")

    # Recomendaciones
    if results['recommendations']:
        st.subheader("💡 Recomendaciones")
        for rec in results['recommendations']:
            st.write(rec)

    # Próximos pasos
    st.divider()
    st.subheader("🎯 Próximos Pasos")

    if can_generate:
        st.info("""
        **✅ Listo para generar PMT**

        Ve a la página **"08_Generador_PMT"** para crear el paquete completo
        con archivos normalizados y metadatos.
        """)

        if st.button("➡️ Ir a Generador PMT", type="primary"):
            st.switch_page("pages/08_Generador_PMT.py")

    else:
        st.warning("""
        **⚠️ Acción requerida**

        Corrige los problemas críticos identificados antes de continuar.
        Revisa los datos originales o contacta al responsable de los aforos.
        """)


if __name__ == "__main__":
    main()