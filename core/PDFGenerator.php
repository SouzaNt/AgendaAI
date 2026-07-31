<?php

class PDFGenerator {
    public static function generateHTMLReport($title, $headerInfo, $columns, $dataRows, $summaryData = []) {
        $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($title) . ' - AgendaAI</title>
    <style>
        body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 12px; color: #333; margin: 20px; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; color: #1e3a8a; margin: 0 0 5px 0; }
        .header p { margin: 2px 0; color: #64748b; font-size: 11px; }
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 15px; background: #f8fafc; padding: 10px; border-radius: 6px; }
        .info-item { font-size: 11px; }
        .info-item strong { color: #1e293b; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #1e293b; color: #ffffff; text-align: left; padding: 8px; font-size: 11px; font-weight: 600; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; display: inline-block; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #e0f2fe; color: #075985; }
        .summary-box { margin-top: 25px; padding: 12px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; }
        .summary-title { font-weight: bold; color: #1e40af; margin-bottom: 8px; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="background: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: bold;">🖨️ Imprimir / Salvar em PDF</button>
    </div>

    <div class="header">
        <h1>AgendaAI - ' . htmlspecialchars($title) . '</h1>
        <p>Relatório Oficial de Gestão e Uso de Recursos Institucionais</p>
        <p>Gerado em: ' . date('d/m/Y \à\s H:i:s') . ' (Fuso Horário: America/Sao_Paulo)</p>
    </div>

    <div class="info-grid">
        ' . $headerInfo . '
    </div>

    <table>
        <thead>
            <tr>';
        foreach ($columns as $col) {
            $html .= '<th>' . htmlspecialchars($col) . '</th>';
        }
        $html .= '</tr>
        </thead>
        <tbody>';

        foreach ($dataRows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . $cell . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody>
    </table>';

        if (!empty($summaryData)) {
            $html .= '<div class="summary-box">
                <div class="summary-title">Resumo de Indicadores BI:</div>';
            foreach ($summaryData as $key => $val) {
                $html .= '<div><strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars($val) . '</div>';
            }
            $html .= '</div>';
        }

        $html .= '<div class="footer">
            AgendaAI &copy; ' . date('Y') . ' - Todos os direitos reservados. Relatório gerado para fins de auditoria e governança.
        </div>
</body>
</html>';

        return $html;
    }
}
