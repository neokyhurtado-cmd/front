import sqlite3
import os

def ensure_db(db_path='panorama_local/data/panorama_crm.db'):
    os.makedirs(os.path.dirname(db_path), exist_ok=True)
    conn = sqlite3.connect(db_path)
    cur = conn.cursor()
    # execute migration SQL if file exists
    mig = os.path.join(os.path.dirname(__file__), 'migrations', '0001_initial.sql')
    if os.path.exists(mig):
        with open(mig, 'r', encoding='utf-8') as f:
            cur.executescript(f.read())
        conn.commit()
    conn.close()

if __name__ == '__main__':
    ensure_db()
