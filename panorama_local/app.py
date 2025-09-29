import streamlit as st
import json
import os
import logging
from modules import simulation, crm
from capacity.core import compute_capacity, get_capacity_recommendations
from exports.zip import export_projects_zip, fetch_project_files
from exports.pdf import render_project_pdf

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Configure database URL from secrets/environment
DB_URL = st.secrets.get("DATABASE_URL") or os.getenv("DATABASE_URL") or "sqlite:///./data/app.db"

# Initialize CRM with proper database path
if "crm_instance" not in st.session_state:
    # Convert URL to file path for SQLite
    if DB_URL.startswith("sqlite:///"):
        db_path = DB_URL.replace("sqlite:///", "")
        if not os.path.isabs(db_path):
            db_path = os.path.join(os.path.dirname(__file__), db_path)
    else:
        db_path = os.path.join(os.path.dirname(__file__), "data", "panorama_crm.db")

    st.session_state.crm_instance = crm.CRM(db_path=db_path)

# Use session-based CRM instance
crm_instance = st.session_state.crm_instance

st.set_page_config(
    page_title="Panorama Ingeniería - PMT",
    page_icon="🚗",
    layout="wide",
    initial_sidebar_state="expanded"
)
option = st.sidebar.title("Panorama Ingeniería - PMT")
option = st.sidebar.selectbox("Selecciona una herramienta", ["Análisis de capacidad", "Análisis de tráfico", "PMT Builder", "Simulación y CRM"])

if option == "Análisis de capacidad":
    st.header("Análisis de Capacidad de Tráfico")
    st.markdown("""
    **Herramienta de análisis de capacidad basada en HCM (Highway Capacity Manual)**

    Calcula métricas clave como ratio de saturación (x), demora (d) y nivel de servicio (LOS).
    """)

    col1, col2, col3 = st.columns(3)

    with col1:
        st.subheader("Parámetros de Flujo")
        v = st.number_input("Flujo v (veh/h)", 0, 5000, 1200, 50,
                          help="Tasa de flujo vehicular por hora")
        c = st.number_input("Capacidad c (veh/h)", 1, 5000, 1800, 50,
                          help="Capacidad del tramo/cruce")

    with col2:
        st.subheader("Parámetros de Saturación")
        s = st.number_input("Flujo de saturación s (veh/h/carril)", 100, 3000, 1900, 50,
                          help="Flujo de saturación por carril")

    with col3:
        st.subheader("Control Semafórico (opcional)")
        use_signal = st.checkbox("Intersección semaforizada")
        if use_signal:
            g = st.number_input("Verde efectivo g (s)", 1, 300, 30, 5,
                              help="Tiempo de verde efectivo por ciclo")
            C = st.number_input("Ciclo total C (s)", 30, 300, 120, 10,
                              help="Longitud total del ciclo semafórico")
        else:
            g = None
            C = None

    # Compute results
    if st.button("Calcular Capacidad", type="primary"):
        results = compute_capacity(v=v, c=c, s=s, g=g, C=C)
        recommendations = get_capacity_recommendations(results)

        st.success("Análisis completado")

        # Display results
        col_res1, col_res2, col_res3 = st.columns(3)

        with col_res1:
            st.metric("Ratio de Saturación (x)", f"{results['x']:.2f}",
                     help="x = v/c. < 0.85: bueno, > 1.0: sobresaturado")
            st.metric("Demora (d)", f"{results['d']:.1f} s/veh",
                     help="Demora promedio por vehículo")

        with col_res2:
            st.metric("Nivel de Servicio", results['los'],
                     help="A: excelente, F: deficiente")

        with col_res3:
            flags = results['flags']
            if "critical" in flags:
                st.error("🚨 Estado crítico")
            elif "warning" in flags:
                st.warning("⚠️ Cercano a capacidad")
            else:
                st.success("✅ Estado normal")

        # Recommendations
        if recommendations:
            st.subheader("Recomendaciones")
            for rec in recommendations:
                st.write(rec)

        # Detailed results
        with st.expander("Resultados detallados"):
            st.json(results)

elif option == "Análisis de tráfico":
    st.header("Análisis de tráfico")
    st.info("Herramienta en construcción.")

elif option == "PMT Builder":
    st.header("PMT Builder")
    st.info("Usa la herramienta existente para dibujar y exportar PMT (no se modifica).")
    st.write("Si ya usaste la herramienta, exporta el JSON y vuelve a esta sección para simularlo y guardarlo en tu cuenta.")

elif option == "Simulación y CRM":
    st.header("Simulación y CRM")
    col1, col2 = st.columns([2, 3])

    with col1:
        st.subheader("Autenticación")
        # Mostrar path de la DB para debugging
        try:
            db_path = os.path.abspath(os.path.join(os.path.dirname(__file__), 'data', 'panorama_crm.db'))
        except Exception:
            db_path = 'n/a'
        st.caption(f"DB: {db_path}")
        if not crm_instance.is_authenticated():
            tab1, tab2 = st.tabs(["Iniciar sesión", "Registrarse"])
            with tab1:
                u = st.text_input("Usuario", key="login_user")
                p = st.text_input("Contraseña", type="password", key="login_pass")
                if st.button("Iniciar sesión"):
                    ok = crm_instance.login(u, p)
                    if ok:
                        st.success("Sesión iniciada")
                    else:
                        st.error("Credenciales incorrectas o usuario no existe.")
            with tab2:
                ru = st.text_input("Usuario (registro)", key="reg_user")
                rp = st.text_input("Contraseña (registro)", type="password", key="reg_pass")
                rplan = st.selectbox("Plan", ["Free", "Pro"], key="reg_plan")
                if st.button("Registrarse"):
                    ok = crm_instance.register(ru, rp, rplan)
                    if ok:
                        st.success("Usuario creado y autenticado")
                    else:
                        st.error("El usuario ya existe")
        else:
            user = crm_instance.current_user()
            st.write(f"Usuario: **{user['username']}**")
            st.write(f"Plan: **{user['plan']}**")
            if user['plan'] == "Free":
                if st.button("Actualizar a Pro"):
                    crm_instance.set_plan(user['username'], "Pro")
                    st.experimental_rerun()
            if st.button("Cerrar sesión"):
                crm_instance.logout()
                st.experimental_rerun()

        # Clear DB (solo para testing) - muestra bajo autenticación para evitar abusos
        if crm_instance.is_authenticated():
            if st.button("Clear DB (delete all)"):
                if st.confirm("¿Borrar toda la base de datos? Esta acción es irreversible."):
                    try:
                        # close connections by re-instantiating CRM after delete
                        dbfile = os.path.join(os.path.dirname(__file__), 'data', 'panorama_crm.db')
                        if os.path.exists(dbfile):
                            os.remove(dbfile)
                        st.success('DB eliminada. Recargando...')
                        st.experimental_rerun()
                    except Exception as e:
                        st.error(f'Error borrando DB: {e}')

    with col2:
        st.subheader("Simular proyecto PMT")
        selected_project = st.session_state.get("selected_project")
        if selected_project:
            st.info("Proyecto seleccionado de guardados. Puedes simularlo directamente o cargar otro.")
            pmt_data = selected_project
            st.json(pmt_data)
            if st.button("Limpiar selección"):
                del st.session_state.selected_project
                st.experimental_rerun()
        else:
            uploaded = st.file_uploader("Carga un JSON exportado desde PMT Builder", type=["json"]) 
            if uploaded is not None:
                try:
                    pmt_data = json.load(uploaded)
                    st.json(pmt_data)
                except Exception as e:
                    st.error(f"Error leyendo JSON: {e}")
                    pmt_data = None
            else:
                st.info("Sube aquí un JSON exportado desde PMT Builder para simularlo.")
                pmt_data = None
        
        if pmt_data is not None:
            engine = st.selectbox("Motor de simulación", ["cityflow", "uxsim"]) 
            if st.button("Ejecutar simulación"):
                res = simulation.run_simulation(pmt_data, engine=engine)
                st.success("Simulación completada")
                st.json(res)
                if crm_instance.is_authenticated():
                    st.subheader("Guardar proyecto")
                    with st.expander("Guardar en mi cuenta"):
                        proj_name = st.text_input("Nombre del proyecto", key="proj_name")
                        proj_notes = st.text_area("Notas (opcional)", key="proj_notes")
                        if st.button("Guardar proyecto"):
                            user = crm_instance.current_user()
                            project_id = crm_instance.add_project(user['username'], pmt_data, proj_name, proj_notes)
                            if project_id > 0:
                                st.success("Proyecto guardado")
                            else:
                                st.error("Error guardando proyecto")
                else:
                    st.info("Inicia sesión para poder guardar proyectos en tu cuenta.")

        if crm_instance.is_authenticated():
            user = crm_instance.current_user()
            st.subheader("Proyectos guardados")
            projects = crm_instance.list_projects(user['username'])

            # Multi-project export section
            if projects:
                st.subheader("Exportación masiva")
                col_zip, col_pdf = st.columns(2)

                with col_zip:
                    selected_projects = st.multiselect(
                        "Seleccionar proyectos para ZIP",
                        options=[f"{p['id']}: {p.get('name', 'Sin nombre')}" for p in projects],
                        help="Selecciona múltiples proyectos para exportar en un archivo ZIP"
                    )
                    if st.button("Descargar ZIP seleccionado") and selected_projects:
                        try:
                            # Extract project IDs
                            project_ids = [s.split(':')[0] for s in selected_projects]
                            
                            # Limit to prevent abuse
                            MAX_PROJECTS = 10
                            if len(project_ids) > MAX_PROJECTS:
                                st.error(f"Máximo {MAX_PROJECTS} proyectos por ZIP")
                                project_ids = project_ids[:MAX_PROJECTS]
                            
                            zip_data = export_projects_zip(project_ids, fetch_project_files)
                            if len(zip_data) > 0:
                                st.download_button(
                                    label="📦 Descargar ZIP",
                                    data=zip_data,
                                    file_name=f"proyectos_pmt_{len(project_ids)}_items.zip",
                                    mime="application/zip"
                                )
                            else:
                                st.error("Error: ZIP vacío generado")
                        except Exception as e:
                            logger.error(f"Error generando ZIP: {e}")
                            st.error(f"Error generando ZIP: {str(e)}")

                with col_pdf:
                    pdf_project = st.selectbox(
                        "Seleccionar proyecto para PDF",
                        options=[""] + [f"{p['id']}: {p.get('name', 'Sin nombre')}" for p in projects],
                        help="Selecciona un proyecto para generar reporte PDF"
                    )
                    if pdf_project and st.button("Generar PDF"):
                        try:
                            project_id = pdf_project.split(':')[0]
                            # Find project data
                            selected_proj = next((p for p in projects if str(p['id']) == project_id), None)
                            if selected_proj:
                                # Create summary for PDF
                                summary = {
                                    "name": selected_proj.get("name", f"Proyecto {project_id}"),
                                    "notes": selected_proj.get("notes", ""),
                                    "created_at": selected_proj.get("created_at", ""),
                                    "capacity": {"v": 1200, "c": 1800, "x": 0.67, "d": 5.2, "los": "C"}
                                }
                                pdf_data = render_project_pdf(project_id, summary)
                                if len(pdf_data) > 0:
                                    st.download_button(
                                        label="📄 Descargar PDF",
                                        data=pdf_data,
                                        file_name=f"reporte_pmt_{project_id}.pdf",
                                        mime="application/pdf"
                                    )
                                else:
                                    st.error("Error: PDF vacío generado")
                            else:
                                st.error("Proyecto no encontrado")
                        except Exception as e:
                            logger.error(f"Error generando PDF: {e}")
                            st.error(f"Error generando PDF: {str(e)}")

            if not projects:
                st.write("No hay proyectos guardados.")
            else:
                for pr in projects:
                    with st.expander(f"Proyecto: {pr.get('name', f'ID {pr['id']}')}"):
                        st.write(f"**Nombre:** {pr.get('name', 'Sin nombre')}")
                        st.write(f"**Notas:** {pr.get('notes', 'Sin notas')}")
                        st.write(f"**Creado:** {pr.get('created_at', 'N/A')}")
                        st.json(pr["project"])
                        col_a, col_b, col_c = st.columns(3)
                        with col_a:
                            if st.button(f"Seleccionar para simular", key=f"select_{pr['id']}"):
                                st.session_state.selected_project = pr["project"]
                                st.success("Proyecto seleccionado para simular")
                        with col_b:
                            try:
                                import json as _json
                                payload = _json.dumps(pr["project"], ensure_ascii=False, indent=2)
                                filename = f"{pr.get('name', f'project_{pr['id']}')}.json".replace(" ", "_").replace("/", "_")
                                st.download_button(label="Exportar (JSON)", data=payload, file_name=filename, mime="application/json", key=f"export_{pr['id']}")
                            except Exception as _e:
                                st.warning(f"No se pudo preparar export: {_e}")
                        with col_c:
                            if st.button(f"Eliminar", key=f"delete_{pr['id']}"):
                                if st.confirm(f"¿Eliminar proyecto '{pr.get('name', f'ID {pr['id']}')}'?"):
                                    deleted = crm_instance.delete_project(user['username'], pr["id"])
                                    if deleted:
                                        st.success("Proyecto eliminado")
                                        st.experimental_rerun()
                                    else:
                                        st.error("Error eliminando proyecto")