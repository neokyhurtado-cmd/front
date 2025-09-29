"""CRM sencillo con persistencia SQLite y hash            created_at TEXT,      created_at TEXT, contraseñas.

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
from datetime import datetime

import bcrypt

# DB path
DB_DIR = os.path.join(os.path.dirname(__file__), "..", "data")
DB_PATH = os.path.join(DB_DIR, "panorama_crm.db")


def _ensure_db(db_path: Optional[str] = None):
    path = db_path or DB_PATH
    dirpath = os.path.dirname(path)
    os.makedirs(dirpath, exist_ok=True)

    # Also ensure logs directory exists
    logs_dir = os.path.join(os.path.dirname(__file__), "..", "logs")
    os.makedirs(logs_dir, exist_ok=True)

    conn = sqlite3.connect(path)
    c = conn.cursor()

    # SQLite optimizations for better performance and concurrency
    c.execute("PRAGMA journal_mode=WAL")  # Write-Ahead Logging for better concurrency
    c.execute("PRAGMA synchronous=NORMAL")  # Balance between performance and safety
    c.execute("PRAGMA cache_size=1000")  # Increase cache size
    c.execute("PRAGMA temp_store=MEMORY")  # Store temp tables in memory
    c.execute("PRAGMA foreign_keys=ON")  # Enable foreign key constraints

    # users: id, username UNIQUE, password_hash, plan, created_at
    c.execute(
        """
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            plan TEXT DEFAULT 'free',
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )
        """
    )
    # login_attempts: username, attempt_time, success (for rate limiting and lockout)
    c.execute(
        """
        CREATE TABLE IF NOT EXISTS login_attempts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            attempt_time TEXT NOT NULL,
            success INTEGER NOT NULL
        )
        """
    )
    # projects: id, username FK, project_json, name, notes, created_at
    c.execute(
        """
        CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            project_json TEXT NOT NULL,
            name TEXT,
            notes TEXT,
            created_at TEXT,
            FOREIGN KEY(username) REFERENCES users(username)
        )
        """
    )
    # speed_comparisons: id, username FK, origin, destination, distance_meters, duration_seconds, duration_traffic_seconds, created_at
    c.execute(
        """
        CREATE TABLE IF NOT EXISTS speed_comparisons (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            origin TEXT NOT NULL,
            destination TEXT NOT NULL,
            distance_meters INTEGER,
            duration_seconds INTEGER,
            duration_traffic_seconds INTEGER,
            created_at TEXT,
            FOREIGN KEY(username) REFERENCES users(username)
        )
        """
    )
    # roles: id, name
    c.execute(
        """
        CREATE TABLE IF NOT EXISTS roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT UNIQUE NOT NULL CHECK (name IN ('admin','verificador','analista'))
        )
        """
    )
    # user_roles: user_id, role_id (N:M relationship)
    c.execute(
        """
        CREATE TABLE IF NOT EXISTS user_roles (
            user_id INTEGER NOT NULL,
            role_id INTEGER NOT NULL,
            PRIMARY KEY (user_id, role_id),
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(role_id) REFERENCES roles(id) ON DELETE CASCADE
        )
        """
    )
    # report_versions: id, project_id, version, kind, status, created_at, meta_json
    c.execute(
        """
        CREATE TABLE IF NOT EXISTS report_versions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            project_id INTEGER NOT NULL,
            version TEXT NOT NULL,
            kind TEXT NOT NULL CHECK (kind IN ('PMT','Estudio')),
            status TEXT NOT NULL CHECK (status IN ('en_revision','aprobado','observado','renovado')),
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            meta_json TEXT,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        )
        """
    )
    # Lightweight migration: ensure columns exist (for older DBs)
    try:
        c.execute("PRAGMA table_info(projects)")
        cols = [row[1] for row in c.fetchall()]
        # add missing columns if needed
        if 'name' not in cols:
            c.execute("ALTER TABLE projects ADD COLUMN name TEXT")
        if 'notes' not in cols:
            c.execute("ALTER TABLE projects ADD COLUMN notes TEXT")
        if 'created_at' not in cols:
            c.execute("ALTER TABLE projects ADD COLUMN created_at TEXT")
    except Exception:
        # if PRAGMA fails (table might not exist yet) ignore and proceed
        pass

    # Create indexes for better performance
    c.execute("CREATE INDEX IF NOT EXISTS idx_projects_created_at ON projects(created_at)")
    c.execute("CREATE INDEX IF NOT EXISTS idx_projects_name ON projects(name)")
    c.execute("CREATE INDEX IF NOT EXISTS idx_projects_username ON projects(username)")
    c.execute("CREATE INDEX IF NOT EXISTS idx_login_attempts_username_time ON login_attempts(username, attempt_time)")

    conn.commit()
    conn.close()


def _init_default_roles(db_path: str = DB_PATH):
    """Initialize default roles if they don't exist."""
    conn = sqlite3.connect(db_path)
    c = conn.cursor()
    default_roles = ['admin', 'verificador', 'analista']
    for role in default_roles:
        try:
            c.execute("INSERT INTO roles (name) VALUES (?)", (role,))
        except sqlite3.IntegrityError:
            # Role already exists
            pass
    conn.commit()
    conn.close()


class CRM:
    def __init__(self, db_path: str = DB_PATH):
        self.db_path = db_path
        _ensure_db(self.db_path)
        _init_default_roles(self.db_path)
        self._current_username: Optional[str] = None

    def _connect(self):
        return sqlite3.connect(self.db_path)

    def _get_connection(self):
        """Alias for _connect for consistency."""
        return self._connect()

    def register(self, username: str, password: str, plan: str = "Free") -> bool:
        # check exists
        if self.find_user(username):
            return False
        # Use higher bcrypt rounds for better security (≥12)
        pw_hash = bcrypt.hashpw(password.encode("utf-8"), bcrypt.gensalt(rounds=12))
        conn = self._connect()
        c = conn.cursor()
        c.execute("INSERT INTO users (username, password_hash, plan) VALUES (?, ?, ?)", (username, pw_hash, plan))
        conn.commit()
        conn.close()
        self._current_username = username
        return True

    def login(self, username: str, password: str) -> bool:
        from datetime import datetime, timedelta, timezone

        # Check for lockout: 5 failed attempts in last 15 minutes
        conn = self._connect()
        c = conn.cursor()
        fifteen_min_ago = (datetime.now(timezone.utc) - timedelta(minutes=15)).isoformat()
        c.execute(
            "SELECT COUNT(*) FROM login_attempts WHERE username = ? AND attempt_time > ? AND success = 0",
            (username, fifteen_min_ago)
        )
        failed_attempts = c.fetchone()[0]
        if failed_attempts >= 5:
            conn.close()
            return False  # Account locked

        # Record login attempt
        attempt_time = datetime.now(timezone.utc).isoformat()
        u = self.find_user(username)
        if not u:
            # User doesn't exist - record failed attempt
            c.execute("INSERT INTO login_attempts (username, attempt_time, success) VALUES (?, ?, 0)",
                     (username, attempt_time))
            conn.commit()
            conn.close()
            return False

        stored = u.get("password_hash")
        if not stored:
            # Invalid password hash - record failed attempt
            c.execute("INSERT INTO login_attempts (username, attempt_time, success) VALUES (?, ?, 0)",
                     (username, attempt_time))
            conn.commit()
            conn.close()
            return False

        try:
            ok = bcrypt.checkpw(password.encode("utf-8"), stored)
        except Exception:
            # Password check failed - record failed attempt
            c.execute("INSERT INTO login_attempts (username, attempt_time, success) VALUES (?, ?, 0)",
                     (username, attempt_time))
            conn.commit()
            conn.close()
            return False

        if ok:
            # Successful login
            c.execute("INSERT INTO login_attempts (username, attempt_time, success) VALUES (?, ?, 1)",
                     (username, attempt_time))
            conn.commit()
            conn.close()
            self._current_username = username
            return True
        else:
            # Failed login
            c.execute("INSERT INTO login_attempts (username, attempt_time, success) VALUES (?, ?, 0)",
                     (username, attempt_time))
            conn.commit()
            conn.close()
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

    def add_project(self, username: str, project: dict, name: Optional[str] = None, notes: Optional[str] = None) -> int:
        import json

        if not self.find_user(username):
            return -1
        conn = self._connect()
        c = conn.cursor()
        # legacy simple insert: but we now support metadata
        created_at = None
        try:
            from datetime import datetime, timezone

            created_at = datetime.now(timezone.utc).isoformat()
        except Exception:
            created_at = None
        c.execute(
            "INSERT INTO projects (username, project_json, name, notes, created_at) VALUES (?, ?, ?, ?, ?)",
            (username, json.dumps(project), None, None, created_at),
        )
        conn.commit()
        last_id = c.lastrowid
        conn.close()
        return last_id

    def list_projects(self, username: str) -> List[dict]:
        import json

        if not self.find_user(username):
            return []
        conn = self._connect()
        c = conn.cursor()
        c.execute(
            "SELECT id, project_json, name, notes, created_at FROM projects WHERE username = ? ORDER BY id",
            (username,),
        )
        rows = c.fetchall()
        conn.close()
        out: List[dict] = []
        for r in rows:
            pid = r[0]
            pj = json.loads(r[1])
            name = r[2]
            notes = r[3]
            created_at = r[4]
            out.append({"id": pid, "project": pj, "name": name, "notes": notes, "created_at": created_at})
        return out

    def delete_project(self, username: str, project_id: int) -> bool:
        conn = self._connect()
        c = conn.cursor()
        c.execute("DELETE FROM projects WHERE id = ? AND username = ?", (project_id, username))
        conn.commit()
        changed = c.rowcount > 0
        conn.close()
        return changed

    def add_project_with_meta(self, username: str, project: dict, name: Optional[str] = None, notes: Optional[str] = None) -> Optional[int]:
        """Add a project with metadata. Returns inserted id or None on failure."""
        import json
        from datetime import datetime, timezone

        if not self.find_user(username):
            return None
        created_at = datetime.now(timezone.utc).isoformat()
        conn = self._connect()
        c = conn.cursor()
        c.execute(
            "INSERT INTO projects (username, project_json, name, notes, created_at) VALUES (?, ?, ?, ?, ?)",
            (username, json.dumps(project), name, notes, created_at),
        )
        conn.commit()
        last_id = c.lastrowid
        conn.close()
        return last_id

    def find_user(self, username: str) -> Optional[Dict[str, Any]]:
        conn = self._connect()
        c = conn.cursor()
        c.execute("SELECT id, username, password_hash, plan FROM users WHERE username = ?", (username,))
        row = c.fetchone()
        conn.close()
        if not row:
            return None
        return {"id": row[0], "username": row[1], "password_hash": row[2], "plan": row[3]}

    def add_speed_comparison(self, username, origin, destination, distance_meters, duration_seconds, duration_traffic_seconds):
        """Add a speed comparison record."""
        with self._get_connection() as conn:
            c = conn.cursor()
            c.execute(
                """
                INSERT INTO speed_comparisons (username, origin, destination, distance_meters, duration_seconds, duration_traffic_seconds, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                """,
                (username, origin, destination, distance_meters, duration_seconds, duration_traffic_seconds, datetime.now().isoformat())
            )
            conn.commit()

    def create_role(self, name):
        """Create a new role if it doesn't exist."""
        conn = self._connect()
        c = conn.cursor()
        try:
            c.execute("INSERT INTO roles (name) VALUES (?)", (name,))
            conn.commit()
            result = c.lastrowid
        except sqlite3.IntegrityError:
            # Role already exists, return existing role id
            c.execute("SELECT id FROM roles WHERE name = ?", (name,))
            existing = c.fetchone()
            result = existing[0] if existing else None
        conn.close()
        return result

    def assign_role_to_user(self, username, role_name):
        """Assign a role to a user."""
        conn = self._connect()
        c = conn.cursor()
        # Get user id
        c.execute("SELECT id FROM users WHERE username = ?", (username,))
        user_row = c.fetchone()
        if not user_row:
            conn.close()
            raise ValueError(f"User {username} not found")
        user_id = user_row[0]
        
        # Get role id
        c.execute("SELECT id FROM roles WHERE name = ?", (role_name,))
        role_row = c.fetchone()
        if not role_row:
            conn.close()
            raise ValueError(f"Role {role_name} not found")
        role_id = role_row[0]
        
        # Assign role
        try:
            c.execute("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)", (user_id, role_id))
            conn.commit()
        except sqlite3.IntegrityError:
            # Role already assigned
            pass
        conn.close()

    def get_user_roles(self, username):
        """Get all roles for a user."""
        conn = self._connect()
        c = conn.cursor()
        c.execute(
            """
            SELECT r.name FROM roles r
            JOIN user_roles ur ON r.id = ur.role_id
            JOIN users u ON u.id = ur.user_id
            WHERE u.username = ?
            """,
            (username,)
        )
        roles = [row[0] for row in c.fetchall()]
        conn.close()
        return roles

    def has_role(self, username, role_name):
        """Check if user has a specific role."""
        roles = self.get_user_roles(username)
        return role_name in roles

    # Report versioning methods
    def create_report_version(self, project_id, version, kind, status, meta_json=None):
        """Create a new report version."""
        conn = self._connect()
        c = conn.cursor()
        c.execute(
            """
            INSERT INTO report_versions (project_id, version, kind, status, meta_json)
            VALUES (?, ?, ?, ?, ?)
            """,
            (project_id, version, kind, status, meta_json)
        )
        conn.commit()
        result = c.lastrowid
        conn.close()
        return result

    def get_report_versions(self, project_id):
        """Get all report versions for a project."""
        conn = self._connect()
        c = conn.cursor()
        c.execute(
            "SELECT * FROM report_versions WHERE project_id = ? ORDER BY created_at DESC",
            (project_id,)
        )
        versions = c.fetchall()
        conn.close()
        return versions

    def update_report_version_status(self, version_id, status):
        """Update the status of a report version."""
        conn = self._connect()
        c = conn.cursor()
        c.execute(
            "UPDATE report_versions SET status = ? WHERE id = ?",
            (status, version_id)
        )
        conn.commit()
        conn.close()

    def get_latest_report_version(self, project_id, kind):
        """Get the latest version of a specific kind for a project."""
        conn = self._connect()
        c = conn.cursor()
        c.execute(
            "SELECT * FROM report_versions WHERE project_id = ? AND kind = ? ORDER BY id DESC LIMIT 1",
            (project_id, kind)
        )
        result = c.fetchone()
        conn.close()
        return result

    def list_speed_comparisons(self, username: str) -> List[dict]:
        """List speed comparisons for a user."""
        if not self.find_user(username):
            return []
        conn = self._connect()
        c = conn.cursor()
        c.execute(
            "SELECT id, origin, destination, distance_meters, duration_seconds, duration_traffic_seconds, created_at FROM speed_comparisons WHERE username = ? ORDER BY created_at DESC",
            (username,),
        )
        rows = c.fetchall()
        conn.close()
        out: List[dict] = []
        for r in rows:
            out.append({
                "id": r[0],
                "origin": r[1],
                "destination": r[2],
                "distance_meters": r[3],
                "duration_seconds": r[4],
                "duration_traffic_seconds": r[5],
                "created_at": r[6]
            })
        return out
