Panorama Local
===============

Mini backend and tools for local development and testing.

Features
- SQLite-backed CRM with bcrypt password hashing
- Streamlit UI to run simulations and save projects
- Admin CLI to list users and projects and to clear DB
- Unit tests for CRM

Run the Streamlit app
---------------------

1. Create a virtualenv and install requirements:

```powershell
python -m venv .venv
.\.venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

2. Run Streamlit:

```powershell
streamlit run app.py
```

The Streamlit UI (Simulación y CRM) will show the DB path and lets you upload a PMT JSON, run a stub simulation, save projects and export them as JSON.

Admin CLI
---------

```powershell
python admin_cli.py list-users
python admin_cli.py list-projects --user tester
python admin_cli.py clear-db
```

Tests
-----

Run pytest from the repository root. If your PYTHONPATH doesn't include the repo root, run:

```powershell
# from repo root
python -c "import sys,pytest; sys.path.insert(0, r'C:\path\to\repo'); sys.exit(pytest.main(['-q']))"
```

Notes
- The DB file is `data/panorama_crm.db` under this folder.
- Use the `Clear DB` button in the Streamlit UI for quick reset while developing.Panorama Local
===============

Aplicación Streamlit de ejemplo para construir PMT y ejecutar simulaciones sintéticas.

Instrucciones rápidas:

1. Instala dependencias: pip install -r requirements.txt
2. Ejecuta: streamlit run app.py

El módulo `modules/simulation.py` implementa stubs; `modules/crm.py` es un CRM en memoria.
