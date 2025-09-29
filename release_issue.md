# Release v0.3.1 (GA)

**Objetivo:** Publicar v0.3.1 (GA) con CRM SQLite/bcrypt, export ZIP/PDF, módulo HCM, CI básico y documentación completa.

**Fecha objetivo:** 2025-10-10
**Milestone:** [v0.3.1 (GA)](https://github.com/neokyhurtado-cmd/front/milestone/1)

## Checklist Técnico

### Core Features
- [x] CRM completo con persistencia SQLite y autenticación bcrypt
- [x] Gestión de proyectos con metadatos (name, notes, created_at)
- [x] Exportación multi-proyecto a ZIP (hasta 10 proyectos)
- [x] Exportación a PDF con reportlab
- [x] Módulo de capacidad HCM: saturación, demora y nivel de servicio
- [x] Validación robusta de archivos PMT con mensajes específicos
- [x] Sistema de logging estructurado (archivo + consola)

### Quality Assurance
- [ ] `requirements.txt` actualizado (incluye `reportlab`, versiones fijadas)
- [ ] `pytest -q` verde en CI; cobertura reportada (`make coverage`)
- [ ] Export **ZIP** de muestra generado correctamente
- [ ] Export **PDF** de muestra generado correctamente
- [ ] `PRAGMA foreign_keys=ON` + índices `projects(created_at, name)`
- [ ] Rate-limiting + lockout de login implementados (5 intentos/15 min)
- [ ] Logs estructurados para auth/simulación/export
- [ ] README actualizado (instalación, despliegue, `st.secrets`)

### CI/CD
- [ ] Workflow de GitHub Actions funcionando en PR y main
- [ ] Tests automatizados ejecutándose correctamente
- [ ] Cobertura de código ≥80% en módulos nuevos

### Documentation
- [ ] README completo con instalación y uso
- [ ] Guía de deployment en Streamlit Cloud
- [ ] Documentación de variables de entorno y secrets
- [ ] CHANGELOG actualizado

## Release & Deploy

### Pre-Release
- [ ] Milestone `v0.3.1 (GA)` cerrado
- [ ] Todas las issues de P0 completadas
- [ ] Branch `main` actualizada y estable
- [ ] CI verde en último commit

### Release Creation
- [ ] Tag `v0.3.1` creado: `git tag v0.3.1 && git push origin v0.3.1`
- [ ] Release en GitHub creado con changelog
- [ ] Assets adjuntos (ZIP/PDF de ejemplo si aplica)

### Deployment
- [ ] Despliegue en Streamlit Cloud validado
- [ ] Smoke test end-to-end completado (8 pasos)
- [ ] URL de producción funcionando: `https://pmt-panorama.streamlit.app/`

### Post-Release
- [ ] Demo interna grabada (5-10 min) o GIF
- [ ] Comunicación a stakeholders si aplica
- [ ] Métricas de uso iniciales monitoreadas

## Owners

- **CI/Tests:** @assignee
- **Export ZIP/PDF:** @assignee
- **HCM/Capacidad:** @assignee
- **CRM/Auth:** @assignee
- **Docs/README:** @assignee
- **Deploy Cloud:** @assignee

## Criterios de Aceptación

### Funcionales
- [ ] Registro/login con bcrypt funciona
- [ ] Import PMT válido → simulación → guardar con metadatos
- [ ] Lista de proyectos muestra name/notes/created_at
- [ ] Export JSON descarga con nombre correcto
- [ ] Export ZIP genera archivo con múltiples proyectos
- [ ] Export PDF genera reporte con datos del proyecto
- [ ] Simular desde proyecto guardado funciona
- [ ] Import PMT inválido muestra errores claros

### No Funcionales
- [ ] Performance: simulación < 5s, export < 10s
- [ ] Seguridad: bcrypt rounds ≥12, rate limiting activo
- [ ] Observabilidad: logs estructurados en producción
- [ ] Compatibilidad: funciona en Streamlit Cloud sin modificaciones

## Riesgos y Mitigaciones

- **Riesgo:** SQLite no persiste en Streamlit Cloud
  - **Mitigación:** Documentar limitación, planear DB externa en v0.4.0

- **Riesgo:** reportlab falla en Cloud
  - **Mitigación:** Test exhaustivo local + CI con reportlab

- **Riesgo:** Imports fallan en Cloud
  - **Mitigación:** Launcher con ROOT en sys.path validado

## Siguientes Pasos (v0.4.0)

- Capas oficiales: Lote → Andén → Calzada
- DB externa: Supabase/Postgres
- IDECA/SDM: integración de datos oficiales
- Simulación real: cityflow/uxsim completo