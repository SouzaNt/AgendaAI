<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/app/Models/AgendamentoModel.php';
require_once ROOT_PATH . '/app/Models/RecursoModel.php';
require_once ROOT_PATH . '/app/Models/SalaModel.php';
require_once ROOT_PATH . '/app/Models/InstituicaoModel.php';
require_once ROOT_PATH . '/app/Models/UsuarioModel.php';

class DashboardController extends Controller {
    public function index() {
        if (!Auth::check()) {
            $this->redirect('login');
        }

        if (!Auth::canViewScreen('dashboard')) {
            $this->redirect('agenda');
        }

        $agendamentos = AgendamentoModel::getAll();
        $recursos = RecursoModel::getAll();
        $salas = SalaModel::getAll();
        $instituicoes = InstituicaoModel::getAll();
        $usuarios = UsuarioModel::getAll();

        // Métricas BI & Operacionais
        $totalAgendamentos = count($agendamentos);
        $pendentes = array_values(array_filter($agendamentos, function ($a) { return ($a['status'] ?? '') === 'Pendente'; }));
        $aprovados = array_values(array_filter($agendamentos, function ($a) { return ($a['status'] ?? '') === 'Aprovado'; }));
        $cancelados = array_values(array_filter($agendamentos, function ($a) { return ($a['status'] ?? '') === 'Cancelado' || ($a['status'] ?? '') === 'Recusado'; }));
        
        $recursosEmManutencao = array_values(array_filter($recursos, function ($r) {
            return ($r['estado'] ?? '') === 'Não Funcionando';
        }));

        $recursosAtivos = count($recursos) - count($recursosEmManutencao);
        $taxaOperacional = count($recursos) > 0 ? round(($recursosAtivos / count($recursos)) * 100, 1) : 100;

        $hojeStr = date('Y-m-d');
        $agendamentosHoje = array_values(array_filter($agendamentos, function ($a) use ($hojeStr) {
            return strpos($a['data_inicio'] ?? '', $hojeStr) === 0 && ($a['status'] ?? '') === 'Aprovado';
        }));

        $feedbacks = JsonDatabase::findAll('feedbacks');
        usort($feedbacks, function($a, $b) {
            return strtotime($b['created_at'] ?? '0') - strtotime($a['created_at'] ?? '0');
        });

        $manutencoes = JsonDatabase::findAll('manutencoes');

        $data = [
            'totalAgendamentos' => $totalAgendamentos,
            'totalPendentes' => count($pendentes),
            'totalAprovados' => count($aprovados),
            'totalCancelados' => count($cancelados),
            'totalRecursos' => count($recursos),
            'totalSalas' => count($salas),
            'totalInstituicoes' => count($instituicoes),
            'totalUsuarios' => count($usuarios),
            'taxaOperacional' => $taxaOperacional,
            'agendamentosPendentes' => array_slice($pendentes, 0, 6),
            'recursosManutencaoList' => $recursosEmManutencao,
            'agendamentosHoje' => $agendamentosHoje,
            'instituicoes' => $instituicoes,
            'feedbacksRecentes' => array_slice($feedbacks, 0, 4),
            'manutencoesRecentes' => array_slice($manutencoes, 0, 5)
        ];

        $this->render('dashboard/index', $data);
    }
}
