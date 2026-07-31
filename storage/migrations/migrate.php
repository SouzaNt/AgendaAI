<?php
// Script de Migração Automatizado JSON -> MySQL
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

header('Content-Type: text/plain; charset=utf-8');

echo "====================================================\n";
echo " AgendaAI - Exportador / Migrador de JSON para MySQL \n";
echo "====================================================\n\n";

$tables = [
    'paises', 'estados', 'municipios', 'bairros', 'tipos_logradouro', 'logradouros',
    'grupos', 'funcoes', 'funcionarios', 'instituicoes', 'tipos_recurso',
    'recursos', 'salas', 'agendamentos', 'feedbacks', 'manutencoes',
    'auditoria', 'email_logs', 'configuracoes'
];

$sqlFile = __DIR__ . '/export_data.sql';
$sqlOutput = "-- Exportação de Dados do AgendaAI em " . date('Y-m-d H:i:s') . "\n\n";

foreach ($tables as $table) {
    $records = JsonDatabase::read($table);
    if (empty($records)) {
        echo "Tabela [{$table}]: 0 registros encontrados.\n";
        continue;
    }

    echo "Exportando [{$table}]: " . count($records) . " registros...\n";
    $sqlOutput .= "-- Dados para a tabela `{$table}`\n";

    foreach ($records as $row) {
        $keys = array_keys($row);
        $escapedKeys = array_map(function ($k) { return "`{$k}`"; }, $keys);
        
        $escapedValues = array_map(function ($v) {
            if ($v === null) return "NULL";
            if (is_bool($v)) return $v ? "1" : "0";
            if (is_array($v)) return "'" . addslashes(json_encode($v, JSON_UNESCAPED_UNICODE)) . "'";
            return "'" . addslashes($v) . "'";
        }, array_values($row));

        $sqlOutput .= "INSERT INTO `{$table}` (" . implode(', ', $escapedKeys) . ") VALUES (" . implode(', ', $escapedValues) . ");\n";
    }
    $sqlOutput .= "\n";
}

file_put_contents($sqlFile, $sqlOutput);

echo "\n====================================================\n";
echo " Migração / Exportação concluída com sucesso! \n";
echo " Arquivo SQL gerado em: " . $sqlFile . "\n";
echo "====================================================\n";
