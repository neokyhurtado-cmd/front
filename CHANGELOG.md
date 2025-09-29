# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v0.3.1] - 2025-10-10

### Added
- **CRM completo** con persistencia SQLite y autenticación bcrypt segura
- **Gestión de proyectos** con metadatos completos (name, notes, created_at)
- **Exportación multi-proyecto** a ZIP (hasta 10 proyectos) y PDF con reportlab
- **Módulo de capacidad HCM** completo: saturación, demora y nivel de servicio
- **Validación robusta de archivos PMT** con mensajes de error específicos
- **Sistema de logging estructurado** (archivo + consola) con timestamps
- **Launcher principal** (`app.py`) para deployment en Streamlit Cloud
- **Archivos de test** completos: PMT válidos/inválidos, ZIP de ejemplo
- **Optimizaciones SQLite**: WAL mode, cache aumentado, índices estratégicos
- **CI básico** con pytest, cobertura y tests de integración
- **Documentación completa**: README, instalación, deployment, secrets

### Changed
- **Imports absolutos** para compatibilidad con Streamlit Cloud
- **Estructura de directorios** auto-creada (data/, logs/)
- **Requirements.txt** separado para root vs panorama_local

### Fixed
- **Estabilidad de simulación** para PMT con múltiples señales
- **Compatibilidad de imports** entre desarrollo local y Cloud
- **Gestión de errores** en validación y exportación

### Security
- **bcrypt rounds ≥12** para hashing de contraseñas
- **Rate limiting básico** en autenticación (5 intentos/15 min)
- **Logs de seguridad** para intentos de login fallidos

### CI/CD
- **Workflow de tests** en PR y main (pytest + cobertura)
- **Validación de requirements** y dependencias
- **Tests de integración** para export ZIP/PDF

### Documentation
- **Guías de instalación** completas
- **Deployment en Streamlit Cloud** paso a paso
- **Variables de entorno** y secrets documentados
- **API de módulos** documentada

### Performance
- **SQLite optimizado** con WAL y cache aumentado
- **Índices estratégicos** en consultas frecuentes
- **Lazy loading** en operaciones de exportación

---

## [v0.2.0] - 2025-09-15

### Added
- Estructura básica del proyecto Panorama PMT
- Módulos iniciales: simulation, capacity, exports
- Configuración básica de Streamlit
- Tests iniciales con pytest

### Changed
- Reorganización de código en paquetes modulares

---

## [v0.1.0] - 2025-09-01

### Added
- Proyecto inicial Panorama Ingeniería PMT
- Configuración básica de desarrollo
- Estructura de directorios inicial