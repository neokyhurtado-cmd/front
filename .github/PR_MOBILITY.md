# Extractor: performance & robustness

## Cambios
- Respeto de `<base href>` al absolutizar imágenes.
- Logging de excepciones/HTTP no-OK (`Log::debug`).
- Ignorar `data:`, `javascript:`, `mailto:`.
- Fallbacks: `itemprop="image"`, `name="image"`.
- `fetchAndCache()` solo extrae en consola (no en web) para evitar timeouts.

## Motivo
Evitar bloqueos de 120s en la API al intentar scrapear múltiples sitios durante la petición web.

## DoD
- [ ] API responde < 1s
- [ ] Logs trazan fallos del extractor (dev)
- [ ] Sin regresiones en `minutesAgo` / shape de respuesta
- [ ] UI sin “loading infinito”

## Smoke
```bash
php -l app/Http/Controllers/MobilityNewsController.php
time curl -sS http://127.0.0.1:7070/api/mobility/news | head -c 400
tail -n 100 storage/logs/laravel.log
```

## Próximo PR
Command + scheduler para extracción en background (no bloquea web).
