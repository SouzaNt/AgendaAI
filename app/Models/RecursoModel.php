<?php
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/core/Notification.php';

class RecursoModel extends Model {
    protected static $table = 'recursos';

    public static function setEstado($id, $novoEstado) {
        $recurso = self::getById($id);
        if (!$recurso) return false;

        $anterior = $recurso['estado'] ?? 'Funcionando';
        $disponivel = ($novoEstado === 'Funcionando');

        $updated = self::update($id, [
            'estado' => $novoEstado,
            'disponivel_agendamento' => $disponivel
        ]);

        if ($updated && $novoEstado === 'Não Funcionando' && $anterior !== 'Não Funcionando') {
            // Notificar administradores / setor responsável
            Notification::create(
                1, 
                'Recurso em Manutenção', 
                "O recurso '{$recurso['nome']}' (Patrimônio: {$recurso['patrimonio']}) foi marcado como 'Não Funcionando'. Novos agendamentos bloqueados.",
                'danger'
            );
            
            // Log histórico de manutenção
            JsonDatabase::insert('manutencoes', [
                'recurso_id' => $id,
                'recurso_nome' => $recurso['nome'],
                'estado_anterior' => $anterior,
                'estado_novo' => $novoEstado,
                'data_registro' => date('Y-m-d H:i:s'),
                'usuario_id' => $_SESSION['usuario']['id'] ?? 1
            ]);
        }

        return $updated;
    }
}
