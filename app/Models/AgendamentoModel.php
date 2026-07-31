<?php
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/core/Notification.php';

class AgendamentoModel extends Model {
    protected static $table = 'agendamentos';

    /**
     * Verifica conflito de horário para um conjunto de recursos e salas
     */
    public static function checkConflict($inicio, $fim, array $recursosIds, array $salasIds, $ignoreAgendamentoId = null) {
        // Carrega tolerância em minutos das configurações
        $config = JsonDatabase::findAll('configuracoes')[0] ?? [];
        $toleranciaMinutos = isset($config['tolerancia_minutos']) ? (int)$config['tolerancia_minutos'] : 15;

        // Estende o intervalo com a tolerância
        $tsInicio = strtotime($inicio) - ($toleranciaMinutos * 60);
        $tsFim = strtotime($fim) + ($toleranciaMinutos * 60);

        $agendamentos = self::where(function ($a) use ($ignoreAgendamentoId) {
            if ($ignoreAgendamentoId && (string)$a['id'] === (string)$ignoreAgendamentoId) {
                return false;
            }
            return in_array($a['status'], ['Pendente', 'Aprovado']);
        });

        $conflitos = [];

        foreach ($agendamentos as $ag) {
            $agInicio = strtotime($ag['data_inicio']);
            $agFim = strtotime($ag['data_fim']);

            // Checa se há sobreposição no tempo
            if ($tsInicio < $agFim && $tsFim > $agInicio) {
                // Checa conflito de recursos
                if (!empty($recursosIds) && !empty($ag['recursos_ids'])) {
                    $intersection = array_intersect($recursosIds, $ag['recursos_ids']);
                    if (!empty($intersection)) {
                        $conflitos[] = [
                            'agendamento' => $ag,
                            'tipo' => 'recurso',
                            'itens_conflitantes' => array_values($intersection)
                        ];
                    }
                }
                // Checa conflito de salas
                if (!empty($salasIds) && !empty($ag['salas_ids'])) {
                    $intersection = array_intersect($salasIds, $ag['salas_ids']);
                    if (!empty($intersection)) {
                        $conflitos[] = [
                            'agendamento' => $ag,
                            'tipo' => 'sala',
                            'itens_conflitantes' => array_values($intersection)
                        ];
                    }
                }
            }
        }

        return $conflitos;
    }

    /**
     * Trata Agendamento de Emergência (Prioridade Alta)
     */
    public static function processEmergencyOverride($conflitos, $usuarioEmergenciaNome) {
        foreach ($conflitos as $conf) {
            $ag = $conf['agendamento'];
            // Atualiza o agendamento conflitante para Cancelado por Emergência
            self::update($ag['id'], [
                'status' => 'Cancelado (Sobreposição de Emergência)',
                'motivo_cancelamento' => "Cancelado automaticamente devido a Agendamento de Emergência de Prioridade por: {$usuarioEmergenciaNome}"
            ]);

            // Notifica o usuário afetado
            Notification::create(
                $ag['usuario_id'],
                '⚠️ Agendamento Cancelado (Prioridade de Emergência)',
                "Seu agendamento (#{$ag['id']}) do dia " . date('d/m/Y H:i', strtotime($ag['data_inicio'])) . " foi cancelado devido a uma reserva de emergência prioritária.",
                'danger'
            );

            // Simula e-mail para o usuário afetado
            $u = JsonDatabase::findById('funcionarios', $ag['usuario_id']);
            if ($u) {
                Notification::sendEmailSimulated(
                    $u['email'],
                    $u['nome'],
                    'Alerta: Cancelamento de Agendamento por Emergência',
                    "<p>Olá <strong>{$u['nome']}</strong>,</p><p>Informamos que seu agendamento #{$ag['id']} foi substituído por uma reserva de emergência institucional.</p>"
                );
            }
        }
    }
}
