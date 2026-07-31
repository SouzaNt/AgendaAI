<?php
require_once ROOT_PATH . '/config/database.php';

class Audit {
    public static function log($acao, $tabela, $valoresAnteriores = null, $valoresNovos = null, $usuarioId = null) {
        if ($usuarioId === null && isset($_SESSION['usuario']['id'])) {
            $usuarioId = $_SESSION['usuario']['id'];
            $usuarioNome = $_SESSION['usuario']['nome'] ?? 'Sistema';
        } else {
            $usuarioNome = 'Sistema';
            if ($usuarioId) {
                $u = JsonDatabase::findById('funcionarios', $usuarioId);
                if ($u) {
                    $usuarioNome = $u['nome'];
                }
            }
        }

        $logEntry = [
            'data_hora' => date('Y-m-d H:i:s'),
            'usuario_id' => $usuarioId,
            'usuario_nome' => $usuarioNome,
            'acao' => $acao, // Inclusão, Alteração, Exclusão Lógica, Login, Reset de Senha, etc.
            'tabela' => $tabela,
            'valores_anteriores' => $valoresAnteriores,
            'valores_novos' => $valoresNovos,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ];

        JsonDatabase::insert('auditoria', $logEntry);
    }
}
