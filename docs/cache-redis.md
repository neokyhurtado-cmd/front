# Redis Cache (recommended for production)

This project uses the `database`/`file` cache by default for local development. For production workloads where frequent `Cache::increment` operations or high cache I/O are expected, Redis is strongly recommended because increments are atomic and much more efficient.

Quick steps to enable Redis locally:

1. Install Redis (macOS/homebrew, Linux package manager, or Docker):

   - Docker:
     ```bash
     docker run -d --name redis -p 6379:6379 redis:7
     ```

2. Install PHP Redis extension (preferred) or predis fallback:

   - Preferred (phpredis): enable `phpredis` extension in your PHP runtime.
   - Alternative (no extension): install predis client:
     ```bash
     composer require predis/predis --no-dev
     ```

3. Update `.env` (do not commit `.env`):

   ```env
   CACHE_DRIVER=redis
   REDIS_CLIENT=phpredis  # or predis
   REDIS_HOST=127.0.0.1
   REDIS_PORT=6379
   ```

4. Clear and cache configuration:

   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan config:cache
   ```

5. Smoke test (atomic increment):

   ```bash
   php artisan tinker
   >>> Cache::put('mobility.image.hits', 0);
   >>> Cache::increment('mobility.image.hits');
   >>> Cache::get('mobility.image.hits'); // should be 1
   ```

6. End-to-end test:

   ```bash
   php artisan serve --host=127.0.0.1 --port=7070
   curl -sS http://127.0.0.1:7070/api/mobility/news > /tmp/mob.json
   ```

Notes:
- Keep `CACHE_DRIVER=database` or `file` in local `.env` if you rely on the database cache for debugging. Document that Redis is the recommended production driver.
- Rolling back: set `CACHE_DRIVER=database` in `.env` and clear config cache.

If you want, I can prepare a branch and commit these changes and open a PR with the checklist and smoke-test guidance.
