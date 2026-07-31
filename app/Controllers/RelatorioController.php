<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/core/PDFGenerator.php';
require_once ROOT_PATH . '/app/Models/AgendamentoModel.php';
require_once ROOT_PATH . '/app/Models/RecursoModel.php';
require_once ROOT_PATH . '/app/Models/TipoRecursoModel.php';
require_once ROOT_PATH . '/app/Models/InstituicaoModel.php';
require_once ROOT_PATH . '/app/Models/ManutencaoModel.php';
require_once ROOT_PATH . '/app/Models/FeedbackModel.php';

class RelatorioController extends Controller {
    public function index() {
        if (!Auth::check()) $this->redirect('login');

        $agendamentos = AgendamentoModel::getAll();
        $recursos = RecursoModel::getAll();
        $tipos = TipoRecursoModel::getAll();
        $instituicoes = InstituicaoModel::getAll();
        $manutencoes = ManutencaoModel::getAll();
        $feedbacks = FeedbackModel::getAll();

        // Contadores para BI Analytics
        $totalReservas = count($agendamentos);
        $totalAprovadas = count(array_filter($agendamentos, function ($a) { return $a['status'] === 'Aprovado'; }));
        $totalCanceladas = count(array_filter($agendamentos, function ($a) { return strpos($a['status'], 'Cancelado') !== false; }));

        // Ranking de recursos mais requisitados
        $recursoContador = [];
        foreach ($agendamentos as $ag) {
            if (!empty($ag['recursos_ids']) && is_array($ag['recursos_ids'])) {
                foreach ($ag['recursos_ids'] as $rId) {
                    $recursoContador[$rId] = ($recursoContador[$rId] ?? 0) + 1;
                }
            }
        }
        arsort($recursoContador);

        $rankingRecursos = [];
        foreach ($recursoContador as $rId => $qtd) {
            $rec = RecursoModel::getById($rId);
            if ($rec) {
                $rankingRecursos[] = [
                    'nome' => $rec['nome'],
                    'patrimonio' => $rec['patrimonio'],
                    'quantidade' => $qtd
                ];
            }
        }

        $this->render('relatorios/index', [
            'totalReservas' => $totalReservas,
            'totalAprovadas' => $totalAprovadas,
            'totalCanceladas' => $totalCanceladas,
            'rankingRecursos' => $rankingRecursos,
            'manutencoes' => $manutencoes,
            'feedbacks' => $feedbacks,
            'instituicoes' => $instituicoes
        ]);
    }

    public function exportarPDF() {
        if (!Auth::check()) $this->redirect('login');

        $tipo = $_GET['tipo'] ?? 'utilizacao';
        $agendamentos = AgendamentoModel::getAll();
        $recursos = RecursoModel::getAll();

        if ($tipo === 'manutencao') {
            $title = 'Relatório de Manutenções e Ociosidade';
            $headerInfo = '<div class="info-item"><strong>Filtro:</strong> Manutenções Registradas</div><div class="info-item"><strong>Instituição:</strong> Todas</div>';
            $columns = ['ID', 'Recurso', 'Estado Anterior', 'Estado Novo', 'Data de Registro', 'Usuário Resp.'];
            
            $manutencoes = ManutencaoModel::getAll();
            $rows = [];
            foreach ($manutencoes as $m) {
                $u = JsonDatabase::findById('funcionarios', $m['usuario_id']);
                $rows[] = [
                    '#' . $m['id'],
                    htmlspecialchars($m['recurso_nome']),
                    '<span class="badge badge-info">' . htmlspecialchars($m['estado_anterior']) . '</span>',
                    '<span class="badge badge-danger">' . htmlspecialchars($m['estado_novo']) . '</span>',
                    date('d/m/Y H:i', strtotime($m['data_registro'])),
                    htmlspecialchars($u['nome'] ?? 'Sistema')
                ];
            }
            $summary = ['Total de Manutenções' => count($manutencoes)];
        } else {
            $title = 'Relatório Generalizado de Utilização de Recursos';
            $headerInfo = '<div class="info-item"><strong>Escopo:</strong> Agendamentos Gerais</div><div class="info-item"><strong>Status:</strong> Todos</div>';
            $columns = ['ID Agendamento', 'Agendador / Requisitante', 'Tipo Uso', 'Data/Hora Início', 'Data/Hora Fim', 'Status'];

            $rows = [];
            foreach ($agendamentos as $ag) {
                $badgeClass = 'badge-warning';
                if ($ag['status'] === 'Aprovado') $badgeClass = 'badge-success';
                if (strpos($ag['status'], 'Cancelado') !== false) $badgeClass = 'badge-danger';

                $rows[] = [
                    '#' . $ag['id'],
                    htmlspecialchars($ag['usuario_nome']),
                    htmlspecialchars($ag['tipo_uso']),
                    date('d/m/Y H:i', strtotime($ag['data_inicio'])),
                    date('d/m/Y H:i', strtotime($ag['data_fim'])),
                    '<span class="badge ' . $badgeClass . '">' . htmlspecialchars($ag['status']) . '</span>'
                ];
            }

            $summary = [
                'Total de Agendamentos' => count($agendamentos),
                'Confirmados/Aprovados' => count(array_filter($agendamentos, function($a){ return $a['status'] === 'Aprovado'; })),
                'Pendentes' => count(array_filter($agendamentos, function($a){ return $a['status'] === 'Pendente'; }))
            ];
        }

        $html = PDFGenerator::generateHTMLReport($title, $headerInfo, $columns, $rows, $summary);
        echo $html;
        exit;
    }
}
