import streamlit as st
import json
import os
import logging
import sys
sys.path.append(os.path.dirname(__file__))  # añade panorama_local/ al PYTHONPATH dinámicamente

# --- imports robustos para local y Streamlit Cloud ---
try:
    # cuando el paquete top-level está disponible (ejecución desde repo root)
    from panorama_local.modules import simulation, crm
    from panorama_local.capacity.core import compute_capacity, get_capacity_recommendations
    from panorama_local.exports.zip import export_projects_zip, fetch_project_files
    from panorama_local.exports.pdf import render_project_pdf
except ImportError:
    # fallback cuando el script se ejecuta dentro de panorama_local/
    from modules import simulation, crm
    from capacity.core import compute_capacity, get_capacity_recommendations
    from exports.zip import export_projects_zip, fetch_project_files
    from exports.pdf import render_project_pdf

# Configure logging
import os
import json
log_dir = os.path.join(os.path.dirname(__file__), "logs")
os.makedirs(log_dir, exist_ok=True)
log_file = os.path.join(log_dir, "panorama_app.log")

# Configure structured logging
class StructuredFormatter(logging.Formatter):
    def format(self, record):
        # Add structured fields to the log record
        if not hasattr(record, 'extra_data'):
            record.extra_data = {}

        # Create structured log entry
        log_entry = {
            'timestamp': self.formatTime(record),
            'level': record.levelname,
            'logger': record.name,
            'message': record.getMessage(),
            'module': record.module,
            'function': record.funcName,
            'line': record.lineno,
            **record.extra_data
        }

        # Add exception info if present
        if record.exc_info:
            log_entry['exception'] = self.formatException(record.exc_info)

        return json.dumps(log_entry, ensure_ascii=False)

# Configure logging with structured formatter
file_handler = logging.FileHandler(log_file, encoding='utf-8')
file_handler.setFormatter(StructuredFormatter())

console_handler = logging.StreamHandler()
console_handler.setFormatter(logging.Formatter(
    '%(asctime)s - %(name)s - %(levelname)s - %(message)s'
))

logging.basicConfig(
    level=logging.INFO,
    handlers=[file_handler, console_handler]
)
logger = logging.getLogger(__name__)

def validate_pmt_data(pmt_data):
    """
    Valida la estructura de un archivo PMT JSON.

    Args:
        pmt_data (dict): Datos del proyecto PMT

    Returns:
        tuple: (is_valid: bool, error_message: str or None)
    """
    if not isinstance(pmt_data, dict):
        return False, "El archivo debe contener un objeto JSON válido"

    # Validar campos opcionales pero recomendados
    errors = []

    # Validar 'plan' si existe
    if 'plan' in pmt_data:
        if not isinstance(pmt_data['plan'], str):
            errors.append("El campo 'plan' debe ser una cadena de texto")
        elif pmt_data['plan'] not in ['Free', 'Pro']:
            errors.append("El campo 'plan' debe ser 'Free' o 'Pro'")

    # Validar 'markers' si existe
    if 'markers' in pmt_data:
        if not isinstance(pmt_data['markers'], list):
            errors.append("El campo 'markers' debe ser una lista")
        else:
            # Validar que cada marker tenga estructura básica
            for i, marker in enumerate(pmt_data['markers']):
                if not isinstance(marker, dict):
                    errors.append(f"El marker {i} debe ser un objeto")
                    continue
                # Markers pueden tener diferentes estructuras, no validamos campos específicos

    # Validar que al menos tenga algún contenido útil
    has_content = any(key in pmt_data for key in ['markers', 'plan', 'rotulo', 'center'])
    if not has_content:
        errors.append("El archivo PMT debe contener al menos uno de: markers, plan, rotulo, center")

    if errors:
        return False, "Errores de validación:\n" + "\n".join(f"• {error}" for error in errors)

    return True, None

def main():
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

    st.sidebar.title("Panorama Ingeniería - PMT")
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
                            logger.info("User login successful", extra={'extra_data': {'event': 'auth_login', 'username': u, 'success': True}})
                        else:
                            st.error("Credenciales incorrectas o usuario no existe.")
                            logger.warning("Failed login attempt", extra={'extra_data': {'event': 'auth_login', 'username': u, 'success': False}})
                with tab2:
                    ru = st.text_input("Usuario (registro)", key="reg_user")
                    rp = st.text_input("Contraseña (registro)", type="password", key="reg_pass")
                    rplan = st.selectbox("Plan", ["Free", "Pro"], key="reg_plan")
                    if st.button("Registrarse"):
                        ok = crm_instance.register(ru, rp, rplan)
                        if ok:
                            st.success("Usuario creado y autenticado")
                            logger.info("New user registered", extra={'extra_data': {'event': 'auth_register', 'username': ru, 'plan': rplan, 'success': True}})
                        else:
                            st.error("El usuario ya existe")
                            logger.warning("Failed registration attempt", extra={'extra_data': {'event': 'auth_register', 'username': ru, 'plan': rplan, 'success': False}})
            else:
                user = crm_instance.current_user()
                # user is a dict {'username':..., 'plan':...}
                st.write(f"Usuario: **{user.get('username')}**")
                st.write(f"Plan: **{user.get('plan')}**")
                if user.get('plan') == "Free":
                    if st.button("Actualizar a Pro"):
                        crm_instance.set_plan(user.get('username'), "Pro")
                        st.experimental_rerun()
                if st.button("Cerrar sesión"):
                    user = crm_instance.current_user()
                    username = user.get('username') if user else 'unknown'
                    crm_instance.logout()
                    logger.info("User logout", extra={'extra_data': {'event': 'auth_logout', 'username': username}})
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
            st.write("---")
            st.markdown("**Simular impacto de tráfico**")
            # Option: select from uploaded file or from saved projects
            sim_source = st.radio("Fuente del proyecto", ["Cargar archivo", "Usar proyecto guardado"], index=0)

            selected_project = None
            uploaded = None
            if sim_source == "Cargar archivo":
                uploaded = st.file_uploader("Carga un JSON exportado desde PMT Builder", type=["json"], key="sim_upload")
                if uploaded is not None:
                    try:
                        pmt_data = json.load(uploaded)

                        # Validar la estructura del PMT
                        is_valid, error_msg = validate_pmt_data(pmt_data)
                        if not is_valid:
                            st.error(f"Archivo PMT inválido:\n{error_msg}")
                            st.info("**Formato esperado:** Objeto JSON con campos como 'markers' (lista), 'plan' ('Free'/'Pro'), 'rotulo', 'center'")
                            selected_project = None
                        else:
                            st.success("✅ Archivo PMT válido")
                            st.json(pmt_data)
                            selected_project = pmt_data

                            # Log successful import
                            logger.info("PMT file imported successfully", extra={'extra_data': {'event': 'pmt_import', 'keys_count': len(pmt_data), 'markers_count': len(pmt_data.get('markers', []))}})

                    except json.JSONDecodeError as e:
                        st.error(f"Error de formato JSON: {str(e)}")
                        st.info("Asegúrate de que el archivo sea un JSON válido exportado desde PMT Builder")
                        selected_project = None
                    except Exception as e:
                        st.error(f"Error leyendo archivo: {str(e)}")
                        selected_project = None
            else:
                # choose from saved projects
                if crm_instance.is_authenticated():
                    user = crm_instance.current_user()
                    projects = crm_instance.list_projects(user.get('username'))
                    choices = {str(p.get('id')): p.get('project') for p in projects} if projects else {}
                    if not choices:
                        st.info("No hay proyectos guardados. Sube un archivo o guarda uno primero.")
                    else:
                        sel = st.selectbox("Selecciona un proyecto guardado", options=list(choices.keys()))
                        selected_project = choices.get(sel)
                else:
                    st.info("Inicia sesión para usar proyectos guardados.")

            # Simulation engine and execution
            engine = st.selectbox("Motor de simulación", ["cityflow", "uxsim"], key="sim_engine")
            if st.button("Ejecutar simulación"):
                if selected_project is None:
                    st.error("Selecciona o carga un proyecto antes de ejecutar la simulación.")
                else:
                    try:
                        res = simulation.run_simulation(selected_project, engine=engine)
                        st.success("Simulación completada")
                        st.json(res)
                        logger.info("Simulation completed", extra={'extra_data': {'event': 'simulation_run', 'engine': engine, 'markers_count': len(selected_project.get('markers', [])), 'success': True}})
                    except Exception as e:
                        st.error(f"Error en simulación: {str(e)}")
                        logger.error("Simulation failed", extra={'extra_data': {'event': 'simulation_run', 'engine': engine, 'error': str(e), 'success': False}})
                    # If the project came from an uploaded file, offer to save with metadata
                    if uploaded is not None and crm_instance.is_authenticated():
                        st.write("Guardar proyecto importado en la cuenta")
                        meta_name = st.text_input("Nombre del proyecto", value="", key="meta_name")
                        meta_notes = st.text_area("Notas (opcionales)", value="", key="meta_notes")
                        if st.button("Guardar proyecto en mi cuenta", key="save_after_sim"):
                            user = crm_instance.current_user()
                            proj_id = crm_instance.add_project_with_meta(user.get('username'), selected_project, name=meta_name or None, notes=meta_notes or None)
                            st.success(f"Proyecto guardado (id={proj_id})")
                            logger.info("Project saved", extra={'extra_data': {'event': 'project_save', 'user': user.get('username'), 'project_id': proj_id, 'name': meta_name, 'markers_count': len(selected_project.get('markers', []))}})
                            st.success(f"Proyecto guardado (id={proj_id})")
                    elif uploaded is not None and not crm_instance.is_authenticated():
                        st.info("Inicia sesión para guardar el proyecto en tu cuenta.")

            if crm_instance.is_authenticated():
                user = crm_instance.current_user()
                st.subheader("Proyectos guardados")
                projects = crm_instance.list_projects(user.get('username'))

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
                                    logger.info("ZIP export completed", extra={'extra_data': {'event': 'export_zip', 'user': user.get('username'), 'projects_count': len(project_ids), 'success': True}})
                                else:
                                    st.error("Error: ZIP vacío generado")
                                    logger.error("ZIP export failed - empty result", extra={'extra_data': {'event': 'export_zip', 'user': user.get('username'), 'projects_count': len(project_ids), 'success': False}})
                            except Exception as e:
                                logger.error("ZIP export exception", extra={'extra_data': {'event': 'export_zip', 'user': user.get('username'), 'error': str(e), 'success': False}})
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
                                        logger.info("PDF export completed", extra={'extra_data': {'event': 'export_pdf', 'user': user.get('username'), 'project_id': project_id, 'success': True}})
                                    else:
                                        st.error("Error: PDF vacío generado")
                                        logger.error("PDF export failed - empty result", extra={'extra_data': {'event': 'export_pdf', 'user': user.get('username'), 'project_id': project_id, 'success': False}})
                                else:
                                    st.error("Proyecto no encontrado")
                            except Exception as e:
                                logger.error("PDF export exception", extra={'extra_data': {'event': 'export_pdf', 'user': user.get('username'), 'project_id': project_id, 'error': str(e), 'success': False}})
                                st.error(f"Error generando PDF: {str(e)}")

                if not projects:
                    st.write("No hay proyectos guardados.")
                else:
                    for pr in projects:
                        pid = pr.get('id')
                        name = pr.get('name') or f"Proyecto {pid}"
                        notes = pr.get('notes') or ""
                        created_at = pr.get('created_at')
                        st.write(f"{name} (id={pid})")
                        if created_at:
                            st.caption(f"Creado: {created_at}")
                        if notes:
                            st.markdown(f"> {notes}")
                        st.json(pr.get('project'))
                        try:
                            import json as _json
                            payload = _json.dumps({
                                "meta": {"id": pid, "name": name, "notes": notes, "created_at": created_at},
                                "project": pr.get('project'),
                            }, ensure_ascii=False, indent=2)
                            safe_name = (name or f"project_{pid}").replace(" ", "_")
                            filename = f"{safe_name}_{pid}.json"
                            st.download_button(label="Exportar proyecto (JSON)", data=payload, file_name=filename, mime="application/json")
                            logger.info("JSON export completed", extra={'extra_data': {'event': 'export_json', 'user': user.get('username'), 'project_id': pid, 'filename': filename}})
                        except Exception as _e:
                            st.warning(f"No se pudo preparar export: {_e}")
                        # Delete with confirmation checkbox to avoid accidents
                        confirm_key = f"confirm_delete_{pid}"
                        if st.checkbox("Confirmar borrado", key=confirm_key):
                            if st.button("Eliminar proyecto", key=f"del_btn_{pid}"):
                                ok = crm_instance.delete_project(user.get('username'), pid)
                                if ok:
                                    st.success(f"Proyecto {pid} eliminado")
                                    logger.info("Project deleted", extra={'extra_data': {'event': 'project_delete', 'user': user.get('username'), 'project_id': pid, 'success': True}})
                                    st.experimental_rerun()
                                else:
                                    st.error("No se pudo eliminar el proyecto")
                                    logger.error("Project delete failed", extra={'extra_data': {'event': 'project_delete', 'user': user.get('username'), 'project_id': pid, 'success': False}})


if __name__ == "__main__":
    main()
