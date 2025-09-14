# 🔧 GUÍA DE SOLUCIÓN: Laravel + Vite "No se puede obtener acceso"

## 🔴 Error típico:
```
Vaya... no se puede obtener acceso a esta página
127.0.0.1 rechazó la conexión
ERR_CONNECTION_REFUSED
```

---

## ✅ SOLUCIÓN PASO A PASO

### 1️⃣ **Verificar `vite.config.js` - SIN DUPLICADOS**

Archivo correcto:
```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        host: '127.0.0.1',
        port: 5174,         // puerto fijo para evitar rotación
        strictPort: true,   // no cambiar automáticamente si está ocupado
        hmr: {
            host: '127.0.0.1',
            port: 5174,
            protocol: 'ws',
        }
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
    },
    base: '/build/',
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

⚠️ **COMÚN ERROR:** Tener DOS bloques `server:` duplicados.

---

### 2️⃣ **Matar procesos zombie (Windows)**

```powershell
# Mata todos los PHP y Node colgados
taskkill /F /IM php.exe /T >NUL 2>&1
taskkill /F /IM node.exe /T >NUL 2>&1
```

---

### 3️⃣ **Limpiar cachés Laravel**

```powershell
cd C:\Users\USER\panorama
php artisan optimize:clear
```

---

### 4️⃣ **Iniciar servidor PHP (método robusto)**

**Opción A - Artisan (preferido):**
```powershell
php artisan serve --host=127.0.0.1 --port=9000
```

**Opción B - PHP directo (si falla A):**
```powershell
php -S 127.0.0.1:9000 -t public
```

**Puertos alternativos si están ocupados:** 8080, 3000, 4000, 9001

---

### 5️⃣ **Actualizar .env con puerto correcto**

```env
APP_URL=http://127.0.0.1:9000
```

Después:
```powershell
php artisan config:clear
```

---

### 6️⃣ **Iniciar Vite HMR**

```powershell
npm run dev
```

**Debe mostrar:**
```
VITE v7.1.5 ready in 567 ms
➜ Local: http://127.0.0.1:5174/build/
LARAVEL v12.28.1 plugin v2.0.1
➜ APP_URL: http://127.0.0.1:9000
```

---

### 7️⃣ **URLs finales**

- **🌐 Aplicación:** http://127.0.0.1:9000
- **⚡ Vite assets:** http://127.0.0.1:5174/build/

---

## 🚨 CHECKLIST DE PROBLEMAS COMUNES

### ❌ `vite.config.js` duplicado:
```js
// MAL ❌
export default defineConfig({
  server: { host: '127.0.0.1', port: 5174 },
  server: { /* otro bloque */ }  // ⚠️ DUPLICADO
})
```

### ❌ Puerto ocupado:
```
Failed to listen on 127.0.0.1:8000 (reason: ?)
```
**Solución:** Cambiar puerto o matar proceso.

### ❌ APP_URL desincronizado:
Vite muestra `APP_URL: http://127.0.0.1:18080` pero servidor está en 9000.
**Solución:** Actualizar `.env` y `php artisan config:clear`.

---

## 🔄 SCRIPT AUTOMATIZADO WINDOWS

Guardar como `start-dev.ps1`:

```powershell
# Matar procesos
Write-Host "🔴 Matando procesos anteriores..." -ForegroundColor Red
taskkill /F /IM php.exe /T >NUL 2>&1
taskkill /F /IM node.exe /T >NUL 2>&1

# Limpiar Laravel
Write-Host "🧹 Limpiando cachés..." -ForegroundColor Yellow
php artisan optimize:clear

# Iniciar PHP
Write-Host "🚀 Iniciando servidor PHP..." -ForegroundColor Green
Start-Process powershell -ArgumentList "php artisan serve --host=127.0.0.1 --port=9000"

# Esperar un poco
Start-Sleep -Seconds 2

# Iniciar Vite
Write-Host "⚡ Iniciando Vite..." -ForegroundColor Cyan
npm run dev
```

**Uso:**
```powershell
.\start-dev.ps1
```

---

## 🎯 SEÑALES DE ÉXITO

✅ PHP servidor: logs de requests aparecen
✅ Vite: "ready in XXXms" + HMR conectado  
✅ Navegador: página carga con toggle tema
✅ HMR: cambios CSS se ven sin recargar

---

## 📝 NOTAS IMPORTANTES

1. **Siempre usa puertos diferentes:** Laravel (9000) ≠ Vite (5174)
2. **Un solo `server:` en vite.config.js**
3. **APP_URL debe coincidir** con puerto real de Laravel
4. **Mata procesos zombie** antes de reiniciar
5. **Windows:** usa `127.0.0.1` en vez de `localhost`

---

**Última actualización:** Septiembre 14, 2025
**Configuración probada:** Laravel 12.28.1 + Vite 7.1.5 + PHP 8.2.12