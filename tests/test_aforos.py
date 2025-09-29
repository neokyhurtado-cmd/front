"""
Tests básicos para el normalizador de aforos
"""

import pytest
import pandas as pd
from panorama_local.aforos.normalize import AforosNormalizer


class TestAforosNormalizer:

    def test_initialization(self):
        """Test inicialización con equivalencias por defecto"""
        normalizer = AforosNormalizer()
        assert normalizer.equivalencias['auto'] == 1.0
        assert normalizer.equivalencias['camion'] == 2.0
        assert normalizer.equivalencias['moto'] == 0.5

    def test_initialization_custom_equivalencias(self):
        """Test inicialización con equivalencias personalizadas"""
        custom_equiv = {'auto': 1.5, 'camion': 3.0}
        normalizer = AforosNormalizer(equivalencias=custom_equiv)
        assert normalizer.equivalencias['auto'] == 1.5
        assert normalizer.equivalencias['camion'] == 3.0

    def test_calculate_equivalences(self):
        """Test cálculo de equivalencias vehiculares"""
        normalizer = AforosNormalizer()

        # DataFrame de prueba
        df = pd.DataFrame({
            'hora': ['08:00', '09:00'],
            'auto': [100, 120],
            'camion': [5, 8],
            'moto': [20, 25]
        })

        result = normalizer._calculate_equivalences(df, ['auto', 'camion', 'moto'])

        # Verificar columnas de equivalencias
        assert 'auto_equiv' in result.columns
        assert 'camion_equiv' in result.columns
        assert 'moto_equiv' in result.columns
        assert 'total_equiv' in result.columns

        # Verificar cálculos
        assert result.loc[0, 'auto_equiv'] == 100 * 1.0  # auto = 1.0
        assert result.loc[0, 'camion_equiv'] == 5 * 2.0   # camion = 2.0
        assert result.loc[0, 'moto_equiv'] == 20 * 0.5    # moto = 0.5
        assert result.loc[0, 'total_equiv'] == (100 + 10 + 10)  # 100 + 10 + 10

    def test_calculate_vph(self):
        """Test cálculo de vehículos por hora"""
        normalizer = AforosNormalizer()

        df = pd.DataFrame({
            'hora': ['08:00', '08:00', '09:00', '09:00'],
            'total_equiv': [100, 120, 150, 180]
        })

        vph = normalizer._calculate_vph(df, 'hora')

        # Verificar que agrupa correctamente por hora
        assert len(vph) == 2  # 2 horas distintas
        assert vph['08:00'] == 220  # 100 + 120
        assert vph['09:00'] == 330  # 150 + 180

    def test_calculate_phf(self):
        """Test cálculo del Peak Hour Factor"""
        normalizer = AforosNormalizer()

        # VPH con pico claro
        vph = pd.Series({
            '06:00': 50,
            '07:00': 100,
            '08:00': 200,  # Pico
            '09:00': 150,
            '10:00': 80
        })

        phf = normalizer._calculate_phf(vph)

        # PHF debería estar en rango típico (0.7-0.95)
        assert 0.7 <= phf <= 0.95

    def test_calculate_hmd(self):
        """Test cálculo de Hora del Máximo Diario"""
        normalizer = AforosNormalizer()

        vph = pd.Series({
            '06:00': 50,
            '08:00': 200,  # Pico
            '10:00': 80
        })

        hmd = normalizer._calculate_hmd(vph)
        assert hmd == '08:00'

    def test_normalize_from_dataframe_basic(self):
        """Test normalización completa desde DataFrame"""
        normalizer = AforosNormalizer()

        # DataFrame simple
        df = pd.DataFrame({
            'hora': ['08:00', '09:00'],
            'auto': [100, 120],
            'camion': [5, 8]
        })

        result = normalizer.normalize_from_dataframe(df, 'hora', ['auto', 'camion'])

        # Verificar estructura del resultado
        required_keys = ['data_normalized', 'vph_by_hour', 'phf', 'hmd', 'metadata', 'summary']
        for key in required_keys:
            assert key in result

        # Verificar que hay datos normalizados
        assert len(result['data_normalized']) == 2

        # Verificar metadatos
        assert 'timestamp' in result['metadata']
        assert result['metadata']['vehicle_types'] == ['auto', 'camion']

        # Verificar resumen
        assert 'total_vehicles' in result['summary']
        assert 'peak_hour_volume' in result['summary']

    def test_validation_fails_missing_columns(self):
        """Test que falla validación con columnas faltantes"""
        normalizer = AforosNormalizer()

        df = pd.DataFrame({
            'hora': ['08:00'],
            'auto': [100]
        })

        with pytest.raises(ValueError, match="Columnas de vehículos no encontradas"):
            normalizer.normalize_from_dataframe(df, 'hora', ['camion'])

    def test_validation_fails_missing_time_column(self):
        """Test que falla validación con columna temporal faltante"""
        normalizer = AforosNormalizer()

        df = pd.DataFrame({
            'auto': [100]
        })

        with pytest.raises(ValueError, match="Columna temporal.*no encontrada"):
            normalizer.normalize_from_dataframe(df, 'hora', ['auto'])