"""
Módulo separado para AforosVerifier - extraído de 07_Verificador.py
para facilitar testing e importaciones
"""

import pandas as pd
from typing import List, Dict, Any, Tuple
import logging

logger = logging.getLogger(__name__)

class AforosVerifier:
    """
    Verificador de calidad de datos de aforos
    NO expone valores numéricos - solo hallazgos y correcciones
    """

    def __init__(self):
        self.logger = logging.getLogger(f"{__name__}.{self.__class__.__name__}")

    def verify_data(self, normalized_data: Dict[str, Any]) -> Dict[str, Any]:
        """
        Verificar calidad de datos normalizados

        Args:
            normalized_data: Datos normalizados del normalizador

        Returns:
            Dict con hallazgos, severidad y recomendaciones
        """
        findings = {
            'critical': [],    # Bloquean generación PMT
            'warning': [],     # Requieren atención
            'info': []         # Mejoras opcionales
        }

        # Verificar estructura básica
        self._check_basic_structure(normalized_data, findings)

        # Verificar completitud temporal
        self._check_temporal_completeness(normalized_data, findings)

        # Verificar consistencia de patrones
        self._check_pattern_consistency(normalized_data, findings)

        # Verificar calidad de PHF
        self._check_phf_quality(normalized_data, findings)

        # Verificar distribución por tipo de vehículo
        self._check_vehicle_distribution(normalized_data, findings)

        # Calcular score general
        score = self._calculate_quality_score(findings)

        return {
            'findings': findings,
            'quality_score': score,
            'can_generate_pmt': len(findings['critical']) == 0,
            'recommendations': self._generate_recommendations(findings)
        }

    def _check_basic_structure(self, data: Dict[str, Any], findings: Dict[str, List[str]]):
        """Verificar estructura básica de datos"""
        if 'data_normalized' not in data:
            findings['critical'].append("Faltan datos normalizados básicos")
            return

        df = pd.DataFrame(data['data_normalized'])

        # Verificar columnas mínimas
        required_cols = ['hora']  # Al menos hora
        missing_cols = [col for col in required_cols if col not in df.columns]
        if missing_cols:
            findings['critical'].append(f"Faltan columnas requeridas: {', '.join(missing_cols)}")

        # Verificar que hay datos
        if len(df) == 0:
            findings['critical'].append("No hay datos para analizar")
            return

        # Verificar tipos de vehículos
        vehicle_cols = [col for col in df.columns if col.endswith('_equiv') or col in ['auto', 'camion', 'bus', 'moto']]
        if len(vehicle_cols) == 0:
            findings['warning'].append("No se detectaron columnas de tipos de vehículos")

    def _check_temporal_completeness(self, data: Dict[str, Any], findings: Dict[str, List[str]]):
        """Verificar completitud temporal"""
        if 'vph_by_hour' not in data:
            findings['warning'].append("No se pudo calcular distribución horaria")
            return

        vph_data = data['vph_by_hour']
        hours_count = len(vph_data)

        # Verificar cobertura horaria mínima
        if hours_count < 12:
            findings['warning'].append("Cobertura horaria insuficiente (menos de 12 horas)")

        if hours_count < 6:
            findings['critical'].append("Cobertura horaria crítica (menos de 6 horas)")

        # Verificar horas pico
        peak_hours = [7, 8, 9, 17, 18, 19]  # Horas pico típicas
        peak_coverage = 0
        for hour in peak_hours:
            # Buscar tanto formato HH como HH:MM
            hour_str = str(hour)
            hour_mm = f"{hour:02d}:00"
            if hour_str in vph_data or hour_mm in vph_data:
                peak_coverage += 1

        if peak_coverage < 3:
            findings['warning'].append("Cobertura insuficiente de horas pico")

    def _check_pattern_consistency(self, data: Dict[str, Any], findings: Dict[str, List[str]]):
        """Verificar consistencia de patrones de tráfico"""
        if 'vph_by_hour' not in data:
            return

        vph_data = data['vph_by_hour']

        # Verificar variabilidad razonable
        values = list(vph_data.values())
        if len(values) > 1:
            mean_val = sum(values) / len(values)
            if mean_val > 0:
                # Calcular coeficiente de variación
                variance = sum((x - mean_val) ** 2 for x in values) / len(values)
                std_dev = variance ** 0.5
                cv = std_dev / mean_val if mean_val > 0 else 0

                if cv > 2.0:  # Variabilidad muy alta
                    findings['warning'].append("Alta variabilidad en los patrones horarios - revisar consistencia")

                if cv < 0.1:  # Variabilidad muy baja (datos sospechosos)
                    findings['info'].append("Baja variabilidad horaria - verificar si los datos representan un día típico")

    def _check_phf_quality(self, data: Dict[str, Any], findings: Dict[str, List[str]]):
        """Verificar calidad del Peak Hour Factor"""
        if 'phf' not in data:
            findings['warning'].append("No se pudo calcular el Factor de Hora Pico (PHF)")
            return

        phf = data['phf']

        # PHF típico debería estar entre 0.7 y 1.0
        if phf < 0.6:
            findings['warning'].append("PHF anormalmente bajo - revisar metodología de cálculo")

        if phf > 1.1:
            findings['warning'].append("PHF mayor a 1.0 - posible error en datos de entrada")

        if phf < 0.3:
            findings['critical'].append("PHF críticamente bajo - datos no confiables")

    def _check_vehicle_distribution(self, data: Dict[str, Any], findings: Dict[str, List[str]]):
        """Verificar distribución por tipo de vehículo"""
        if 'data_normalized' not in data:
            return

        df = pd.DataFrame(data['data_normalized'])

        # Buscar columnas de equivalencias
        equiv_cols = [col for col in df.columns if col.endswith('_equiv')]

        if not equiv_cols:
            findings['info'].append("No se encontraron equivalencias vehiculares - usando datos brutos")
            return

        # Verificar que las equivalencias sean razonables
        for col in equiv_cols:
            values = df[col].dropna()
            if len(values) > 0:
                negative_values = (values < 0).sum()
                if negative_values > 0:
                    findings['critical'].append(f"Valores negativos en equivalencias de {col.replace('_equiv', '')}")

                # Verificar valores extremos
                mean_val = values.mean()
                if mean_val > 0:
                    extreme_values = (values > mean_val * 10).sum()
                    if extreme_values > len(values) * 0.1:  # Más del 10% son extremos
                        findings['warning'].append(f"Valores extremos detectados en {col.replace('_equiv', '')}")

    def _calculate_quality_score(self, findings: Dict[str, List[str]]) -> float:
        """Calcular score de calidad (0-100)"""
        base_score = 100

        # Penalizaciones
        critical_penalty = len(findings['critical']) * 30  # -30 por crítico
        warning_penalty = len(findings['warning']) * 10    # -10 por warning

        score = max(0, base_score - critical_penalty - warning_penalty)
        return score

    def _generate_recommendations(self, findings: Dict[str, List[str]]) -> List[str]:
        """Generar recomendaciones basadas en hallazgos"""
        recommendations = []

        if findings['critical']:
            recommendations.append("🔴 Resolver problemas críticos antes de generar PMT")

        if findings['warning']:
            recommendations.append("🟡 Revisar advertencias para mejorar calidad")

        if not findings['critical'] and not findings['warning']:
            recommendations.append("✅ Datos listos para generación de PMT")

        # Recomendaciones específicas
        if any('hora' in f.lower() for f in findings['critical'] + findings['warning']):
            recommendations.append("📅 Ampliar cobertura horaria, especialmente horas pico")

        if any('phf' in f.lower() for f in findings['critical'] + findings['warning']):
            recommendations.append("📊 Verificar metodología de cálculo del PHF")

        if any('veh' in f.lower() for f in findings['critical'] + findings['warning']):
            recommendations.append("🚗 Revisar clasificación y equivalencias de vehículos")

        return recommendations