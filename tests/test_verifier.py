"""
Tests básicos para el verificador de aforos
"""

import pytest
from panorama_local.aforos.verifier import AforosVerifier


class TestAforosVerifier:

    def test_verify_perfect_data(self):
        """Test verificación con datos perfectos"""
        verifier = AforosVerifier()

        # Datos normalizados "perfectos" - 16 horas de cobertura
        perfect_data = {
            'data_normalized': [
                {'hora': '06:00', 'total_equiv': 50},
                {'hora': '07:00', 'total_equiv': 100},
                {'hora': '08:00', 'total_equiv': 200},  # Pico mañana
                {'hora': '09:00', 'total_equiv': 180},
                {'hora': '10:00', 'total_equiv': 120},
                {'hora': '11:00', 'total_equiv': 90},
                {'hora': '12:00', 'total_equiv': 80},
                {'hora': '13:00', 'total_equiv': 85},
                {'hora': '14:00', 'total_equiv': 95},
                {'hora': '15:00', 'total_equiv': 110},
                {'hora': '16:00', 'total_equiv': 130},
                {'hora': '17:00', 'total_equiv': 190},  # Pico tarde
                {'hora': '18:00', 'total_equiv': 170},
                {'hora': '19:00', 'total_equiv': 140},
                {'hora': '20:00', 'total_equiv': 100},
                {'hora': '21:00', 'total_equiv': 70},
            ],
            'vph_by_hour': {
                '06:00': 50, '07:00': 100, '08:00': 200, '09:00': 180, '10:00': 120,
                '11:00': 90, '12:00': 80, '13:00': 85, '14:00': 95, '15:00': 110,
                '16:00': 130, '17:00': 190, '18:00': 170, '19:00': 140, '20:00': 100, '21:00': 70
            },
            'phf': 0.85,
            'hmd': '08:00',
            'metadata': {
                'total_records': 16,
                'vehicle_types': ['auto', 'camion']
            }
        }

        result = verifier.verify_data(perfect_data)

        # Debería pasar todas las verificaciones
        assert result['can_generate_pmt'] is True
        assert result['quality_score'] >= 80  # Score alto para datos buenos
        assert result['quality_score'] >= 80  # Score alto
        assert len(result['findings']['critical']) == 0
        assert len(result['findings']['warning']) == 0

    def test_verify_data_missing_hours(self):
        """Test verificación con cobertura horaria insuficiente"""
        verifier = AforosVerifier()

        # Solo 2 horas de datos
        poor_data = {
            'data_normalized': [
                {'hora': '08:00', 'total_equiv': 100},
                {'hora': '09:00', 'total_equiv': 120},
            ],
            'vph_by_hour': {'08:00': 100, '09:00': 120},
            'phf': 0.9,
            'hmd': '09:00',
            'metadata': {
                'total_records': 2,
                'vehicle_types': ['auto']
            }
        }

        result = verifier.verify_data(poor_data)

        # Debería tener warnings por cobertura insuficiente
        assert 'warning' in result['findings']
        assert any('horaria insuficiente' in finding.lower()
                  for finding in result['findings']['warning'])

    def test_verify_data_bad_phf(self):
        """Test verificación con PHF problemático"""
        verifier = AforosVerifier()

        data_with_bad_phf = {
            'data_normalized': [
                {'hora': '06:00', 'total_equiv': 50},
                {'hora': '07:00', 'total_equiv': 100},
                {'hora': '08:00', 'total_equiv': 200},
                {'hora': '09:00', 'total_equiv': 180},
                {'hora': '10:00', 'total_equiv': 120},
                {'hora': '11:00', 'total_equiv': 90},
                {'hora': '12:00', 'total_equiv': 80},
            ],
            'vph_by_hour': {
                '06:00': 50, '07:00': 100, '08:00': 200,
                '09:00': 180, '10:00': 120, '11:00': 90, '12:00': 80
            },
            'phf': 0.25,  # PHF críticamente bajo
            'hmd': '08:00',
            'metadata': {
                'total_records': 7,
                'vehicle_types': ['auto']
            }
        }

        result = verifier.verify_data(data_with_bad_phf)

        # Debería tener problemas críticos por PHF
        assert result['can_generate_pmt'] is False
        assert any('PHF críticamente bajo' in finding
                  for finding in result['findings']['critical'])

    def test_verify_empty_data(self):
        """Test verificación con datos vacíos"""
        verifier = AforosVerifier()

        empty_data = {
            'data_normalized': [],
            'vph_by_hour': {},
            'phf': 1.0,
            'hmd': '00:00',
            'metadata': {
                'total_records': 0,
                'vehicle_types': []
            }
        }

        result = verifier.verify_data(empty_data)

        # Debería fallar críticamente
        assert result['can_generate_pmt'] is False
        assert len(result['findings']['critical']) > 0

    def test_quality_score_calculation(self):
        """Test cálculo del score de calidad"""
        verifier = AforosVerifier()

        # Datos con algunos problemas
        data_with_issues = {
            'data_normalized': [
                {'hora': '08:00', 'total_equiv': 100},
                {'hora': '09:00', 'total_equiv': 120},
            ],
            'vph_by_hour': {'08:00': 100, '09:00': 120},
            'phf': 0.9,
            'hmd': '09:00',
            'metadata': {
                'total_records': 2,
                'vehicle_types': ['auto']
            }
        }

        result = verifier.verify_data(data_with_issues)

        # Score debería estar entre 0 y 100
        assert 0 <= result['quality_score'] <= 100

        # Debería tener algunas recomendaciones
        assert len(result['recommendations']) > 0

    def test_recommendations_generation(self):
        """Test generación de recomendaciones"""
        verifier = AforosVerifier()

        # Datos con problemas críticos
        critical_data = {
            'data_normalized': [],
            'vph_by_hour': {},
            'phf': 0.2,
            'hmd': '00:00',
            'metadata': {
                'total_records': 0,
                'vehicle_types': []
            }
        }

        result = verifier.verify_data(critical_data)

        recommendations = result['recommendations']

        # Debería incluir recomendaciones para resolver problemas
        assert any('resolver problemas críticos' in rec.lower() for rec in recommendations)
        assert any('ampliar cobertura horaria' in rec.lower() for rec in recommendations)
        assert any('verificar metodología de cálculo del phf' in rec.lower() for rec in recommendations)