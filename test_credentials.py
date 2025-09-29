#!/usr/bin/env python3
"""
Test script for user credentials: david / Prueba321*
"""

import sys
import os

# Add panorama_local to Python path
panorama_local_path = os.path.join(os.path.dirname(__file__), 'panorama_local')
if panorama_local_path not in sys.path:
    sys.path.insert(0, panorama_local_path)

from modules import crm

def test_credentials():
    print("Testing credentials for user 'david' with password 'Prueba321*'")
    print("=" * 60)

    # Create CRM instance with proper database path
    db_path = os.path.join(os.path.dirname(__file__), 'panorama_local', 'data', 'panorama_crm.db')
    crm_instance = crm.CRM(db_path=db_path)

    # Test login
    print("Attempting login...")
    login_success = crm_instance.login('david', 'Prueba321*')

    if login_success:
        print("✅ Login successful!")
        user_info = crm_instance.current_user()
        print(f"User info: {user_info}")

        # Test if user can access projects
        projects = crm_instance.list_projects('david')
        print(f"Number of projects: {len(projects) if projects else 0}")

        # Logout
        crm_instance.logout()
        print("✅ Logout successful")
    else:
        print("❌ Login failed - credentials may be incorrect or user doesn't exist")

        # Try to register the user if login failed
        print("Attempting to register user...")
        register_success = crm_instance.register('david', 'Prueba321*', 'Free')
        if register_success:
            print("✅ User registered successfully")
            print("Please try logging in again")
        else:
            print("❌ Registration failed - user may already exist")

if __name__ == "__main__":
    test_credentials()