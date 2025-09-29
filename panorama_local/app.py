import streamlit as st
import json
import os
from modules import simulation, crm

st.set_page_config(page_title="Panorama Local", layout="wide")

st.sidebar.title("Panorama Local")
option = st.sidebar.selectbox("Selecciona una herramienta", ["Análisis de tráfico", "PMT Builder", "Simulación y CRM"])

if option == "Análisis de tráfico":
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
        if not crm.global_crm.is_authenticated():
            tab1, tab2 = st.tabs(["Iniciar sesión", "Registrarse"])
            with tab1:
                u = st.text_input("Usuario", key="login_user")
                p = st.text_input("Contraseña", type="password", key="login_pass")
                if st.button("Iniciar sesión"):
                    ok = crm.global_crm.login(u, p)
                    if ok:
                        st.success("Sesión iniciada")
                    else:
                        st.error("Credenciales incorrectas o usuario no existe.")
            with tab2:
                ru = st.text_input("Usuario (registro)", key="reg_user")
                rp = st.text_input("Contraseña (registro)", type="password", key="reg_pass")
                rplan = st.selectbox("Plan", ["Free", "Pro"], key="reg_plan")
                if st.button("Registrarse"):
                    ok = crm.global_crm.register(ru, rp, rplan)
                    if ok:
                        st.success("Usuario creado y autenticado")
                    else:
                        st.error("El usuario ya existe")
        else:
            user = crm.global_crm.current_user()
            # user is a dict {'username':..., 'plan':...}
            st.write(f"Usuario: **{user.get('username')}**")
            st.write(f"Plan: **{user.get('plan')}**")
            if user.get('plan') == "Free":
                if st.button("Actualizar a Pro"):
                    crm.global_crm.set_plan(user.get('username'), "Pro")
                    st.experimental_rerun()
            if st.button("Cerrar sesión"):
                crm.global_crm.logout()
                st.experimental_rerun()

        # Clear DB (solo para testing) - muestra bajo autenticación para evitar abusos
        if crm.global_crm.is_authenticated():
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
                    st.json(pmt_data)
                    selected_project = pmt_data
                except Exception as e:
                    st.error(f"Error leyendo JSON: {e}")
        else:
            # choose from saved projects
            if crm.global_crm.is_authenticated():
                user = crm.global_crm.current_user()
                projects = crm.global_crm.list_projects(user.get('username'))
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
                res = simulation.run_simulation(selected_project, engine=engine)
                st.success("Simulación completada")
                st.json(res)
                # If the project came from an uploaded file, offer to save with metadata
                if uploaded is not None and crm.global_crm.is_authenticated():
                    st.write("Guardar proyecto importado en la cuenta")
                    meta_name = st.text_input("Nombre del proyecto", value="", key="meta_name")
                    meta_notes = st.text_area("Notas (opcionales)", value="", key="meta_notes")
                    if st.button("Guardar proyecto en mi cuenta", key="save_after_sim"):
                        user = crm.global_crm.current_user()
                        proj_id = crm.global_crm.add_project_with_meta(user.get('username'), selected_project, name=meta_name or None, notes=meta_notes or None)
                        st.success(f"Proyecto guardado (id={proj_id})")
                elif uploaded is not None and not crm.global_crm.is_authenticated():
                    st.info("Inicia sesión para guardar el proyecto en tu cuenta.")

        if crm.global_crm.is_authenticated():
            user = crm.global_crm.current_user()
            st.subheader("Proyectos guardados")
            projects = crm.global_crm.list_projects(user.get('username'))
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
                    except Exception as _e:
                        st.warning(f"No se pudo preparar export: {_e}")
                    # Delete with confirmation checkbox to avoid accidents
                    confirm_key = f"confirm_delete_{pid}"
                    if st.checkbox("Confirmar borrado", key=confirm_key):
                        if st.button("Eliminar proyecto", key=f"del_btn_{pid}"):
                            ok = crm.global_crm.delete_project(user.get('username'), pid)
                            if ok:
                                st.success(f"Proyecto {pid} eliminado")
                                st.experimental_rerun()
                            else:
                                st.error("No se pudo eliminar el proyecto")