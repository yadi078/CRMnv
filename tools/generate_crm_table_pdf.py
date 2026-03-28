from reportlab.lib import colors
from reportlab.lib.pagesizes import A4, landscape
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import Paragraph, SimpleDocTemplate, Spacer, Table, TableStyle


def build_pdf(output_path: str) -> None:
    doc = SimpleDocTemplate(
        output_path,
        pagesize=landscape(A4),
        leftMargin=1.5 * cm,
        rightMargin=1.5 * cm,
        topMargin=1.2 * cm,
        bottomMargin=1.2 * cm,
    )

    styles = getSampleStyleSheet()
    title_style = styles["Heading2"]
    title_style.fontName = "Helvetica-Bold"
    title_style.spaceAfter = 8

    note_style = ParagraphStyle(
        "note",
        parent=styles["BodyText"],
        fontName="Helvetica",
        fontSize=10,
        leading=13,
    )

    cell_style = ParagraphStyle(
        "cell",
        parent=styles["BodyText"],
        fontName="Helvetica",
        fontSize=9,
        leading=12,
    )

    header_style = ParagraphStyle(
        "header",
        parent=styles["BodyText"],
        fontName="Helvetica-Bold",
        fontSize=9,
        leading=11,
        textColor=colors.black,
    )

    data = [
        [
            Paragraph("Indicador de calidad", header_style),
            Paragraph("Escenario anterior<br/>(Excel / proceso manual)", header_style),
            Paragraph("Escenario actual<br/>(CRM)", header_style),
            Paragraph("Evidencia tecnica en CRM", header_style),
            Paragraph("Impacto / Mejora", header_style),
        ],
        [
            Paragraph("<b>Integridad referencial (IIR)</b>", cell_style),
            Paragraph("72% (existencia de registros sin vinculacion)", cell_style),
            Paragraph("<b>100%</b> (vinculos integros entre entidades)", cell_style),
            Paragraph(
                "Llaves foraneas entre <b>contacts</b> y <b>follow_ups</b>; "
                "validacion de relaciones en API",
                cell_style,
            ),
            Paragraph("<b>+28%</b> de precision estructural", cell_style),
        ],
        [
            Paragraph("<b>Redundancia (duplicados)</b>", cell_style),
            Paragraph("Alta (multiples registros equivalentes)", cell_style),
            Paragraph("<b>0%</b> de duplicados criticos", cell_style),
            Paragraph(
                "Restricciones <b>UNIQUE</b> en campos clave y validaciones "
                "de unicidad en la capa de aplicacion",
                cell_style,
            ),
            Paragraph("Optimizacion de calidad de datos", cell_style),
        ],
        [
            Paragraph("<b>Consistencia transaccional</b>", cell_style),
            Paragraph(
                "Vulnerable (errores por edicion manual y concurrencia)", cell_style
            ),
            Paragraph("<b>Alta</b> (operaciones atomicas)", cell_style),
            Paragraph(
                "Uso de transacciones <b>ACID</b> en operaciones de "
                "creacion/actualizacion",
                cell_style,
            ),
            Paragraph("Mayor fiabilidad operativa", cell_style),
        ],
        [
            Paragraph("<b>Trazabilidad operativa</b>", cell_style),
            Paragraph("Nula o parcial", cell_style),
            Paragraph("<b>Total</b> (auditoria por usuario y evento)", cell_style),
            Paragraph(
                "Registro de cambios, historial de acciones y seguimiento por entidad",
                cell_style,
            ),
            Paragraph("Transparencia y control", cell_style),
        ],
    ]

    col_widths = [4.2 * cm, 5.2 * cm, 4.6 * cm, 7.1 * cm, 4.2 * cm]
    table = Table(data, colWidths=col_widths, repeatRows=1)
    table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#e6eef8")),
                ("TEXTCOLOR", (0, 0), (-1, 0), colors.black),
                ("GRID", (0, 0), (-1, -1), 0.6, colors.HexColor("#8a8a8a")),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("LEFTPADDING", (0, 0), (-1, -1), 6),
                ("RIGHTPADDING", (0, 0), (-1, -1), 6),
                ("TOPPADDING", (0, 0), (-1, -1), 5),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 6),
            ]
        )
    )

    elements = [
        Paragraph("Tabla comparativa de calidad de datos en el CRM", title_style),
        Paragraph(
            "Validacion basada en el Indice de Integridad Referencial (IIR) y "
            "auditoria estructural de tablas criticas del sistema.",
            note_style,
        ),
        Spacer(1, 0.35 * cm),
        table,
    ]

    doc.build(elements)


if __name__ == "__main__":
    build_pdf("docs/tabla_comparativa_calidad_crm.pdf")
