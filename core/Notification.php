<?php
require_once ROOT_PATH . '/config/database.php';

class Notification {
    public static function create($usuarioId, $titulo, $mensagem, $tipo = 'info') {
        $notificacao = [
            'usuario_id' => (int)$usuarioId,
            'titulo' => $titulo,
            'mensagem' => $mensagem,
            'tipo' => $tipo, // info, success, warning, danger
            'lida' => false,
            'data_hora' => date('Y-m-d H:i:s')
        ];
        return JsonDatabase::insert('notificacoes', $notificacao);
    }

    public static function sendEmailSimulated($destinatarioEmail, $destinatarioNome, $assunto, $corpoHtml, $tipoEvento = 'Agendamento') {
        // Verifica se envios de email estão habilitados nas configurações
        $config = JsonDatabase::findAll('configuracoes')[0] ?? [];
        $envioHabilitado = isset($config['flags_email']) ? (bool)$config['flags_email'] : true;

        $logEmail = [
            'destinatario_email' => $destinatarioEmail,
            'destinatario_nome' => $destinatarioNome,
            'assunto' => $assunto,
            'corpo' => $corpoHtml,
            'tipo_evento' => $tipoEvento,
            'data_envio' => date('Y-m-d H:i:s'),
            'status' => $envioHabilitado ? 'Enviado (Simulado)' : 'Desativado pelas Configurações'
        ];

        JsonDatabase::insert('email_logs', $logEmail);
        return true;
    }
}
