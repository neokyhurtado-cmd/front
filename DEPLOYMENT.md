# Despliegue a Streamlit Cloud

## Pasos para desplegar

1. **Crear cuenta en Streamlit Cloud**: https://share.streamlit.io/

2. **Conectar repositorio**: Conectar tu cuenta de GitHub

3. **Seleccionar repositorio**: Elegir `front` del usuario `neokyhurtado-cmd`

4. **Configurar despliegue**:
   - **Main file path**: `panorama_local/app.py`
   - **Python version**: 3.9 (o superior)

5. **Configurar Secrets** (Settings > Secrets):
   ```
   DATABASE_URL = sqlite:///./data/app.db
   SECRET_KEY = tu_clave_secreta_aqui
   ENV = production
   ```

6. **Deploy**: Hacer clic en "Deploy"

## Requisitos

- `requirements.txt` actualizado con todas las dependencias
- Archivos principales en `panorama_local/`
- Base de datos SQLite se crea automáticamente

## URL de la app

Después del despliegue, obtendrás una URL como:
`https://neokyhurtado-cmd-front.streamlit.app/`

## Troubleshooting

- Si hay errores de importación, verificar que todos los módulos estén en `panorama_local/`
- Para la base de datos, verificar permisos de escritura
- Los logs se pueden ver en la pestaña "Logs" de Streamlit Cloud