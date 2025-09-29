#!/usr/bin/env python3
"""
Panorama Ingeniería PMT Application Launcher

This is the main entry point for the Streamlit application.
It ensures proper import paths for deployment on Streamlit Cloud.
"""

import sys
import os

# Add repo ROOT to Python path (NOT panorama_local)
# Python searches for packages in the directory that contains the package
ROOT = os.path.dirname(__file__)
if ROOT not in sys.path:
    sys.path.insert(0, ROOT)

# Import and run the main application from the panorama_local package
from panorama_local.app import main

if __name__ == "__main__":
    main()