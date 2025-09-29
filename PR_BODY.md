 soporte para importar y exportar proyectos PMT en la sección “Simulación y CRM".
- Se permite seleccionar proyectos guardados para ejecutar simulaciones directamente.
- Se mantiene la opción de cargar un JSON desde archivo y, tras la simulación, guardarlo en la cuenta si el usuario está autenticado.
- Al guardar un proyecto, se solicitan metadatos: nombre descriptivo, notas y timestamp; estos datos se muestran en la lista y se incluyen en el JSON exportado.
- Los proyectos se exportan con nombres basados en el metadato "name" (saneado) y el id.

Cambios importantes

- `panorama_local/modules/crm.py`: añade columnas `name`, `notes`, `created_at`; soporte para insertar proyectos con metadatos (`add_project_with_meta`); `list_projects` devuelve {id, project, name, notes, created_at}; ligero migrador que añade columnas si faltan.
- `panorama_local/app.py`: UI para importar/exportar proyectos, selección de proyecto guardado para simular, prompts para nombre y notas al guardar, uso del name en el filename de export.
- `tests/test_crm.py`: tests actualizados para metadata (usa `add_project_with_meta` y verifica id/name/notes).
- `.gitignore`: añade `panorama_local/data/` (la base de datos local no se versiona).
- Se eliminó `panorama_local/data/panorama_crm.db` del índice de la rama limpia.

Pruebas realizadas

- py_compile sobre los módulos modificados: OK.
- pytest: 1 passed (tests/test_crm.py).
- Flujos manuales verificados: registro/login, importación, simulación, guardado con metadatos, export, borrado con confirmación.

Pendientes / recomendaciones

- Convertir timestamps a timezone-aware (ej. datetime.now(timezone.utc).isoformat()) — actualmente se usa utcnow() y pytest emite un warning.
- Export ZIP para múltiples proyectos seleccionados, validaciones más robustas (JSON inválido), logging y pruebas adicionales.
- Considerar añadir `panorama_crm_example.db` o `example_projects.json` con datos dummy para onboarding.
- Para producción, migraciones más formales (alembic / flyway) en lugar del ALTER TABLE ligero.
