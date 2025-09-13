# 🚀 Panorama Ingeniería IA - Sistema Automatizado de Blog

![Laravel](https://img.shields.io/badge/Laravel-11-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2.12-blue.svg)
![Filament](https://img.shields.io/badge/Filament-4.0.12-yellow.svg)
![OpenAI](https://img.shields.io/badge/OpenAI-GPT--3.5--turbo-green.svg)

Sistema completo de blog automatizado que ingiere noticias desde RSS, las reescribe con IA y las publica automáticamente con un panel de administración profesional.

## ✨ Características Principales

- 📡 **Ingesta automática de RSS** desde múltiples fuentes de noticias
- 🤖 **Reescritura inteligente** con OpenAI GPT-3.5-turbo
- 📋 **Panel de administración** completo con Filament
- 🎨 **Diseño profesional** con Tailwind CSS + fuente Montserrat
- 📊 **Dashboard con estadísticas** en tiempo real
- 🔄 **Programación automática** con Laravel Scheduler
- 🏷️ **Gestión de tags y categorías**
- 📱 **Diseño responsive** y optimizado
- 🔍 **SEO optimizado** con meta tags automáticos
- 📈 **Sistema de paginación** profesional

## 🛠️ Stack Tecnológico

- **Backend:** Laravel 11, PHP 8.2.12
- **Frontend:** Livewire 3.6.4, Tailwind CSS, Montserrat Font
- **Admin Panel:** Filament 4.0.12
- **Base de Datos:** SQLite (fácil de migrar a MySQL/PostgreSQL)
- **IA:** OpenAI GPT-3.5-turbo
- **CSS Framework:** Tailwind CSS con componentes personalizados
- **Procesamiento:** Laravel Queues + Scheduler

## 🚀 Instalación Rápida

\`\`\`bash
# Clonar el repositorio
git clone https://github.com/tu-usuario/panorama-blog.git
cd panorama-blog

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos
touch database/database.sqlite
php artisan migrate

# Crear usuario admin
php artisan make:filament-user

# Compilar assets
npm run build

# Iniciar servidor
php artisan serve
\`\`\`

## ⚙️ Configuración

### 1. Variables de Entorno (.env)

\`\`\`env
# OpenAI API Key (requerido para reescritura de IA)
OPENAI_API_KEY=tu_api_key_aqui

# Configuración de base de datos
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/completa/a/database/database.sqlite

# URLs y configuración
APP_URL=http://127.0.0.1:8000
\`\`\`

### 2. Configurar Scheduler (Opcional)

Para automatización completa, añade al crontab:

\`\`\`bash
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
\`\`\`

## 🎯 Comandos Disponibles

### Comandos del Sistema de Blog

\`\`\`bash
# Ingesta manual de RSS
php artisan blog:ingest-rss

# Reescribir posts con IA
php artisan blog:rewrite-posts

# Procesar cola de trabajos
php artisan blog:process-queue

# Ejecutar ciclo completo
php artisan blog:run-full-cycle
\`\`\`

### Scripts PowerShell

\`\`\`powershell
# Ejecutar sistema completo (Windows)
.\run-blog-system.ps1
\`\`\`

## 📊 Panel de Administración

Accede al panel de administración en `/admin`:

- **Gestión completa de posts** (crear, editar, eliminar)
- **Dashboard con estadísticas** en tiempo real
- **Filtros avanzados** por estado, tags, fuente
- **Editor rich text** para contenido
- **Gestión de imágenes** y metadatos SEO
- **Sistema de permisos** y autenticación

## 🎨 Diseño y UI

- **Diseño profesional** inspirado en Filament
- **Fuente Montserrat** para mejor legibilidad
- **Componentes reutilizables** con Tailwind CSS
- **Grid responsive** de 6 posts destacados
- **Cards de estadísticas** con iconos SVG
- **Paleta de colores** corporativa

## 🔧 Estructura del Proyecto

\`\`\`
panorama/
├── app/
│   ├── Console/Commands/          # Comandos Artisan personalizados
│   ├── Filament/Resources/        # Recursos del panel admin
│   ├── Jobs/                      # Jobs de procesamiento
│   ├── Models/                    # Modelos Eloquent
│   └── Services/                  # Servicios (RSS, IA)
├── resources/views/blog/          # Vistas del blog público
├── database/migrations/           # Migraciones de BD
└── routes/web.php                 # Rutas web
\`\`\`

## 📈 Fuentes RSS Configuradas

El sistema está preconfigurado para procesar noticias de:
- Movilidad urbana
- Señalización vial
- Transporte público
- Noticias de Bogotá y Colombia

## 🤖 Procesamiento con IA

Cada artículo ingresado pasa por:
1. **Extracción** de contenido desde RSS
2. **Reescritura inteligente** con GPT-3.5-turbo
3. **Optimización SEO** automática
4. **Generación de tags** contextual
5. **Programación de publicación**

## 🔒 Seguridad

- **Autenticación** con Laravel Breeze
- **Middleware** de protección en rutas admin
- **Sanitización** de contenido HTML
- **Rate limiting** en APIs
- **Validación** de formularios

## 📱 SEO y Performance

- **Meta tags** automáticos
- **Open Graph** y Twitter Card
- **Sitemap.xml** generado automáticamente
- **Lazy loading** de imágenes
- **Cache** optimizado
- **URLs amigables** con slugs

## 🚀 Deployment

### Producción con Apache/Nginx

\`\`\`bash
# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
\`\`\`

### Variables de Entorno de Producción

\`\`\`env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
\`\`\`

## 🤝 Contribuciones

1. Fork el proyecto
2. Crea una rama feature (\`git checkout -b feature/AmazingFeature\`)
3. Commit tus cambios (\`git commit -m 'Add some AmazingFeature'\`)
4. Push a la rama (\`git push origin feature/AmazingFeature\`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver \`LICENSE\` para más detalles.

## 📞 Soporte

- **Email:** admin@panoramaingenieria.com
- **Sitio Web:** [Panorama Ingeniería](https://www.panoramaingenieria.com)
- **Issues:** [GitHub Issues](https://github.com/tu-usuario/panorama-blog/issues)

---

**⭐ Si te gusta este proyecto, ¡dale una estrella en GitHub!**
