"""Tests for RBAC and report versioning functionality."""

import pytest
import os
import sys
import tempfile
import json
from datetime import datetime

from panorama_local.modules import crm as crm_module


class TestRBAC:
    """Test RBAC functionality."""

    def setup_method(self):
        """Set up test database."""
        self.db_fd, self.db_path = tempfile.mkstemp()
        self.crm = crm_module.CRM(self.db_path)

    def teardown_method(self):
        """Clean up test database."""
        os.close(self.db_fd)
        os.unlink(self.db_path)

    def test_role_creation_and_assignment(self):
        """Test creating roles and assigning them to users."""
        # Register a user
        assert self.crm.register("testuser", "testpass", "free")

        # Create a role (should work since roles are auto-initialized)
        role_id = self.crm.create_role("analista")  # Use valid role
        assert role_id is not None

        # Assign role to user
        self.crm.assign_role_to_user("testuser", "analista")

        # Check if user has role
        assert self.crm.has_role("testuser", "analista")
        assert not self.crm.has_role("testuser", "admin")

        # Get user roles
        roles = self.crm.get_user_roles("testuser")
        assert "analista" in roles

    def test_default_roles_initialized(self):
        """Test that default roles are initialized."""
        # Register a user
        assert self.crm.register("testuser", "testpass", "free")

        # Assign default roles
        self.crm.assign_role_to_user("testuser", "admin")
        self.crm.assign_role_to_user("testuser", "verificador")
        self.crm.assign_role_to_user("testuser", "analista")

        # Check all roles
        roles = self.crm.get_user_roles("testuser")
        assert "admin" in roles
        assert "verificador" in roles
        assert "analista" in roles

    def test_role_assignment_validation(self):
        """Test role assignment validation."""
        # Register a user
        assert self.crm.register("testuser", "testpass", "free")

        # Try to assign invalid role
        with pytest.raises(ValueError):
            self.crm.assign_role_to_user("testuser", "invalid_role")

        # Try to assign role to non-existent user
        with pytest.raises(ValueError):
            self.crm.assign_role_to_user("nonexistent", "analista")


class TestReportVersioning:
    """Test report versioning functionality."""

    def setup_method(self):
        """Set up test database."""
        self.db_fd, self.db_path = tempfile.mkstemp()
        self.crm = crm_module.CRM(self.db_path)

        # Register a user and create a project
        assert self.crm.register("testuser", "testpass", "free")
        self.project_id = self.crm.add_project("testuser", {"test": "data"})

    def teardown_method(self):
        """Clean up test database."""
        os.close(self.db_fd)
        os.unlink(self.db_path)

    def test_create_report_version(self):
        """Test creating report versions."""
        # Create a PMT version
        version_id = self.crm.create_report_version(
            self.project_id, "1.0", "PMT", "en_revision",
            json.dumps({"comentarios": "Versión inicial"})
        )
        assert version_id is not None

        # Create an Estudio version
        version_id2 = self.crm.create_report_version(
            self.project_id, "1.1", "Estudio", "aprobado"
        )
        assert version_id2 is not None
        assert version_id2 != version_id

    def test_get_report_versions(self):
        """Test getting report versions for a project."""
        # Create some versions
        self.crm.create_report_version(self.project_id, "1.0", "PMT", "en_revision")
        self.crm.create_report_version(self.project_id, "1.1", "PMT", "aprobado")
        self.crm.create_report_version(self.project_id, "1.0", "Estudio", "observado")

        # Get all versions
        versions = self.crm.get_report_versions(self.project_id)
        assert len(versions) == 3

        # Check version details
        pmt_versions = [v for v in versions if v[3] == "PMT"]
        assert len(pmt_versions) == 2

    def test_update_report_version_status(self):
        """Test updating report version status."""
        # Create a version
        version_id = self.crm.create_report_version(
            self.project_id, "1.0", "PMT", "en_revision"
        )

        # Update status
        self.crm.update_report_version_status(version_id, "aprobado")

        # Check updated status
        versions = self.crm.get_report_versions(self.project_id)
        updated_version = next(v for v in versions if v[0] == version_id)
        assert updated_version[4] == "aprobado"

    def test_get_latest_report_version(self):
        """Test getting the latest version of a specific kind."""
        # Create versions with timestamps
        import time
        time.sleep(0.01)  # Ensure different timestamps
        self.crm.create_report_version(self.project_id, "1.0", "PMT", "en_revision")
        time.sleep(0.01)
        self.crm.create_report_version(self.project_id, "1.1", "PMT", "aprobado")
        time.sleep(0.01)
        self.crm.create_report_version(self.project_id, "1.0", "Estudio", "observado")

        # Get latest PMT version
        latest_pmt = self.crm.get_latest_report_version(self.project_id, "PMT")
        assert latest_pmt[2] == "1.1"
        assert latest_pmt[4] == "aprobado"

        # Get latest Estudio version
        latest_estudio = self.crm.get_latest_report_version(self.project_id, "Estudio")
        assert latest_estudio[2] == "1.0"
        assert latest_estudio[4] == "observado"


class TestIntegration:
    """Integration tests for RBAC and versioning."""

    def setup_method(self):
        """Set up test database."""
        self.db_fd, self.db_path = tempfile.mkstemp()
        self.crm = crm_module.CRM(self.db_path)

    def teardown_method(self):
        """Clean up test database."""
        os.close(self.db_fd)
        os.unlink(self.db_path)

    def test_admin_workflow(self):
        """Test complete admin workflow."""
        # Register admin user
        assert self.crm.register("admin", "adminpass", "premium")
        self.crm.assign_role_to_user("admin", "admin")

        # Register regular user
        assert self.crm.register("analyst", "analystpass", "free")
        self.crm.assign_role_to_user("analyst", "analista")

        # Create project as analyst
        project_id = self.crm.add_project("analyst", {"test": "project"})

        # Create report versions
        version_id = self.crm.create_report_version(
            project_id, "1.0", "PMT", "en_revision"
        )

        # Update status as admin
        self.crm.update_report_version_status(version_id, "aprobado")

        # Verify final state
        assert self.crm.has_role("admin", "admin")
        assert self.crm.has_role("analyst", "analista")

        versions = self.crm.get_report_versions(project_id)
        assert len(versions) == 1
        assert versions[0][4] == "aprobado"
