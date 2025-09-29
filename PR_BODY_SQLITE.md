Feature: SQLite persistence & bcrypt for CRM

This PR implements SQLite-backed persistence for users and projects and replaces plaintext passwords with bcrypt hashing.

Changes included:
- Migration SQL: `panorama_local/migrations/0001_initial.sql`
- Migration helper: `panorama_local/db_migration.py`
- Tests will be added/updated in follow-up commits (using temporary DB paths)

How to test locally:
1. Create a venv, install `panorama_local/requirements.txt`.
2. Run `python panorama_local/db_migration.py` to create DB.
3. Run tests with `pytest . -q` (tests use temporary DB paths).

Migration notes:
- The helper will create `panorama_local/data/panorama_crm.db`. Ensure `*.db` is in `.gitignore`.
