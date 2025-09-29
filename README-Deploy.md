# 🚀 Panorama Ingeniería - PMT Tool Deployment Guide

## Overview
Complete PMT (Proyecto de Movilidad y Transporte) analysis tool with HCM-based capacity calculations, secure CRM, and export capabilities.

## Features
- **Capacity Analysis**: HCM methodology with LOS calculations
- **Secure CRM**: SQLite + bcrypt authentication with project metadata
- **Export System**: ZIP bulk export + PDF reports
- **Simulation**: CityFlow/UXsim integration (stub ready)

## Quick Deploy (Streamlit Cloud)

### Prerequisites
- Repository: `neokyhurtado-cmd/front`
- Branch: `main` (after PR merge) or `feat/sqlite-bcrypt-crm-v031`

### Deployment Steps
1. **Connect Repository**: Link `neokyhurtado-cmd/front` in Streamlit Community Cloud
2. **App Configuration**:
   - **Main file**: `panorama_local/app.py`
   - **Python version**: 3.10
   - **Requirements file**: `panorama_local/requirements.txt`
3. **Secrets** (Optional):
   ```toml
   CRM_DB_PATH = "panorama_local/data/panorama_crm.db"
   ```
4. **Deploy**: Click deploy and wait for build completion

### Post-Deploy Verification
- [ ] Registration/Login works
- [ ] JSON import → simulation → save with metadata
- [ ] Project listing with metadata display
- [ ] JSON export with sanitized filenames
- [ ] ZIP export (multiple projects)
- [ ] PDF export (single project reports)

## Local Development

### Setup
```bash
# Install dependencies
pip install -r panorama_local/requirements.txt

# Run tests
pytest tests/ -v

# Start app
streamlit run panorama_local/app.py
```

### Database
- **Local**: SQLite file-based (`panorama_local/data/panorama_crm.db`)
- **Production**: SQLite with persistent volume or external DB (future)

## Security Notes
- bcrypt password hashing implemented
- Session-based authentication
- Input validation for JSON imports
- Rate limiting considerations for production

## Version History
- **v0.3.1**: CRM SQLite+bcrypt, metadata & export ZIP/PDF
- **v0.2.0**: Basic capacity analysis + simulation stub
- **v0.1.0**: Initial PMT builder integration

## Roadmap
- IDECA/Catastro layers integration
- SDM (CIV, radicados) alerts
- Full CityFlow/UXsim model execution
- External database migration