# Panorama PMT - Makefile
# Standard development workflow commands

.PHONY: help install run test coverage clean release deploy smoke-test

# Default target
help:
	@echo "Panorama PMT - Development Commands"
	@echo ""
	@echo "Available commands:"
	@echo "  install      Install dependencies"
	@echo "  run          Run the Streamlit app locally"
	@echo "  test         Run all tests"
	@echo "  coverage     Run tests with coverage report"
	@echo "  smoke-test   Run smoke test suite"
	@echo "  clean        Clean cache files and artifacts"
	@echo "  release      Prepare and create release"
	@echo "  deploy       Deploy to Streamlit Cloud"
	@echo ""
	@echo "Example: make test"

# Install dependencies
install:
	@echo "Installing dependencies..."
	pip install -r requirements.txt
	pip install -r panorama_local/requirements.txt

# Run the app locally
run:
	@echo "Starting Streamlit app..."
	streamlit run app.py --server.headless true --server.port 8501

# Run tests
test:
	@echo "Running tests..."
	pytest -v

# Run tests with coverage
coverage:
	@echo "Running tests with coverage..."
	pytest --cov=panorama_local --cov-report=html --cov-report=term-missing
	@echo "Coverage report generated in htmlcov/"

# Run smoke tests
smoke-test:
	@echo "Running smoke tests..."
	python panorama_local/smoke_test.py

# Clean cache and artifacts
clean:
	@echo "Cleaning cache files..."
	find . -type d -name __pycache__ -exec rm -rf {} +
	find . -type d -name "*.pyc" -delete
	find . -type d -name ".pytest_cache" -exec rm -rf {} +
	find . -type d -name "htmlcov" -exec rm -rf {} +
	find . -name "*.pyc" -delete
	find . -name "*.pyo" -delete
	find . -name "*.pyd" -delete

# Prepare release (comprehensive check)
release: clean test coverage smoke-test
	@echo "=== RELEASE PREPARATION ==="
	@echo "1. All tests passed ✓"
	@echo "2. Coverage report generated ✓"
	@echo "3. Smoke tests passed ✓"
	@echo ""
	@echo "Next steps:"
	@echo "1. Update version in code if needed"
	@echo "2. Commit changes: git add . && git commit -m 'chore: prepare v0.3.1 release'"
	@echo "3. Create tag: git tag v0.3.1"
	@echo "4. Push: git push && git push --tags"
	@echo "5. Create GitHub release with CHANGELOG.md content"
	@echo ""
	@echo "Ready for release! 🚀"

# Deploy to Streamlit Cloud (placeholder - requires manual setup)
deploy:
	@echo "=== STREAMLIT CLOUD DEPLOYMENT ==="
	@echo "Manual steps required:"
	@echo "1. Go to https://share.streamlit.io/"
	@echo "2. Create new app with these settings:"
	@echo "   - Repository: neokyhurtado-cmd/front"
	@echo "   - Branch: main"
	@echo "   - Main file path: app.py"
	@echo "   - Python version: 3.10"
	@echo "   - Requirements file: requirements.txt"
	@echo "3. Add secrets if needed:"
	@echo "   CRM_DB_PATH='panorama_local/data/panorama_crm.db'"
	@echo ""
	@echo "After deployment, run: make smoke-test"
	@echo "(This will test the deployed app)"

# Development setup
setup: install
	@echo "Setting up development environment..."
	mkdir -p panorama_local/data
	mkdir -p panorama_local/logs
	@echo "Development environment ready!"

# Lint code (if black/flake8 installed)
lint:
	@echo "Linting code..."
	-black panorama_local/ --check --diff || echo "Install black: pip install black"
	-flake8 panorama_local/ --max-line-length=100 || echo "Install flake8: pip install flake8"

# Format code (if black installed)
format:
	@echo "Formatting code..."
	-black panorama_local/ || echo "Install black: pip install black"

# Show project structure
tree:
	@echo "Project structure:"
	@find . -type f -name "*.py" -o -name "*.md" -o -name "*.txt" -o -name "*.json" -o -name "*.yml" | grep -v __pycache__ | sort

# Show current status
status:
	@echo "=== PROJECT STATUS ==="
	@echo "Python version: $$(python --version)"
	@echo "Current branch: $$(git branch --show-current)"
	@echo "Last commit: $$(git log -1 --oneline)"
	@echo ""
	@echo "Requirements check:"
	@python -c "import streamlit, pandas, bcrypt, reportlab; print('✅ All core dependencies available')" 2>/dev/null || echo "❌ Missing dependencies - run 'make install'"
	@echo ""
	@echo "Test status:"
	@pytest --collect-only -q 2>/dev/null | grep -c "test session starts" >/dev/null && echo "✅ Tests discovered" || echo "❌ No tests found"