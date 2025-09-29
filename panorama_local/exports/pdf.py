"""
PDF report generation for PMT projects.
"""

from io import BytesIO
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle
from reportlab.lib import colors
from reportlab.lib.units import inch
from typing import Dict, Any, Optional
import datetime


def render_project_pdf(project_id: str, summary: Optional[Dict[str, Any]] = None) -> bytes:
    """
    Generate a PDF report for a PMT project.

    Args:
        project_id: Project identifier
        summary: Project summary data (capacity analysis, metadata, etc.)

    Returns:
        PDF content as bytes
    """
    buf = BytesIO()

    # Create PDF document
    doc = SimpleDocTemplate(buf, pagesize=A4)
    styles = getSampleStyleSheet()

    # Custom styles
    title_style = ParagraphStyle(
        'CustomTitle',
        parent=styles['Heading1'],
        fontSize=18,
        spaceAfter=30,
        alignment=1  # Center
    )

    section_style = styles['Heading2']
    normal_style = styles['Normal']

    # Content elements
    elements = []

    # Title
    elements.append(Paragraph(f"Reporte Técnico PMT", title_style))
    elements.append(Paragraph(f"Proyecto: {project_id}", title_style))
    elements.append(Paragraph(f"Fecha de generación: {datetime.datetime.now().strftime('%Y-%m-%d %H:%M')}", normal_style))
    elements.append(Spacer(1, 20))

    # Project Summary Section
    elements.append(Paragraph("Resumen del Proyecto", section_style))
    elements.append(Spacer(1, 10))

    if summary:
        # Summary table
        summary_data = [
            ["Parámetro", "Valor"],
            ["Nombre", summary.get("name", "N/A")],
            ["Notas", summary.get("notes", "Sin notas")],
            ["Fecha de creación", summary.get("created_at", "N/A")],
        ]

        # Add capacity metrics if available
        capacity = summary.get("capacity", {})
        if capacity:
            summary_data.extend([
                ["Flujo (v)", f"{capacity.get('v', 'N/A')} veh/h"],
                ["Capacidad (c)", f"{capacity.get('c', 'N/A')} veh/h"],
                ["Ratio de saturación (x)", f"{capacity.get('x', 'N/A'):.2f}"],
                ["Demora (d)", f"{capacity.get('d', 'N/A'):.1f} s/veh"],
                ["Nivel de Servicio", capacity.get('los', 'N/A')]
            ])

        summary_table = Table(summary_data)
        summary_table.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'LEFT'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, 0), 12),
            ('BOTTOMPADDING', (0, 0), (-1, 0), 12),
            ('BACKGROUND', (0, 1), (-1, -1), colors.beige),
            ('GRID', (0, 0), (-1, -1), 1, colors.black)
        ]))

        elements.append(summary_table)
        elements.append(Spacer(1, 20))

        # Recommendations section
        recommendations = summary.get("recommendations", [])
        if recommendations:
            elements.append(Paragraph("Recomendaciones", section_style))
            elements.append(Spacer(1, 10))
            for rec in recommendations:
                elements.append(Paragraph(f"• {rec}", normal_style))
            elements.append(Spacer(1, 20))

    else:
        # Default summary for testing
        elements.append(Paragraph("Proyecto generado automáticamente para demostración.", normal_style))
        elements.append(Spacer(1, 10))

        default_data = [
            ["Parámetro", "Valor"],
            ["Flujo (v)", "1200 veh/h"],
            ["Capacidad (c)", "1800 veh/h"],
            ["Ratio de saturación (x)", "0.67"],
            ["Demora (d)", "5.2 s/veh"],
            ["Nivel de Servicio", "C"]
        ]

        default_table = Table(default_data)
        default_table.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'LEFT'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('BOTTOMPADDING', (0, 0), (-1, 0), 12),
            ('BACKGROUND', (0, 1), (-1, -1), colors.beige),
            ('GRID', (0, 0), (-1, -1), 1, colors.black)
        ]))

        elements.append(default_table)

    # Footer
    elements.append(Spacer(1, 40))
    elements.append(Paragraph("Generado por Panorama Ingeniería - Sistema PMT", normal_style))

    # Build PDF
    doc.build(elements)
    buf.seek(0)
    return buf.read()