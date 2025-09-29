# Roadmap y Plan de Desarrollo: Aforos + PMT + Comparador de Velocidades

**Fecha:** 29 de septiembre de 2025  
**Versión actual:** v0.3.1 (rama `feat/aforos-drive-plantillas-pmt`)  
**Objetivo:** Cerrar gaps y automatizar estudios (básico → intermedio → avanzado) + PMT completos.

Esta matriz sirve como **fuente de verdad** para el desarrollo. Todo está listo para pegar en GitHub (issues, PR y milestones).

---

## 1) Roadmap Corto (por Releases)

### v0.3.1 → v0.3.2 (2 semanas)

* Integrar rama `feat/aforos-drive-plantillas-pmt` en `main`.
* UI "Comparador de velocidades" (Google Matrix), persistencia de resultados en CRM.
* Roles básicos en CRM (admin/verificador/analista) y versionado de informes.
* Plantilla **Docx/PDF** inicial para PMT + Estudio (docxtpl/reportlab) con anexos (aforos normalizados, PHF, HMD).
* Verificador normativo v1 (cartilla base, chequeos mínimos de señalización y sentidos).

### v0.4 (4–6 semanas)

* Generador de **puntos de aforo** asistido por mapa (Deck.gl) + catálogo de movimientos/códigos.
* Adaptador de **simulación real** (CityFlow o similar) + calibración con aforos.
* HCM extendido (movimientos, giros, colas por fase, tabla LOS por movimiento).
* Panel Admin de proyectos (workflow: En revisión → Aprobado → Observado → Renovado).
* Perfiles normativos por entidad (Bogotá/IDU, etc.) con selector en UI.

### v0.5+ (avanzado)

* Integraciones: Waze (cuando haya acceso), HERE/TomTom alternativas.
* Modelación sincrónica (Synchro/Sidra/Vissim) vía adaptadores.
* Renovación automática ante observaciones: parser → diffs → v+1.

---

## 2) Issues Listos para Crear

> **Tip:** Pégalos como issues individuales o usa `gh issue create` con estos títulos y descripciones. Incluyen criterios de aceptación (CA).

### 2.1 Integración de aforos (branch → main)

**Título:** Integrar módulo de aforos (Apps Script + normalizador + verificador + generador ZIP)  
**Descripción:** Merge de `feat/aforos-drive-plantillas-pmt` en `main`. Mantener verificador sin revelar números.  
**CA:**
- [ ] Cargar aforos por Apps Script/CSV/XLSX.
- [ ] Normalización con equivalencias, VPH, PHF (0.7–0.95), HMD.
- [ ] ZIP con `aforos_normalizados.csv`, `metadata.json`, `resumen.json`.
- [ ] 15/15 tests verdes (aforos + verifier).

### 2.2 Roles y versionado en CRM

**Título:** CRM: roles (admin/verificador/analista) + versionado de informes  
**CA:**
- [ ] RBAC almacenado en SQLite.
- [ ] Versionado por proyecto (`v1`, `v1.1`, etc.).
- [ ] Bitácora de acciones (quién/qué/cuándo).

### 2.3 Comparador de velocidades (Google Matrix) + persistencia

**Título:** UI "Comparador de velocidades": consultas a Google Matrix y guardado en proyecto  
**CA:**
- [ ] Form de pares OD + `departure_time=now` + `traffic_model=best_guess`.
- [ ] Tabla "actual vs. proyecto" con % cambio.
- [ ] Guardar resultados en CRM y exportar CSV/XLSX/PDF/HTML.
- [ ] Flag de proveedor (google | waze[disabled]).

### 2.4 Plantillas Docx/PDF de PMT y Estudio (v1)

**Título:** Plantillas Jinja/docxtpl para PMT y Estudio con anexos  
**CA:**
- [ ] Portada + índice + capítulos estándar (intro, normativa, metodología, análisis, conclusiones).
- [ ] Inserción de tablas: aforos, PHF/HMD, LOS básico, comparador de velocidades.
- [ ] Export Docx y PDF (reportlab).
- [ ] ZIP con anexos (CSV/XLSX/PDF/HTML).

### 2.5 Verificador normativo v1

**Título:** Verificador normativo (señalización mínima + sentidos viales)  
**CA:**
- [ ] Regla de señalización temporal base por categoría PMT (I/II/III).
- [ ] Chequeo de coherencia de sentidos (regla simple + aviso).
- [ ] Salida **sin números**; solo "qué está mal y cómo corregir".

### 2.6 Puntos de aforo asistidos por mapa (MVP)

**Título:** Asistente para generar puntos de aforo y movimientos (Deck.gl)  
**CA:**
- [ ] Sugerencia de puntos y movimientos (entradas/salidas/peatones/ciclistas).
- [ ] Códigos estandarizados por punto/mov.
- [ ] Edición manual + guardado al proyecto.

### 2.7 HCM extendido (movimientos/colas)

**Título:** HCM extendido: movimientos, giros, colas por fase, LOS por movimiento  
**CA:**
- [ ] Inputs por carril/fase, salida por movimiento.
- [ ] Tabla LOS por movimiento con recomendaciones.
- [ ] Export a plantillas.

### 2.8 Adaptador de simulación real (CityFlow MVP)

**Título:** Adaptador simulación CityFlow (MVP) + calibración con aforos  
**CA:**
- [ ] Contrato I/O (JSON).
- [ ] Calibración mínima vs. aforos.
- [ ] Métricas mapeadas (avg_travel_time, queue, speed).

### 2.9 Panel Admin de flujo de trabajo

**Título:** Panel Admin: estados del proyecto y revisión  
**CA:**
- [ ] Flujo: "En revisión" → "Aprobado" → "Observado" → "Renovado".
- [ ] Vistas de control y filtros por estado/dueño.
- [ ] Notas y checklist por revisión.

### 2.10 Cumplimiento/No-scraping y configuración

**Título:** Política proveedores y configuración segura  
**CA:**
- [ ] Documentar uso de Google Matrix (no scraping).
- [ ] Flags de proveedor y manejo de cuotas/caching.
- [ ] Secrets en `st.secrets` / env.

---

## 3) PR Listo para Crear

**Título:** Aforos + Verificador (PHF/HMD) + Generador base PMT/Estudio + Comparador de velocidades (UI)  
**Descripción:**

* **Objetivo:** integrar aforos como fuente única, verificador sin revelar números, ZIP de anexos y comparador de velocidades con persistencia.
* **Incluye:** ingestión Apps Script/CSV/XLSX, normalizador (equivalencias, VPH, PHF, HMD), verificador, páginas 06–08, plantillas base, export CSV/XLSX/PDF/HTML.
* **Cómo probar:**
  1. "Cargar Aforos": pegar URL de Apps Script o subir XLSX.
  2. "Verificador": revisar hallazgos (sin números).
  3. "Generador PMT/Estudio": crear ZIP y abrir `resumen.json`.
  4. "Comparador de Velocidades": calcular y guardar en proyecto.
* **Checklist:**
  - [ ] 15/15 tests verdes.
  - [ ] PHF 0.7–0.95; HMD detectado en datasets 16h.
  - [ ] ZIP con anexos generado.
  - [ ] Sin secretos hardcodeados.

---

## 4) Comandos Útiles (gh CLI)

```bash
# Milestone (si no existe)
gh milestone create "v0.3.2" -D 2025-10-24 -d "Integración aforos + plantillas PMT/Estudio + comparador de velocidades + roles"

# Crear etiquetas comunes
for l in v0.3.2 backend frontend infra/CI docs verifier pmts aforos hcm simulacion; do
  gh label create "$l" --color BFD4F2 || true
done
```

---

## 5) Señales de "Hecho" por Nivel de Estudio

* **Básico:** aforos normalizados + PHF/HMD + HCM básico + comparador de velocidades + PMT v1 (docx/pdf) → **entregable ZIP completo**.
* **Intermedio:** puntos de aforo por mapa + HCM extendido + verificador normativo v1 + panel admin + simulación CityFlow MVP → **validado con aforos**.
* **Avanzado:** integraciones Waze/HERE, modelación Sidra/Vissim, perfiles normativos múltiples, renovación automática ante observaciones.

---

**Notas finales:**
- Esta documentación debe mantenerse actualizada con cada release.
- Usar milestones para tracking de progreso.
- Priorizar issues por impacto y dependencia.</content>
<parameter name="filePath">C:\laragon\www\panorama-ingenieria\.clean_push_repo\docs\ROADMAP_AFOROS_PMT.md