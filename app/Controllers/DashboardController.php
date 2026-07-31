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

        // Métricas BI
        $totalAgendamentos = count($agendamentos);
        $pendentes = array_values(array_filter($agendamentos, function ($a) { return $a['status'] === 'Pendente'; }));
        $aprovados = array_values(array_filter($agendamentos, function ($a) { return $a['status'] === 'Aprovado'; }));
        
        $recursosEmManutencao = array_values(array_filter($recursos, function ($r) {
            return ($r['estado'] ?? '') === 'Não Funcionando';
        }));

        $hojeStr = date('Y-m-d');
        $agendamentosHoje = array_values(array_filter($agendamentos, function ($a) use ($hojeStr) {
            return strpos($a['data_inicio'], $hojeStr) === 0 && $a['status'] === 'Aprovado';
        }));

        $data = [
            'totalAgendamentos' => $totalAgendamentos,
            'totalPendentes' => count($pendentes),
            'totalAprovados' => count($aprovados),
            'totalRecursos' => count($recursos),
            'totalManutencao' => count($recursosEmManutencao),
            'agendamentosPendentes' => array_slice($pendentes, 0, 5),
            'recursosManutencaoList' => $recursosEmManutencao,
            'agendamentosHoje' => $agendamentosHoje,
            'instituicoes' => $instituicoes
        ];

        $this->render('dashboard/index', $data);
    }
}
