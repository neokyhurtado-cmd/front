import os
import tempfile
import sqlite3
import pytest

from panorama_local.modules import crm as crm_module


def test_register_login_and_projects(tmp_path):
    # create a temporary DB path
    db_file = tmp_path / "test_crm.db"
    c = crm_module.CRM(db_path=str(db_file))

    # register
    assert c.register("u1", "pass1", "Free") is True
    # cannot register same user
    assert c.register("u1", "pass1", "Free") is False

    # login with wrong password
    assert c.login("u1", "wrong") is False
    # login with correct
    assert c.login("u1", "pass1") is True

    # add project and list
    project = {"markers": [{"id": 1}], "plan": "Free"}
    pid = c.add_project_with_meta("u1", project, name="Test project", notes="note")
    assert isinstance(pid, int) and pid > 0
    projects = c.list_projects("u1")
    assert isinstance(projects, list)
    assert len(projects) == 1
    first = projects[0]
    assert first.get("id") == pid
    assert first.get("name") == "Test project"
    assert first.get("notes") == "note"

    # check project metadata keys
    p = projects[0]
    assert "id" in p
    assert "project" in p
    assert "name" in p
    assert "notes" in p
    assert "created_at" in p
    assert p["name"] == "Test project"
    assert p["notes"] == "note"
    assert p["project"] == project

    # delete project
    assert c.delete_project("u1", pid) is True
    projects = c.list_projects("u1")
    assert len(projects) == 0

    # set plan
    assert c.set_plan("u1", "Pro") is True
    u = c.find_user("u1")
    assert u and u.get("plan") == "Pro"

    # check password is hashed (not plain text)
    assert u.get("password_hash") != b"pass1"
    # and bcrypt can verify it
    import bcrypt
    assert bcrypt.checkpw(b"pass1", u["password_hash"])
