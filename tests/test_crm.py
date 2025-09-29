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
    assert c.add_project("u1", project) is True
    projects = c.list_projects("u1")
    assert isinstance(projects, list)
    assert len(projects) == 1

    # set plan
    assert c.set_plan("u1", "Pro") is True
    u = c.find_user("u1")
    assert u and u.get("plan") == "Pro"
