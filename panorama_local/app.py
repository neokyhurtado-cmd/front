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
            st.write(f"Usuario: **{user.username}**")
            st.write(f"Plan: **{user.plan}**")
            if user.plan == "Free":
                if st.button("Actualizar a Pro"):
                    crm.global_crm.set_plan(user.username, "Pro")
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
        uploaded = st.file_uploader("Carga un JSON exportado desde PMT Builder", type=["json"]) 
        if uploaded is not None:
            try:
                pmt_data = json.load(uploaded)
                st.json(pmt_data)
                engine = st.selectbox("Motor de simulación", ["cityflow", "uxsim"]) 
                if st.button("Ejecutar simulación"):
                    res = simulation.run_simulation(pmt_data, engine=engine)
                    st.success("Simulación completada")
                    st.json(res)
                    if crm.global_crm.is_authenticated():
                        if st.button("Guardar proyecto en mi cuenta"):
                            user = crm.global_crm.current_user()
                            crm.global_crm.add_project(user.username, pmt_data)
                            st.success("Proyecto guardado")
                    else:
                        st.info("Inicia sesión para poder guardar proyectos en tu cuenta.")
            except Exception as e:
                st.error(f"Error leyendo JSON: {e}")
        else:
            st.info("Sube aquí un JSON exportado desde PMT Builder para simularlo.")

        if crm.global_crm.is_authenticated():
            user = crm.global_crm.current_user()
            st.subheader("Proyectos guardados")
            projects = crm.global_crm.list_projects(user.username)
            if not projects:
                st.write("No hay proyectos guardados.")
            else:
                for i, pr in enumerate(projects):
                    st.write(f"Proyecto {i+1}")
                    st.json(pr)
                    try:
                        import json as _json
                        payload = _json.dumps(pr, ensure_ascii=False, indent=2)
                        filename = f"project_{i+1}.json"
                        st.download_button(label="Exportar proyecto (JSON)", data=payload, file_name=filename, mime="application/json")
                    except Exception as _e:
                        st.warning(f"No se pudo preparar export: {_e}")