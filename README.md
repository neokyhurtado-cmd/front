# Panorama Ingeniería - PMT

Herramientas de análisis de capacidad de tráfico y simulación PMT con CRM integrado.

## Características

- **Análisis de Capacidad HCM**: Cálculos de ratio de saturación, demora y nivel de servicio
- **Simulación PMT**: Motor de simulación con CityFlow y UXSim
- **CRM SQLite**: Sistema de usuarios con hash bcrypt y gestión de proyectos
- **Exportación**: ZIP masivo y reportes PDF individuales
- **Logging Estructurado**: Logs JSON para auditoría y monitoreo
- **Seguridad**: Rate limiting, lockout de login, encriptación bcrypt ≥12 rounds

## Instalación

### Prerrequisitos

- Python 3.10+
- pip
- Virtualenv (recomendado)

### Instalación Local

1. **Clonar el repositorio**:
```bash
git clone <repository-url>
cd panorama-ingenieria
```

2. **Crear entorno virtual**:
```bash
python -m venv .venv
# Windows
.\.venv\Scripts\Activate.ps1
# Linux/Mac
source .venv/bin/activate
```

3. **Instalar dependencias**:
```bash
pip install -r requirements.txt
```

4. **Verificar instalación**:
```bash
python -c "import streamlit, bcrypt, reportlab; print('✓ Todas las dependencias instaladas')"
```

## Uso Local

### Ejecutar la aplicación Streamlit

```bash
# Desde el directorio raíz del repositorio
streamlit run app.py --server.headless true --server.port 8504
```

La aplicación estará disponible en `http://localhost:8504`

### Funcionalidades disponibles

1. **Análisis de Capacidad**: Calcula métricas HCM (ratio de saturación, demora, LOS)
2. **Simulación PMT**: Ejecuta simulaciones con archivos JSON de PMT Builder
3. **CRM**: Registro/login de usuarios, guardado de proyectos, exportación

### Comandos de desarrollo

```bash
# Ejecutar tests
make test
# o
pytest

# Ejecutar con coverage
make coverage
# o
pytest --cov=panorama_local --cov-report=html

# Ejecutar smoke tests
make smoke-test

# Limpiar cache y reinstalar
make install
```

## Despliegue en Streamlit Cloud

### Configuración de Secrets

Crea un archivo `secrets.toml` en Streamlit Cloud o configura las siguientes variables:

```toml
# secrets.toml
DATABASE_URL = "sqlite:///./data/app.db"

# Opcional: configuración adicional
LOG_LEVEL = "INFO"
MAX_UPLOAD_SIZE = 50  # MB
```

### Variables de Entorno

Para despliegue local o alternativo:

```bash
export DATABASE_URL="sqlite:///./data/app.db"
export STREAMLIT_SERVER_HEADLESS=true
export STREAMLIT_SERVER_PORT=8501
```

### Estructura del Proyecto

```
panorama_local/
├── app.py                 # Aplicación principal Streamlit
├── modules/
│   ├── crm.py            # Sistema CRM con SQLite
│   └── simulation.py     # Motor de simulación
├── capacity/
│   └── core.py           # Cálculos HCM
├── exports/
│   ├── zip.py            # Exportación ZIP
│   └── pdf.py            # Generación PDF
├── logs/                 # Logs estructurados
└── data/                 # Base de datos SQLite
```

## Seguridad

### Autenticación
- Hash bcrypt con 12 rounds mínimo
- Rate limiting: máximo 5 intentos fallidos en 15 minutos
- Lockout automático tras intentos fallidos

### Base de Datos
- SQLite con optimizaciones WAL
- Foreign keys habilitadas
- Índices en campos de búsqueda frecuente

### Logging
- Logs estructurados en formato JSON
- Eventos auditados: auth, simulation, exports
- Separación de logs de aplicación y errores

## Desarrollo

### Ejecutar Tests

```bash
# Tests completos
pytest -v

# Con coverage
pytest --cov=panorama_local --cov-report=term-missing

# Tests específicos
pytest tests/test_crm.py -v
```

### CLI de Administración

```bash
# Listar usuarios
python admin_cli.py list-users

# Listar proyectos de un usuario
python admin_cli.py list-projects --user tester

# Limpiar base de datos
python admin_cli.py clear-db
```

### Logs

Los logs se almacenan en `panorama_local/logs/panorama_app.log` en formato JSON estructurado:

```json
{
  "timestamp": "2025-09-29 10:30:45",
  "level": "INFO",
  "logger": "__main__",
  "message": "User login successful",
  "event": "auth_login",
  "username": "testuser",
  "success": true
}
```

## API y Extensiones

### Módulos Principales

- `crm.CRM`: Gestión de usuarios y proyectos
- `simulation.run_simulation()`: Ejecuta simulaciones PMT
- `capacity.core.compute_capacity()`: Cálculos HCM
- `exports.zip.export_projects_zip()`: Exportación masiva
- `exports.pdf.render_project_pdf()`: Reportes PDF

### Formato PMT

Los archivos PMT deben ser JSON con estructura:

```json
{
  "markers": [...],
  "plan": "Free|Pro",
  "center": {...},
  "rotulo": "..."
}
```

## Troubleshooting

### Problemas Comunes

1. **Import errors en Streamlit Cloud**:
   - Verificar que `app.py` esté en el directorio raíz
   - Asegurar imports robustos con try/except

2. **Database locked**:
   - SQLite WAL mode está habilitado
   - Verificar conexiones abiertas

3. **Memory issues**:
   - Ajustar `cache_size` en SQLite si es necesario
   - Monitorear uso de memoria en simulaciones grandes

### Logs de Debug

```bash
# Ver logs en tiempo real
tail -f panorama_local/logs/panorama_app.log

# Buscar errores específicos
grep "ERROR" panorama_local/logs/panorama_app.log | jq .message
```

## Contribución

1. Fork el repositorio
2. Crea una rama para tu feature
3. Añade tests para nueva funcionalidad
4. Ejecuta `make test` y `make coverage`
5. Envía un Pull Request

## Licencia

Este proyecto es parte de Panorama Ingeniería. Ver términos de uso internos.

---

**Versión**: 0.3.1 (GA)
**Fecha**: Septiembre 2025
**Estado**: Listo para producción
