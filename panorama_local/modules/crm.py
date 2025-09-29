"""CRM sencillo con persistencia SQLite y hashing de contraseñas.

Proporciona una clase CRM que usa una base de datos SQLite local para:
- registrar usuarios (con contraseñas hasheadas con bcrypt),
- autenticar usuarios,
- almacenar proyectos PMT por usuario,
- cambiar planes de usuario.

Es intencionalmente simple: no hay control de concurrencia avanzado ni
migraciones. La base de datos se crea en `data/panorama_crm.db`.
"""
import sqlite3
import os
from typing import List, Optional, Dict, Any

import bcrypt

# DB path
DB_DIR = os.path.join(os.path.dirname(__file__), "..", "data")
DB_PATH = os.path.join(DB_DIR, "panorama_crm.db")


def _ensure_db(db_path: Optional[str] = None):
    path = db_path or DB_PATH
    dirpath = os.path.dirname(path)
    os.makedirs(dirpath, exist_ok=True)
    conn = sqlite3.connect(path)
    c = conn.cursor()
    # users: username (primary), password_hash, plan
    c.execute(
        """
        CREATE TABLE IF NOT EXISTS users (
            username TEXT PRIMARY KEY,
            password_hash BLOB NOT NULL,
            plan TEXT NOT NULL
        )
        """
    )
    # projects: id, username FK, project_json
    c.execute(
        """
        CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            project_json TEXT NOT NULL,
            FOREIGN KEY(username) REFERENCES users(username)
        )
        """
    )
    conn.commit()
    conn.close()


class CRM:
    def __init__(self, db_path: str = DB_PATH):
        self.db_path = db_path
        _ensure_db(self.db_path)
        self._current_username: Optional[str] = None

    def _connect(self):
        return sqlite3.connect(self.db_path)

    def register(self, username: str, password: str, plan: str = "Free") -> bool:
        # check exists
        if self.find_user(username):
            return False
        pw_hash = bcrypt.hashpw(password.encode("utf-8"), bcrypt.gensalt())
        conn = self._connect()
        c = conn.cursor()
        c.execute("INSERT INTO users (username, password_hash, plan) VALUES (?, ?, ?)", (username, pw_hash, plan))
        conn.commit()
        conn.close()
        self._current_username = username
        return True

    def login(self, username: str, password: str) -> bool:
        u = self.find_user(username)
        if not u:
            return False
        stored = u.get("password_hash")
        if not stored:
            return False
        try:
            ok = bcrypt.checkpw(password.encode("utf-8"), stored)
        except Exception:
            return False
        if ok:
            self._current_username = username
            return True
        return False

    def logout(self):
        self._current_username = None

    def is_authenticated(self) -> bool:
        return self._current_username is not None

    def current_user(self) -> Optional[Dict[str, Any]]:
        if not self._current_username:
            return None
        return self.find_user(self._current_username)

    def set_plan(self, username: str, plan: str) -> bool:
        conn = self._connect()
        c = conn.cursor()
        c.execute("UPDATE users SET plan = ? WHERE username = ?", (plan, username))
        conn.commit()
        changed = c.rowcount > 0
        conn.close()
        return changed

    def add_project(self, username: str, project: dict) -> bool:
        import json

        if not self.find_user(username):
            return False
        conn = self._connect()
        c = conn.cursor()
        c.execute("INSERT INTO projects (username, project_json) VALUES (?, ?)", (username, json.dumps(project)))
        conn.commit()
        conn.close()
        return True

    def list_projects(self, username: str) -> List[dict]:
        import json

        if not self.find_user(username):
            return []
        conn = self._connect()
        c = conn.cursor()
        c.execute("SELECT project_json FROM projects WHERE username = ? ORDER BY id", (username,))
        rows = c.fetchall()
        conn.close()
        return [json.loads(r[0]) for r in rows]

    def find_user(self, username: str) -> Optional[Dict[str, Any]]:
        conn = self._connect()
        c = conn.cursor()
        c.execute("SELECT username, password_hash, plan FROM users WHERE username = ?", (username,))
        row = c.fetchone()
        conn.close()
        if not row:
            return None
        return {"username": row[0], "password_hash": row[1], "plan": row[2]}


# Instancia global para acceder desde la app
global_crm = CRM()
