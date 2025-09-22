# Configuración CRM + n8n SIN DOCKER

## 📋 Resumen

Este documento contiene las plantillas y configuraciones necesarias para ejecutar el proyecto CRM con React + Vite + Laravel + n8n **sin usar Docker** en un entorno Laragon.

## 🛠️ Archivos Creados

### 1. **Configuración Principal**
- `project-config.json` - Configuración central del proyecto
- `.env.template` - Plantilla de variables de entorno mejorada
- `n8n-config.env` - Configuración específica para n8n

### 2. **Scripts Mejorados**
- `scripts/start-n8n-no-docker.ps1` - Script avanzado para n8n sin Docker
- `scripts/start-dev-windows.ps1` - Script de desarrollo actualizado

### 3. **Workflows n8n**
- `laravel-backend/n8n-workflows/rss-upsert-ready-no-docker.json` - Workflow optimizado sin Docker

## 🚀 Configuración Paso a Paso

### 1. Variables de Entorno

Copia `.env.template` a `.env` en la carpeta `laravel-backend`:

```bash
cp .env.template laravel-backend/.env
```

**Edita las siguientes variables importantes:**

```env
# URLs sin Docker
APP_URL=http://127.0.0.1:8095
FRONTEND_URL=http://127.0.0.1:5174
N8N_URL=http://127.0.0.1:5678

# Tokens de seguridad (CAMBIAR)
N8N_TO_LARAVEL_TOKEN=tu_token_secreto_aqui
N8N_WEBHOOK_TOKEN=webhook_token_secreto_aqui
APP_KEY=base64:GENERA_CON_php_artisan_key_generate
```

### 2. Instalar n8n Globalmente

```bash
npm install -g n8n
```

### 3. Configurar Laravel

```bash
cd laravel-backend
composer install
php artisan key:generate
php artisan migrate
```

### 4. Configurar Frontend

```bash
cd frontend
npm install
```

## 🏃‍♂️ Iniciar el Entorno

### Opción A: Script Automático (Recomendado)

```powershell
.\scripts\start-dev-windows.ps1
```

Este script abre 3 ventanas:
- **Laravel Backend** (Puerto 8095)
- **Frontend Vite** (Puerto 5174)  
- **n8n** (Puerto 5678)

### Opción B: Manual

**Terminal 1 - Laravel:**
```bash
cd laravel-backend
php artisan serve --host 127.0.0.1 --port 8095
```

**Terminal 2 - Frontend:**
```bash
cd frontend
npm run dev -- --host 127.0.0.1 --port 5174
```

**Terminal 3 - n8n:**
```bash
cd scripts
.\start-n8n-no-docker.ps1 -NoAuth
```

## 📊 Configuración de n8n

### 1. Acceder a n8n
- URL: http://127.0.0.1:5678
- Usuario: `admin` 
- Contraseña: `admin123` (si la auth está habilitada)

### 2. Importar Workflows

1. Ve a n8n → **Import**
2. Selecciona el archivo: `laravel-backend/n8n-workflows/rss-upsert-ready-no-docker.json`
3. Importa el workflow

### 3. Configurar Variables de Entorno en n8n

En n8n, ve a **Settings > Environment Variables** y configura:

```
APP_URL=http://127.0.0.1:8095
N8N_TO_LARAVEL_TOKEN=tu_token_secreto_aqui
RSS_FEED_URL=https://www.eltiempo.com/rss/bogota.xml
```

### 4. Activar el Workflow

1. Abre el workflow importado
2. Haz clic en el toggle **Active** en la esquina superior derecha
3. El workflow correrá cada 3 horas automáticamente

## 🔧 Personalización

### Cambiar URLs de RSS

Edita en tu `.env`:

```env
RSS_FEED_URL=https://tu-feed-personalizado.xml
RSS_FEED_URL_MOVILIDAD=https://otro-feed.xml
```

### Cambiar Filtros del Workflow

En el workflow de n8n, nodo **"If - Filter Movilidad"**:

```javascript
// Cambiar "movilidad" por tu palabra clave
{{ ($json["title"] + ' ' + ($json["contentSnippet"] || '')).toLowerCase() }}
```

### Agregar APIs Externas

En tu `.env`:

```env
OPENAI_API_KEY=tu_openai_key
GEMINI_API_KEY=tu_gemini_key  
PEXELS_API_KEY=tu_pexels_key
```

## 🐛 Solución de Problemas

### n8n No Inicia

1. Verifica que n8n esté instalado: `n8n --version`
2. Verifica puertos disponibles: `netstat -an | findstr 5678`
3. Revisa logs en la ventana de PowerShell

### Laravel No Conecta con n8n

1. Verifica que `APP_URL` en `.env` apunte a `http://127.0.0.1:8095`
2. Verifica que los tokens coincidan en ambos lados
3. Prueba el endpoint: `http://127.0.0.1:8095/api/n8n/upsert-news`

### Frontend No Carga la API

1. Verifica proxy en `frontend/vite.config.ts`:
   ```typescript
   server: {
     proxy: {
       '/api': {
         target: 'http://127.0.0.1:8095',
         changeOrigin: true,
       },
     },
   }
   ```

### Workflows No Se Ejecutan

1. Verifica que el workflow esté **Activo**
2. Revisa las **Executions** en n8n para ver errores
3. Verifica las variables de entorno en n8n

## 📚 Recursos Útiles

- [Documentación n8n](https://docs.n8n.io/)
- [Laravel API Resources](https://laravel.com/docs/eloquent-resources)  
- [Vite Configuration](https://vitejs.dev/config/)
- [PowerShell Scripts](https://docs.microsoft.com/powershell/)

## 🆘 Soporte

Si encuentras problemas:

1. Revisa los logs en cada ventana de PowerShell
2. Verifica que todos los puertos estén disponibles
3. Asegúrate de que Laragon esté ejecutándose
4. Verifica las variables de entorno en cada servicio

---

**¡Tu entorno CRM + n8n sin Docker está listo! 🎉**