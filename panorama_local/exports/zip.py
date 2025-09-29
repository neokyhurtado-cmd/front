"""
ZIP export functionality for multiple projects.
"""

from io import BytesIO
from zipfile import ZipFile, ZIP_DEFLATED
from typing import List, Dict, Any, Callable, Optional
import json


def export_projects_zip(project_ids: List[str], fetch_fn: Optional[Callable[[str], Dict[str, bytes]]] = None) -> bytes:
    """
    Export multiple projects as a ZIP file.

    Args:
        project_ids: List of project IDs to export
        fetch_fn: Function that takes project_id and returns dict of filename -> content bytes
                 If None, creates dummy content

    Returns:
        ZIP file as bytes
    """
    buf = BytesIO()

    with ZipFile(buf, "w", ZIP_DEFLATED) as zf:
        for project_id in project_ids:
            # Create project folder
            folder_name = f"{project_id}"

            if fetch_fn:
                files = fetch_fn(project_id)
            else:
                # Dummy content for testing
                files = {
                    "README.txt": f"Proyecto {project_id}\n\nArchivos del proyecto PMT.".encode('utf-8'),
                    "proyecto.json": json.dumps({
                        "id": project_id,
                        "tipo": "PMT",
                        "fecha_export": "2025-01-01",
                        "capacidad": {"v": 1200, "c": 1800, "x": 0.67}
                    }, indent=2, ensure_ascii=False).encode('utf-8')
                }

            for filename, content in files.items():
                zf.writestr(f"{folder_name}/{filename}", content)

    buf.seek(0)
    return buf.read()


def fetch_project_files(project_id: str) -> Dict[str, bytes]:
    """
    Fetch all files for a project from the database/storage.
    This is a placeholder - integrate with actual CRM/project storage.
    """
    # TODO: Integrate with CRM to fetch actual project data
    # For now, return dummy data
    return {
        "proyecto.json": json.dumps({
            "id": project_id,
            "metadata": {"name": f"Proyecto {project_id}", "notes": "Exportado automáticamente"}
        }, indent=2, ensure_ascii=False).encode('utf-8'),
        "capacidad.csv": "parametro,valor\nv,1200\nc,1800\nx,0.67\n".encode('utf-8')
    }