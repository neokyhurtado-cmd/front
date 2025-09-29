"""
Capacity analysis calculations for PMT projects.
Based on HCM methodology with operational simplifications.
"""

from typing import Dict, Any, Tuple
import math


def compute_capacity(v: float, c: float, s: float = 1900, g: float = None, C: float = None) -> Dict[str, Any]:
    """
    Compute capacity metrics for a traffic stream.

    Args:
        v: Flow rate (veh/h)
        c: Capacity (veh/h)
        s: Saturation flow (veh/h/lane), default 1900
        g: Effective green time (s), optional for signalized intersections
        C: Cycle length (s), optional for signalized intersections

    Returns:
        Dict with x (saturation ratio), d (delay), los (level of service), flags
    """
    # Basic saturation ratio
    x = v / c if c > 0 else float('inf')

    # Delay calculation
    d = 0.0
    if g is not None and C is not None and C > 0:
        # Signalized intersection delay (simplified HCM)
        y = v / s if s > 0 else 0  # Flow ratio
        g_C = g / C
        if y > 0:
            d = 0.5 * C * (1 - g_C)**2 / (1 - min(1.0, x))
        else:
            d = 0.0
    else:
        # Unsignalized or free flow - HCM-inspired delay model
        # Base delay increases with saturation ratio
        d = 5 + 25 * x  # More conservative delay estimation

    # Level of Service based on delay thresholds
    if d <= 10:
        los = "A"
    elif d <= 20:
        los = "B"
    elif d <= 35:
        los = "C"
    elif d <= 55:
        los = "D"
    elif d <= 80:
        los = "E"
    else:
        los = "F"

    # Flags for warnings
    flags = []
    if x > 0.85:
        flags.append("warning")
    if x > 1.0:
        flags.append("critical")

    return {
        "x": x,
        "d": d,
        "los": los,
        "flags": flags,
        "v": v,
        "c": c,
        "s": s
    }


def compute_adjusted_capacity(base_s: float = 1900, width_m: float = 3.5,
                            has_sidewalk: bool = True, friction_factor: float = 1.0,
                            slope_pct: float = 0.0) -> float:
    """
    Compute adjusted saturation flow rate with geometric and operational factors.

    Args:
        base_s: Base saturation flow (veh/h/lane)
        width_m: Lane width (m)
        has_sidewalk: Whether sidewalk is present
        friction_factor: Lateral friction factor (0.8-1.0)
        slope_pct: Slope percentage

    Returns:
        Adjusted saturation flow rate
    """
    # Width adjustment factor
    if width_m >= 3.5:
        f_width = 1.0
    elif width_m >= 3.0:
        f_width = 0.95
    else:
        f_width = 0.90

    # Sidewalk adjustment (if narrow, reduces capacity)
    f_sidewalk = 0.95 if has_sidewalk and width_m < 3.5 else 1.0

    # Slope adjustment
    if slope_pct > 3:
        f_slope = 0.9
    elif slope_pct > 1:
        f_slope = 0.95
    else:
        f_slope = 1.0

    # Combined adjustment
    s_adjusted = base_s * f_width * f_sidewalk * friction_factor * f_slope

    return s_adjusted


def get_capacity_recommendations(results: Dict[str, Any]) -> list:
    """
    Generate recommendations based on capacity analysis results.
    """
    recommendations = []

    x = results["x"]
    los = results["los"]

    if x > 1.0:
        recommendations.append("🚨 Capacidad insuficiente - considerar ampliación de carriles")
    elif x > 0.85:
        recommendations.append("⚠️ Cercano a capacidad - monitorear flujo")

    if los in ["E", "F"]:
        recommendations.append("📉 Nivel de servicio deficiente - revisar geometría y control")

    if "critical" in results["flags"]:
        recommendations.append("🔴 Sobresaturación detectada - intervención inmediata requerida")

    return recommendations