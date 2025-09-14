# 🔧 Continue Workflows para PANORAMA IAS

## 🚀 Modelos disponibles en VS Code

### 1. 🔧 PANORAMA IAS (Modelo principal)
**Cuándo usar:** Desarrollo diario Laravel + Vite + Tailwind
- ✅ Errores 500/404 en rutas Laravel
- ✅ Problemas con @vite() en Blade
- ✅ Optimizar componentes `.blade.php`
- ✅ Configuración Tailwind CSS v4
- ✅ Scripts PowerShell QA

### 2. 🧠 GPT-5 Architect  
**Cuándo usar:** Decisiones de arquitectura y escalabilidad
- ✅ Reestructurar controladores
- ✅ Patrones de diseño (Repository, Service Layer)
- ✅ Estrategias de caché y performance
- ✅ Migración de tecnologías

### 3. 🐛 Debug Specialist
**Cuándo usar:** Errores complejos y debugging
- ✅ Stack traces de Laravel
- ✅ Problemas de memoria/performance
- ✅ Conflictos de dependencias
- ✅ Análisis de logs

### 4. ⚡ Quick Fix
**Cuándo usar:** Fixes rápidos sin explicación
- ✅ Typos en sintaxis PHP/Blade
- ✅ Imports missing
- ✅ Correcciones de CSS

---

## 📋 Comandos personalizados

### `Laravel Error Analysis`
**Uso:** Selecciona stack trace completo → clic derecho → Custom Command → Laravel Error Analysis

**Ejemplo:**
```
Illuminate\View\ViewException: Too few arguments to function Illuminate\Foundation\Vite::__invoke()
```
**Resultado:** Causa + solución directa (ej: agregar argumentos a @vite)

---

### `Blade Component Optimize`
**Uso:** Selecciona componente completo → Custom Command

**Antes:**
```blade
<div class="card">
  <img src="{{$post->image_url}}" alt="{{$post->title}}">
  <h3>{{$post->title}}</h3>
</div>
```

**Después (automático):**
```blade
<article class="card group focus-within:ring-2 ring-offset-2 ring-blue-500">
  <div class="card-image aspect-video overflow-hidden">
    <img 
      src="{{$post->image_url}}" 
      alt="{{$post->title}}"
      loading="lazy"
      class="w-full h-full object-cover transition-transform group-hover:scale-105"
      onerror="this.src='/images/placeholder.jpg'"
    >
  </div>
  <h3 class="font-semibold text-gray-900 dark:text-gray-100 line-clamp-2">
    {{$post->title}}
  </h3>
</article>
```

---

### `Vite + Tailwind Fix`
**Uso:** Cuando assets no cargan o hay errores de build

**Selecciona:** vite.config.js + tailwind.config.js
**Comando:** Vite + Tailwind Fix
**Resultado:** Configuración corregida + explicación de qué estaba mal

---

### `Security Review`
**Uso:** Antes de push a producción

**Selecciona:** Controlador o ruta completa
**Resultado:** Lista de vulnerabilidades + código corregido

---

### `PowerShell QA Script`
**Uso:** Crear/mejorar scripts de testing

**Ejemplo input:**
```powershell
# Necesito script que valide que Laravel esté funcionando
```

**Resultado:**
```powershell
# Validate Laravel application health
function Test-LaravelHealth {
    param([string]$BaseUrl = "http://127.0.0.1:7070")
    
    try {
        $health = Invoke-RestMethod "$BaseUrl/healthz" -TimeoutSec 5
        if ($health -eq "ok") {
            Write-Host "✅ Laravel health check: OK" -ForegroundColor Green
            return $true
        }
    } catch {
        Write-Host "❌ Laravel health check failed: $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}
```

---

## 🎯 Flujos de trabajo típicos

### 🔥 Error 500 en homepage
1. Abrir `routes/web.php` + `home.blade.php`
2. Seleccionar ruta problemática
3. **Ask Continue (PANORAMA IAS):** "¿Por qué me da error 500?"
4. Aplicar fix sugerido
5. Test: `curl http://127.0.0.1:7070/`

### 🎨 Mejorar componente existente
1. Seleccionar todo el `.blade.php`
2. **Custom Command:** "Blade Component Optimize"
3. Revisar mejoras (accesibilidad, responsive, themes)
4. Aceptar/modificar sugerencias

### ⚙️ Debug Vite assets
1. Seleccionar `vite.config.js`
2. **Ask Continue (Debug Specialist):** "Assets no cargan después de npm run build"
3. Seguir checklist de debugging
4. Aplicar correcciones

### 🛡️ Security check antes de deploy
1. Seleccionar controlador completo
2. **Custom Command:** "Security Review"
3. Aplicar fixes de seguridad
4. Re-run security review hasta estar limpio

---

## ⌨️ Atajos de teclado recomendados

- `Ctrl+L` → Abrir Continue sidebar
- `Ctrl+Shift+L` → Ask Continue sobre selección actual
- `Ctrl+I` → Inline edit (cambios directos en código)
- `Tab` → Autocomplete con modelo rápido

---

## 🏃‍♂️ Quick Start

1. **Instala Continue extension** en VS Code
2. **Copia el config.json** a `.continue/config.json`
3. **Agrega tu OpenAI API Key** en los 4 modelos
4. **Reinicia VS Code**
5. **Test:** Selecciona cualquier función PHP → `Ctrl+Shift+L` → "Explica qué hace este código"

---

## 🔧 QA Integration con Continue

**Nuevo:** Script `tools/continue-qa.ps1` con reporting estructurado

### Comandos QA:
```powershell
.\tools\continue-qa.ps1 -Mode quick    # Blade + Vite
.\tools\continue-qa.ps1 -Mode full     # + Laravel health
.\tools\continue-qa.ps1 -Mode deploy   # + security 
.\tools\continue-qa.ps1 -JsonOutput    # Para Continue
```

### Workflow integrado:
1. Ejecuta QA script en PowerShell terminal
2. Si hay errores, usa comando **"Continue QA Integration"**
3. Pega output del script en Continue chat
4. Recibe plan priorizado con fixes específicos (archivo:línea)

¡Listo! Ya tienes tu asistente personal integrado en VS Code 🚀