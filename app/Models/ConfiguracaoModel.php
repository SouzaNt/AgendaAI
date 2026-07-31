<?php
require_once ROOT_PATH . '/core/Model.php';

class ConfiguracaoModel extends Model {
    protected static $table = 'configuracoes';

    public static function getConfig() {
        $configs = JsonDatabase::findAll('configuracoes');
        if (empty($configs)) {
            // Configurações Padrão de Fábrica
            $defaultConfig = [
                'tolerancia_minutos' => 15,
                'antecedencia_horas' => 1,
                'smtp_servidor' => 'smtp.agendaai.local',
                'smtp_porta' => 587,
                'smtp_usuario' => 'notificacoes@agendaai.local',
                'flags_email' => true,
                'agendamento_direto' => false,
                'limite_itens_usuario' => 5,
                'duracao_maxima_horas' => 8,
                'prazo_cancelamento_horas' => 2,
                'horario_abertura' => '07:00',
                'horario_fechamento' => '22:00',
                'nivel_visibilidade' => 'Publico', // Publico ou Oculto
                'justificativa_cancelamento_obrigatoria' => true,
                'agendamento_massa_habilitado' => true,
                'limite_dias_agendamento_massa' => 30
            ];
            JsonDatabase::insert('configuracoes', $defaultConfig);
            return $defaultConfig;
        }
        return $configs[0];
    }

    public static function updateConfig(array $data) {
        $configs = JsonDatabase::findAll('configuracoes');
        if (empty($configs)) {
            return self::create($data);
        }
        return self::update($configs[0]['id'], $data);
    }
}
