"""
Normalizador de Aforos - Panorama Ingeniería PMT

Este módulo normaliza datos de aforos (conteos de tráfico) desde diferentes fuentes:
- Google Apps Script (Drive)
- Archivos CSV/XLSX locales
- APIs de tráfico

Funcionalidades:
- Equivalencias vehiculares
- Cálculo de VPH (Vehículos por Hora)
- Factor PHF (Peak Hour Factor)
- Distribución HMD (Hora del Máximo Diario)
- Metadatos y validaciones
"""

import pandas as pd
import numpy as np
from typing import Dict, List, Optional, Tuple, Any
from datetime import datetime, time
import logging
import json

logger = logging.getLogger(__name__)

class AforosNormalizer:
    """
    Normalizador de datos de aforos para PMT
    """

    # Equivalencias vehiculares estándar (ajustables por ciudad/entidad)
    EQUIVALENCIAS_DEFAULT = {
        'auto': 1.0,
        'camioneta': 1.0,
        'camion': 2.0,
        'bus': 2.5,
        'moto': 0.5,
        'bicicleta': 0.2,
        'peaton': 0.0
    }

    def __init__(self, equivalencias: Optional[Dict[str, float]] = None):
        """
        Inicializar normalizador

        Args:
            equivalencias: Dict con equivalencias vehiculares personalizadas
        """
        self.equivalencias = equivalencias or self.EQUIVALENCIAS_DEFAULT.copy()
        self.logger = logging.getLogger(f"{__name__}.{self.__class__.__name__}")

    def normalize_from_apps_script(self, script_url: str) -> Dict[str, Any]:
        """
        Normalizar datos desde Google Apps Script

        Args:
            script_url: URL del script de Google Apps Script

        Returns:
            Dict con datos normalizados y metadatos
        """
        # TODO: Implementar conexión a Apps Script
        # Por ahora, placeholder
        self.logger.info(f"Conectando a Apps Script: {script_url}")
        raise NotImplementedError("Conexión a Apps Script no implementada")

    def normalize_from_dataframe(self, df: pd.DataFrame,
                               time_col: str = 'hora',
                               vehicle_cols: Optional[List[str]] = None) -> Dict[str, Any]:
        """
        Normalizar datos desde DataFrame

        Args:
            df: DataFrame con datos de aforos
            time_col: Columna con información temporal
            vehicle_cols: Columnas con tipos de vehículos

        Returns:
            Dict con datos normalizados y metadatos
        """
        self.logger.info(f"Normalizando DataFrame con {len(df)} filas")

        # Detectar columnas automáticamente si no se especifican
        if vehicle_cols is None:
            vehicle_cols = self._detect_vehicle_columns(df)

        # Validar estructura básica
        self._validate_dataframe(df, time_col, vehicle_cols)

        # Calcular equivalencias
        df_normalized = self._calculate_equivalences(df, vehicle_cols)

        # Calcular VPH por hora
        vph_by_hour = self._calculate_vph(df_normalized, time_col)

        # Calcular PHF (Peak Hour Factor)
        phf = self._calculate_phf(vph_by_hour)

        # Calcular HMD (Hora del Máximo Diario)
        hmd = self._calculate_hmd(vph_by_hour)

        # Generar metadatos
        metadata = self._generate_metadata(df, vehicle_cols, phf, hmd)

        # Resultado final
        result = {
            'data_normalized': df_normalized.to_dict('records'),
            'vph_by_hour': vph_by_hour.to_dict(),
            'phf': phf,
            'hmd': hmd,
            'metadata': metadata,
            'summary': self._generate_summary(df_normalized, vph_by_hour, phf, hmd)
        }

        self.logger.info(f"Normalización completada. PHF: {phf:.3f}, HMD: {hmd}")
        return result

    def _detect_vehicle_columns(self, df: pd.DataFrame) -> List[str]:
        """Detectar automáticamente columnas de vehículos"""
        # TODO: Implementar detección automática
        # Por ahora, asumir columnas comunes
        common_vehicle_cols = ['auto', 'camioneta', 'camion', 'bus', 'moto', 'bicicleta', 'peaton']
        detected = [col for col in df.columns if col.lower() in common_vehicle_cols]
        return detected or list(df.select_dtypes(include=[np.number]).columns)

    def _validate_dataframe(self, df: pd.DataFrame, time_col: str, vehicle_cols: List[str]):
        """Validar estructura del DataFrame"""
        if time_col not in df.columns:
            raise ValueError(f"Columna temporal '{time_col}' no encontrada")

        missing_vehicle_cols = [col for col in vehicle_cols if col not in df.columns]
        if missing_vehicle_cols:
            raise ValueError(f"Columnas de vehículos no encontradas: {missing_vehicle_cols}")

    def _calculate_equivalences(self, df: pd.DataFrame, vehicle_cols: List[str]) -> pd.DataFrame:
        """Calcular equivalencias vehiculares"""
        df_copy = df.copy()

        # Aplicar equivalencias
        for col in vehicle_cols:
            if col in self.equivalencias:
                equiv_col = f"{col}_equiv"
                df_copy[equiv_col] = df_copy[col] * self.equivalencias[col]

        # Calcular total equivalente
        equiv_cols = [f"{col}_equiv" for col in vehicle_cols if f"{col}_equiv" in df_copy.columns]
        if equiv_cols:
            df_copy['total_equiv'] = df_copy[equiv_cols].sum(axis=1)

        return df_copy

    def _calculate_vph(self, df: pd.DataFrame, time_col: str) -> pd.Series:
        """Calcular Vehículos Por Hora"""
        # TODO: Implementar cálculo VPH basado en intervalos temporales
        # Placeholder: asumir datos por hora
        if 'total_equiv' in df.columns:
            return df.groupby(time_col)['total_equiv'].sum()
        else:
            # Fallback: suma de todas las columnas numéricas por hora
            numeric_cols = df.select_dtypes(include=[np.number]).columns
            return df.groupby(time_col)[numeric_cols].sum().sum(axis=1)

    def _calculate_phf(self, vph_by_hour: pd.Series) -> float:
        """Calcular Peak Hour Factor (PHF)"""
        if len(vph_by_hour) == 0:
            return 1.0

        # PHF = (flujo máximo por hora) / (flujo máximo por 15 minutos * 4)
        # TODO: Implementar cálculo real con intervalos de 15 minutos
        # Placeholder: simplificación
        max_hourly = vph_by_hour.max()
        return max_hourly / (max_hourly * 0.85) if max_hourly > 0 else 1.0

    def _calculate_hmd(self, vph_by_hour: pd.Series) -> str:
        """Calcular Hora del Máximo Diario (HMD)"""
        if len(vph_by_hour) == 0:
            return "00:00"

        # Encontrar hora con máximo flujo
        max_hour = vph_by_hour.idxmax()
        return str(max_hour)

    def _generate_metadata(self, df: pd.DataFrame, vehicle_cols: List[str],
                          phf: float, hmd: str) -> Dict[str, Any]:
        """Generar metadatos del proceso de normalización"""
        return {
            'timestamp': datetime.now().isoformat(),
            'vehicle_types': vehicle_cols,
            'equivalencias_used': self.equivalencias,
            'total_records': len(df),
            'date_range': {
                'start': df.index.min().isoformat() if isinstance(df.index, pd.DatetimeIndex) else None,
                'end': df.index.max().isoformat() if isinstance(df.index, pd.DatetimeIndex) else None
            },
            'phf': phf,
            'hmd': hmd,
            'normalizer_version': '1.0.0'
        }

    def _generate_summary(self, df_normalized: pd.DataFrame, vph_by_hour: pd.Series,
                         phf: float, hmd: str) -> Dict[str, Any]:
        """Generar resumen ejecutivo"""
        return {
            'total_vehicles': df_normalized['total_equiv'].sum() if 'total_equiv' in df_normalized.columns else 0,
            'peak_hour_volume': vph_by_hour.max(),
            'peak_hour_factor': phf,
            'hora_maximo_diario': hmd,
            'hours_analyzed': len(vph_by_hour),
            'avg_hourly_volume': vph_by_hour.mean()
        }


# Funciones de utilidad para integración con Streamlit
def load_aforos_from_csv(file_path: str) -> pd.DataFrame:
    """Cargar aforos desde CSV"""
    return pd.read_csv(file_path)


def load_aforos_from_excel(file_path: str, sheet_name: str = 0) -> pd.DataFrame:
    """Cargar aforos desde Excel"""
    return pd.read_excel(file_path, sheet_name=sheet_name)


def save_normalized_aforos(data: Dict[str, Any], output_path: str):
    """Guardar datos normalizados en formato JSON"""
    with open(output_path, 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=2, ensure_ascii=False)