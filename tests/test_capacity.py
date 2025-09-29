"""
Tests for capacity analysis calculations.
"""

import pytest
from panorama_local.capacity.core import compute_capacity, compute_adjusted_capacity, get_capacity_recommendations


def test_compute_capacity_basic():
    """Test basic capacity calculation."""
    result = compute_capacity(v=1200, c=1800, s=1900)

    assert result["x"] == 1200 / 1800  # 0.666...
    assert result["los"] == "C"  # Based on delay thresholds
    assert "warning" not in result["flags"]  # x < 0.85
    assert "critical" not in result["flags"]  # x < 1.0


def test_compute_capacity_critical():
    """Test capacity calculation with critical saturation."""
    result = compute_capacity(v=2000, c=1800, s=1900)

    assert result["x"] > 1.0
    assert "critical" in result["flags"]
    assert result["d"] > 0  # Should have delay


def test_compute_capacity_signalized():
    """Test capacity calculation for signalized intersection."""
    result = compute_capacity(v=1200, c=1800, s=1900, g=30, C=120)

    assert "x" in result
    assert "d" in result
    assert result["d"] > 0  # Signalized delay


def test_compute_adjusted_capacity():
    """Test adjusted capacity calculation with factors."""
    base_s = 1900

    # Standard conditions
    adjusted = compute_adjusted_capacity(base_s, width_m=3.5, has_sidewalk=True, friction_factor=1.0, slope_pct=0)
    assert adjusted == base_s

    # Narrow lane
    adjusted_narrow = compute_adjusted_capacity(base_s, width_m=3.0)
    assert adjusted_narrow < base_s

    # Uphill
    adjusted_slope = compute_adjusted_capacity(base_s, slope_pct=5)
    assert adjusted_slope < base_s


def test_get_capacity_recommendations():
    """Test generation of capacity recommendations."""
    # Normal conditions
    normal = {"x": 0.6, "los": "B", "flags": []}
    recs = get_capacity_recommendations(normal)
    assert len(recs) == 0  # No recommendations for good conditions

    # Critical conditions
    critical = {"x": 1.2, "los": "F", "flags": ["critical"]}
    recs = get_capacity_recommendations(critical)
    assert len(recs) > 0
    assert any("capacidad insuficiente" in r.lower() for r in recs)