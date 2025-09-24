Docker Quick Start
------------------

This repository includes `docker-compose.yml` and Dockerfiles to quickly run the Laravel backend and Vite frontend locally.

Build and start both services:

```powershell
docker compose up --build
```

Services:
- Backend (Laravel): http://localhost:8095
- Frontend (Vite): http://localhost:5174

Notes:
- The frontend is configured to use `http://host.docker.internal:8095` as the backend URL from inside Docker. If Docker on your system doesn't map `host.docker.internal`, update the `docker-compose.yml` environment variables or pass a custom `.env.docker`.
- If you need persistent DB or other services, extend `docker-compose.yml` with a `mysql`/`postgres` service and configure `laravel-backend/.env` accordingly.
