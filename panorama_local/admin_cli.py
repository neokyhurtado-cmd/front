import argparse
import os
import json

from modules import crm


def list_users():
    conn = crm.CRM()._connect()
    c = conn.cursor()
    c.execute("SELECT username, plan FROM users ORDER BY username")
    for row in c.fetchall():
        print(f"{row[0]}\t{row[1]}")
    conn.close()


def list_projects(username: str):
    c = crm.CRM()
    projects = c.list_projects(username)
    for i, p in enumerate(projects, 1):
        print(f"Project {i}:")
        print(json.dumps(p, indent=2))


def clear_db():
    dbfile = os.path.join(os.path.dirname(__file__), 'data', 'panorama_crm.db')
    if os.path.exists(dbfile):
        os.remove(dbfile)
        print('DB removed:', dbfile)
    else:
        print('DB file not found:', dbfile)


def main():
    p = argparse.ArgumentParser()
    p.add_argument('cmd', choices=['list-users', 'list-projects', 'clear-db'])
    p.add_argument('--user', '-u', help='username for list-projects')
    args = p.parse_args()

    if args.cmd == 'list-users':
        list_users()
    elif args.cmd == 'list-projects':
        if not args.user:
            print('Please provide --user')
            return
        list_projects(args.user)
    elif args.cmd == 'clear-db':
        clear_db()


if __name__ == '__main__':
    main()
