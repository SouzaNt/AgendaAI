<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/app/Models/ConfiguracaoModel.php';

class ConfiguracaoController extends Controller {
    public function index() {
        if (!Auth::check()) $this->redirect('login');

        $config = ConfiguracaoModel::getConfig();
        $this->render('configuracoes/index', ['config' => $config]);
    }

    public function store() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();

        $payload = [
            'tolerancia_minutos' => (int)($data['tolerancia_minutos'] ?? 15),
            'antecedencia_horas' => (int)($data['antecedencia_horas'] ?? 1),
            'smtp_servidor' => trim($data['smtp_servidor'] ?? ''),
            'smtp_porta' => (int)($data['smtp_porta'] ?? 587),
            'smtp_usuario' => trim($data['smtp_usuario'] ?? ''),
            'flags_email' => !empty($data['flags_email']),
            'agendamento_direto' => !empty($data['agendamento_direto']),
            'limite_itens_usuario' => (int)($data['limite_itens_usuario'] ?? 5),
            'duracao_maxima_horas' => (int)($data['duracao_maxima_horas'] ?? 8),
            'prazo_cancelamento_horas' => (int)($data['prazo_cancelamento_horas'] ?? 2),
            'horario_abertura' => trim($data['horario_abertura'] ?? '07:00'),
            'horario_fechamento' => trim($data['horario_fechamento'] ?? '22:00'),
            'nivel_visibilidade' => trim($data['nivel_visibilidade'] ?? 'Publico'),
            'justificativa_cancelamento_obrigatoria' => !empty($data['justificativa_cancelamento_obrigatoria']),
            'agendamento_massa_habilitado' => !empty($data['agendamento_massa_habilitado']),
            'limite_dias_agendamento_massa' => (int)($data['limite_dias_agendamento_massa'] ?? 30)
        ];

        $updated = ConfiguracaoModel::updateConfig($payload);
        if ($updated) {
            return $this->json(['success' => true, 'message' => 'Parâmetros globais atualizados com sucesso!']);
        }
        return $this->json(['success' => false, 'message' => 'Erro ao salvar configurações.']);
    }
}
