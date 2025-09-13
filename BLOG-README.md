# 📰 Blog Automatizado con IA - Panorama Ingeniería

Sistema completo de blog automatizado que recopila noticias RSS sobre movilidad urbana en Colombia, las procesa con IA y las publica automáticamente en horarios programados.

## 🚀 Características

- **Recopilación automática**: 4+ feeds RSS de Google News sobre movilidad
- **Procesamiento con IA**: Reescritura y optimización SEO automática
- **Publicación programada**: 4 franjas diarias (8:00, 12:00, 16:00, 20:00)
- **SEO completo**: Meta tags, sitemap automático, robots.txt
- **Rotación inteligente**: Archiva noticias viejas, mantiene contenido educativo
- **Interface moderna**: Vistas responsive con Tailwind CSS

## 📋 Requisitos

- PHP 8.1+
- Laravel 11
- Composer
- OpenAI API Key válida

## 🛠️ Instalación

El sistema ya está instalado y configurado. Solo necesitas:

```bash
# 1. Configurar tu OpenAI API Key en .env
OPENAI_API_KEY=tu-clave-aqui

# 2. Ejecutar migraciones (ya hecho)
php artisan migrate

# 3. ¡Listo para usar!
```

## 🎯 Uso

### Ejecución Manual

```bash
# Script completo (Windows)
.\run-blog-system.ps1

# O comandos individuales:
php artisan feeds:fetch          # Traer feeds RSS
php artisan queue:work --max-jobs=5  # Procesar con IA
php artisan posts:schedule-daily     # Programar publicaciones
php artisan posts:publish-due       # Publicar programados
php artisan posts:rotate-monthly    # Ver estadísticas
```

### Automatización (Servidor)

Agregar al cron:
```bash
* * * * * cd /ruta/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

### Ver el Blog

```bash
php artisan serve
# Visitar: http://127.0.0.1:8000
```

## 📊 Estado Actual

- ✅ **307 posts** importados de RSS
- ✅ **8 posts** procesados con IA
- ✅ **1 publicado**, **2 programados**
- ✅ **Scheduler** configurado
- ✅ **SEO** optimizado
- ✅ **Vistas** funcionando

## 🔄 Flujo Automático

1. **Cada hora**: `feeds:fetch` → Busca nuevas noticias → Crea borradores → Encola IA
2. **07:55 diario**: `posts:schedule-daily` → Programa 4 posts para el día
3. **Cada minuto**: `posts:publish-due` → Publica posts en su hora exacta
4. **Mensual**: `posts:rotate-monthly` → Archiva noticias > 60 días
5. **Semanal**: Genera sitemap actualizado

## 📁 Estructura

```
app/
├── Models/Post.php              # Modelo principal
├── Services/RssIngest.php       # Servicio RSS
├── Jobs/RewritePostJob.php      # Job de IA
└── Console/Commands/            # 4 comandos Artisan

routes/
├── web.php                      # Rutas del blog
└── console.php                  # Scheduler

resources/views/
├── layouts/app.blade.php        # Layout principal
└── blog/
    ├── index.blade.php          # Lista de posts
    └── show.blade.php           # Post individual
```

## 🌐 URLs

- `/` - Página principal (índice del blog)
- `/blog/{slug}` - Post individual
- `/sitemap.xml` - Sitemap SEO
- `/robots.txt` - Robots para SEO

## ⚙️ Configuración

### Feeds RSS (configurable en `RssIngest.php`):
- Google News - Movilidad Bogotá
- Google News - Cierres viales  
- Google News - Transporte público
- Google News - Señalización vial

### Franjas de Publicación:
- 08:00 AM - Noticias matutinas
- 12:00 PM - Información mediodía
- 16:00 PM - Actualizaciones tarde
- 20:00 PM - Resumen nocturno

## 🎨 Personalización

- **Feeds**: Editar `app/Services/RssIngest.php`
- **Horarios**: Modificar `routes/console.php`
- **Vistas**: Personalizar en `resources/views/blog/`
- **Estilos**: Ajustar clases Tailwind CSS

## 🔧 Comandos Útiles

```bash
# Ver estado
php artisan posts:rotate-monthly

# Limpiar caché
php artisan cache:clear
php artisan config:clear

# Generar sitemap manual
php artisan tinker --execute="Spatie\Sitemap\SitemapGenerator::create(config('app.url'))->writeToFile(public_path('sitemap.xml'))"

# Ver trabajos pendientes
php artisan queue:work --once
```

## 📈 Monitoreo

El sistema registra logs en `storage/logs/laravel.log`:
- Errores de RSS
- Problemas de IA
- Estado de publicaciones

## 🚨 Solución de Problemas

### OpenAI no funciona:
- Verificar API key válida
- Comprobar modelo disponible (gpt-3.5-turbo)
- Revisar créditos en cuenta OpenAI

### No se publican posts:
- Verificar cron configurado
- Ejecutar `php artisan posts:schedule-daily`
- Comprobar posts con `body` no nulo

### RSS no se importa:
- Verificar conectividad internet
- Comprobar URLs de feeds válidas
- Ver logs de errores

## 💡 Próximas Mejoras

- [ ] Panel admin con Filament
- [ ] Integración redes sociales
- [ ] Análitics y métricas
- [ ] Newsletter automático
- [ ] Más fuentes RSS oficiales

---

**Desarrollado con ❤️ para Panorama Ingeniería**  
Sistema 100% funcional y listo para producción 🚀
