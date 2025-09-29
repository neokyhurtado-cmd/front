"""
Gestión de Usuarios y Roles - Panorama Ingeniería
Página para administradores para gestionar usuarios, roles y versiones de reportes.
"""

import streamlit as st
import pandas as pd
from datetime import datetime
import json

from modules.crm import CRM
from modules.auth import require_auth, require_role

# Initialize CRM
crm = CRM()

def main():
    """Main function for user and role management page."""
    st.title("👥 Gestión de Usuarios y Roles")

    # Require authentication and admin role
    if not require_auth():
        return

    if not crm.has_role(st.session_state.username, 'admin'):
        st.error("❌ Acceso denegado. Se requiere rol de administrador.")
        return

    # Create tabs for different management sections
    tab1, tab2, tab3, tab4 = st.tabs([
        "👤 Usuarios",
        "🔐 Roles",
        "📊 Versiones de Reportes",
        "📈 Dashboard"
    ])

    with tab1:
        manage_users_tab()

    with tab2:
        manage_roles_tab()

    with tab3:
        manage_report_versions_tab()

    with tab4:
        dashboard_tab()

def manage_users_tab():
    """Manage users tab."""
    st.header("Gestión de Usuarios")

    # List all users
    users = get_all_users()
    if users:
        st.subheader("Lista de Usuarios")
        df = pd.DataFrame(users)
        st.dataframe(df, use_container_width=True)

        # User management actions
        st.subheader("Acciones de Usuario")
        col1, col2, col3 = st.columns(3)

        with col1:
            with st.expander("➕ Crear Usuario"):
                create_user_form()

        with col2:
            with st.expander("🔄 Cambiar Plan"):
                change_plan_form(users)

        with col3:
            with st.expander("🗑️ Eliminar Usuario"):
                delete_user_form(users)
    else:
        st.info("No hay usuarios registrados.")

def manage_roles_tab():
    """Manage roles tab."""
    st.header("Gestión de Roles")

    # List all users with their roles
    users_roles = get_users_with_roles()
    if users_roles:
        st.subheader("Usuarios y sus Roles")
        df = pd.DataFrame(users_roles)
        st.dataframe(df, use_container_width=True)

        # Role assignment
        st.subheader("Asignar Roles")
        col1, col2 = st.columns(2)

        with col1:
            with st.expander("🔗 Asignar Rol"):
                assign_role_form()

        with col2:
            with st.expander("🔄 Remover Rol"):
                remove_role_form()
    else:
        st.info("No hay usuarios con roles asignados.")

def manage_report_versions_tab():
    """Manage report versions tab."""
    st.header("Versiones de Reportes")

    # Get all projects
    projects = crm.list_projects(st.session_state.username)
    if projects:
        # Project selector
        project_names = [f"{p['id']}: {p.get('name', 'Sin nombre')}" for p in projects]
        selected_project = st.selectbox("Seleccionar Proyecto", project_names)

        if selected_project:
            project_id = int(selected_project.split(':')[0])

            # List report versions for selected project
            versions = crm.get_report_versions(project_id)
            if versions:
                st.subheader(f"Versiones del Proyecto {project_id}")
                df = pd.DataFrame(versions, columns=[
                    'id', 'project_id', 'version', 'kind', 'status', 'created_at', 'meta_json'
                ])
                st.dataframe(df, use_container_width=True)

                # Version management
                col1, col2 = st.columns(2)

                with col1:
                    with st.expander("📝 Crear Nueva Versión"):
                        create_version_form(project_id)

                with col2:
                    with st.expander("🔄 Actualizar Estado"):
                        update_version_status_form(versions)
            else:
                st.info("No hay versiones de reportes para este proyecto.")

                # Create first version
                with st.expander("📝 Crear Primera Versión"):
                    create_version_form(project_id)
    else:
        st.info("No hay proyectos disponibles.")

def dashboard_tab():
    """Dashboard with statistics."""
    st.header("Dashboard de Gestión")

    col1, col2, col3, col4 = st.columns(4)

    with col1:
        total_users = len(get_all_users())
        st.metric("Total Usuarios", total_users)

    with col2:
        total_projects = len(crm.list_projects(st.session_state.username))
        st.metric("Total Proyectos", total_projects)

    with col3:
        # Count total report versions
        projects = crm.list_projects(st.session_state.username)
        total_versions = sum(len(crm.get_report_versions(p['id'])) for p in projects)
        st.metric("Versiones de Reportes", total_versions)

    with col4:
        # Count users with admin role
        admin_users = sum(1 for user in get_all_users() if crm.has_role(user['username'], 'admin'))
        st.metric("Administradores", admin_users)

    # Recent activity
    st.subheader("Actividad Reciente")
    # This would require additional logging, for now show recent projects
    projects = crm.list_projects(st.session_state.username)
    if projects:
        recent_projects = sorted(projects, key=lambda x: x.get('created_at', ''), reverse=True)[:5]
        for project in recent_projects:
            st.write(f"📁 Proyecto {project['id']}: {project.get('name', 'Sin nombre')} - {project.get('created_at', 'Fecha desconocida')}")

def create_user_form():
    """Form to create a new user."""
    with st.form("create_user_form"):
        username = st.text_input("Nombre de usuario")
        password = st.text_input("Contraseña", type="password")
        plan = st.selectbox("Plan", ["free", "premium", "enterprise"])
        role = st.selectbox("Rol inicial", ["analista", "verificador", "admin"])

        submitted = st.form_submit_button("Crear Usuario")
        if submitted:
            if username and password:
                if crm.register(username, password, plan):
                    crm.assign_role_to_user(username, role)
                    st.success(f"✅ Usuario {username} creado exitosamente con rol {role}")
                    st.rerun()
                else:
                    st.error("❌ Error al crear usuario. El nombre de usuario ya existe.")
            else:
                st.error("❌ Por favor complete todos los campos.")

def change_plan_form(users):
    """Form to change user plan."""
    usernames = [u['username'] for u in users]
    with st.form("change_plan_form"):
        username = st.selectbox("Seleccionar Usuario", usernames)
        new_plan = st.selectbox("Nuevo Plan", ["free", "premium", "enterprise"])

        submitted = st.form_submit_button("Cambiar Plan")
        if submitted:
            if crm.set_plan(username, new_plan):
                st.success(f"✅ Plan de {username} cambiado a {new_plan}")
                st.rerun()
            else:
                st.error("❌ Error al cambiar el plan.")

def delete_user_form(users):
    """Form to delete a user."""
    usernames = [u['username'] for u in users if u['username'] != st.session_state.username]
    with st.form("delete_user_form"):
        username = st.selectbox("Seleccionar Usuario", usernames if usernames else ["No hay usuarios para eliminar"])

        submitted = st.form_submit_button("Eliminar Usuario", type="primary")
        if submitted and username != "No hay usuarios para eliminar":
            # Note: In a real implementation, you'd want a confirmation dialog
            # For now, we'll implement a soft delete or require additional confirmation
            st.warning(f"⚠️ Función de eliminación no implementada completamente. Usuario: {username}")

def assign_role_form():
    """Form to assign role to user."""
    users = get_all_users()
    usernames = [u['username'] for u in users]

    with st.form("assign_role_form"):
        username = st.selectbox("Seleccionar Usuario", usernames)
        role = st.selectbox("Rol a asignar", ["analista", "verificador", "admin"])

        submitted = st.form_submit_button("Asignar Rol")
        if submitted:
            try:
                crm.assign_role_to_user(username, role)
                st.success(f"✅ Rol {role} asignado a {username}")
                st.rerun()
            except ValueError as e:
                st.error(f"❌ Error: {str(e)}")

def remove_role_form():
    """Form to remove role from user."""
    users_roles = get_users_with_roles()
    if users_roles:
        # Create options for users with roles
        options = []
        for ur in users_roles:
            if ur['roles']:
                for role in ur['roles'].split(', '):
                    options.append(f"{ur['username']} - {role}")

        with st.form("remove_role_form"):
            selection = st.selectbox("Seleccionar Usuario y Rol", options)

            submitted = st.form_submit_button("Remover Rol")
            if submitted and selection:
                username, role = selection.split(' - ')
                # Note: This would require a remove_role_from_user method
                st.warning(f"⚠️ Función de remover rol no implementada. Usuario: {username}, Rol: {role}")

def create_version_form(project_id):
    """Form to create a new report version."""
    with st.form(f"create_version_form_{project_id}"):
        version = st.text_input("Número de Versión", value="1.0")
        kind = st.selectbox("Tipo", ["PMT", "Estudio"])
        status = st.selectbox("Estado", ["en_revision", "aprobado", "observado", "renovado"])
        meta_info = st.text_area("Información adicional (JSON)", placeholder='{"comentarios": "Versión inicial"}')

        submitted = st.form_submit_button("Crear Versión")
        if submitted:
            try:
                meta_json = json.loads(meta_info) if meta_info.strip() else None
                version_id = crm.create_report_version(project_id, version, kind, status, json.dumps(meta_json) if meta_json else None)
                st.success(f"✅ Versión {version} creada exitosamente (ID: {version_id})")
                st.rerun()
            except json.JSONDecodeError:
                st.error("❌ Error en el formato JSON de la información adicional.")
            except Exception as e:
                st.error(f"❌ Error al crear versión: {str(e)}")

def update_version_status_form(versions):
    """Form to update report version status."""
    version_options = [f"{v[0]}: {v[2]} ({v[3]}) - {v[4]}" for v in versions]

    with st.form("update_version_status_form"):
        selected_version = st.selectbox("Seleccionar Versión", version_options)
        new_status = st.selectbox("Nuevo Estado", ["en_revision", "aprobado", "observado", "renovado"])

        submitted = st.form_submit_button("Actualizar Estado")
        if submitted and selected_version:
            version_id = int(selected_version.split(':')[0])
            crm.update_report_version_status(version_id, new_status)
            st.success(f"✅ Estado de versión {version_id} actualizado a {new_status}")
            st.rerun()

def get_all_users():
    """Get all users from database."""
    # This is a simplified approach - in production you'd have a dedicated method
    conn = crm._connect()
    c = conn.cursor()
    c.execute("SELECT id, username, plan, created_at FROM users ORDER BY created_at DESC")
    rows = c.fetchall()
    conn.close()

    return [
        {
            "id": row[0],
            "username": row[1],
            "plan": row[2],
            "created_at": row[3]
        }
        for row in rows
    ]

def get_users_with_roles():
    """Get users with their assigned roles."""
    users = get_all_users()
    users_with_roles = []

    for user in users:
        roles = crm.get_user_roles(user['username'])
        users_with_roles.append({
            "username": user['username'],
            "plan": user['plan'],
            "roles": ", ".join(roles) if roles else "Sin roles",
            "created_at": user['created_at']
        })

    return users_with_roles

if __name__ == "__main__":
    main()