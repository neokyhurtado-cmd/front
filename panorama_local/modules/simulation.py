"""Módulo de simulación (stubs) para panorama_local.

Contiene funciones que generan métricas sintéticas basadas en un proyecto PMT.
Estas funciones son stubs y están pensadas para ser reemplazadas por llamadas a
CityFlow, UXsim u otros motores reales en el futuro.
"""
import random
from typing import Dict


def run_cityflow_simulation(pmt_data: Dict) -> Dict:
    """Simulación stub que genera métricas plausibles a partir de pmt_data.

    pmt_data expected keys: plan, markers (list), rotulo, center
    """
    markers = pmt_data.get("markers") or []
    n = max(0, len(markers))

    # Base values influenced by number of signals/markers
    avg_travel_time = round(60 + n * random.uniform(0.5, 2.0), 2)  # seconds
    avg_queue_length = round(max(0.0, n * random.uniform(1.0, 4.0) - random.uniform(0, 3)), 2)
    avg_speed = round(max(5.0, 40 - n * random.uniform(0.5, 1.5)), 2)  # km/h

    # Slightly better numbers for Pro users (simulating better parameters)
    plan = (pmt_data.get("plan") or "Free").lower()
    if plan == "pro":
        avg_travel_time *= 0.95
        avg_queue_length *= 0.9
        avg_speed *= 1.05

    return {
        "engine": "cityflow",
        "avg_travel_time": round(avg_travel_time, 2),
        "avg_queue_length": round(avg_queue_length, 2),
        "avg_speed": round(avg_speed, 2),
    }


def run_uxsim_simulation(pmt_data: Dict) -> Dict:
    """Otro stub con parámetros distintos para el motor 'uxsim'."""
    markers = pmt_data.get("markers") or []
    n = max(0, len(markers))

    avg_travel_time = round(55 + n * random.uniform(0.6, 1.8), 2)
    avg_queue_length = round(max(0.0, n * random.uniform(0.8, 3.5) - random.uniform(0, 2)), 2)
    avg_speed = round(max(5.0, 42 - n * random.uniform(0.4, 1.3)), 2)

    plan = (pmt_data.get("plan") or "Free").lower()
    if plan == "pro":
        avg_travel_time *= 0.96
        avg_queue_length *= 0.92
        avg_speed *= 1.04

    return {
        "engine": "uxsim",
        "avg_travel_time": round(avg_travel_time, 2),
        "avg_queue_length": round(avg_queue_length, 2),
        "avg_speed": round(avg_speed, 2),
    }


def run_simulation(pmt_data: Dict, engine: str = "cityflow") -> Dict:
    """Dispatcher para ejecutar la simulación con el motor seleccionado.

    engine: 'cityflow' or 'uxsim'
    """
    engine = (engine or "cityflow").lower()
    if engine == "uxsim":
        return run_uxsim_simulation(pmt_data)
    # default
    return run_cityflow_simulation(pmt_data)
