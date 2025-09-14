# 🛡️ Pre-Commit Hook Documentation

## 🔧 **Configuración Automática**
El pre-commit hook ya está configurado en `.git/hooks/pre-commit` y se ejecuta automáticamente antes de cada commit.

## 📋 **¿Qué valida el hook?**
- ✅ **Blade Template Integrity**: Directivas @vite correctas, sin colisiones
- ✅ **Vite Configuration**: Assets configurados correctamente  
- ✅ **Accessibility**: Alt attributes en imágenes
- ✅ **Security**: Escapado correcto, no debug en producción

## 🚀 **Flujo Normal de Commit**
```powershell
# 1. Hacer cambios en archivos
git add .

# 2. Intentar commit (hook se ejecuta automáticamente)
git commit -m "feat: nueva funcionalidad"

# 3a. Si pasa QA: ✅ Commit exitoso
# 3b. Si hay errores: ❌ Commit bloqueado + reporte detallado
```

## ❌ **Si el hook bloquea tu commit**

### **Salida de ejemplo:**
```
❌ COMMIT BLOCKED: 2 critical error(s) found

🚫 [CRITICAL] ViteDirective
   File: resources/views/layouts/app.blade.php:15
   Issue: Empty @vite directive  
   Fix: Use @vite(['resources/css/app.css', 'resources/js/app.js'])

🚫 [CRITICAL] BladeDirectiveCollision
   File: resources/views/layouts/app.blade.php:22
   Issue: Unescaped @vite in URL causing Blade directive collision
   Fix: Change '@vite/client' to '@@vite/client'
```

### **Pasos para resolver:**
1. **Ver detalles completos:**
   ```powershell
   .\tools\continue-qa.ps1 -Mode quick
   ```

2. **Fix manual** usando las sugerencias mostradas

3. **Fix con Continue AI:**
   - Selecciona el archivo problemático en VS Code
   - Copia el error del QA output
   - Usa comando personalizado: **"Continue QA Integration"**
   - Continue te dará la solución exacta

4. **Re-intentar commit:**
   ```powershell
   git add .
   git commit -m "fix: resolved QA issues"
   ```

## 🚨 **Bypass de Emergencia**
Si necesitas hacer un commit urgente **sin validación QA**:

```powershell
git commit --no-verify -m "hotfix: emergency commit"
```

⚠️ **USAR CON CUIDADO** - Solo para emergencias reales

## 🔧 **Continue AI Integration**

### **Workflow recomendado:**
```powershell
# 1. Ejecutar QA manualmente
.\tools\continue-qa.ps1 -Mode quick -JsonOutput

# 2. Si hay errores, copiar el JSON output

# 3. En VS Code Continue:
#    - Pegar el JSON output
#    - Usar comando "Continue QA Integration"
#    - Aplicar los fixes sugeridos

# 4. Re-validar
.\tools\continue-qa.ps1 -Mode quick

# 5. Commit cuando esté limpio
git commit -m "fix: resolved all QA issues"
```

## 📊 **Modos de QA Disponibles**
- `quick`: Solo Blade + Vite (usado por pre-commit)
- `full`: + Laravel health checks 
- `deploy`: + Security baseline

```powershell
# Para validación completa antes de deploy:
.\tools\continue-qa.ps1 -Mode deploy
```

## 🎯 **Best Practices**
1. **Ejecutar QA antes del commit:**
   ```powershell
   .\tools\continue-qa.ps1 -Mode quick
   ```

2. **Usar Continue AI** para fixes automáticos

3. **No hacer bypass** a menos que sea emergencia real

4. **Validar deploy** con modo `deploy` antes de subir a producción

## 🔄 **Troubleshooting**

### **Hook no se ejecuta:**
```powershell
# Verificar permisos (Linux/Mac):
chmod +x .git/hooks/pre-commit

# Windows: Ya debe funcionar
```

### **Error de PowerShell:**
```powershell
# Verificar que PowerShell está disponible:
pwsh --version
# o 
powershell --version
```

### **Script QA no encontrado:**
```powershell
# Verificar que estás en el directorio del proyecto:
ls tools/continue-qa.ps1
```

### **Deshabilitar hook temporalmente:**
```powershell
# Renombrar el hook:
mv .git/hooks/pre-commit .git/hooks/pre-commit.disabled

# Restaurar:  
mv .git/hooks/pre-commit.disabled .git/hooks/pre-commit
```