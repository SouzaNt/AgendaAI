<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/app/Models/RecursoModel.php';
require_once ROOT_PATH . '/app/Models/TipoRecursoModel.php';
require_once ROOT_PATH . '/app/Models/InstituicaoModel.php';

class RecursoController extends Controller {
    public function index() {
        if (!Auth::check()) $this->redirect('login');

        $recursos = RecursoModel::getAll();
        $tipos = TipoRecursoModel::getAll();
        $instituicoes = InstituicaoModel::getAll();

        $this->render('recursos/index', [
            'recursos' => $recursos,
            'tipos' => $tipos,
            'instituicoes' => $instituicoes
        ]);
    }

    public function store() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();

        $id = $data['id'] ?? null;
        $nome = trim($data['nome'] ?? '');
        $idTipo = (int)($data['id_tipo_recurso'] ?? 0);
        $patrimonio = trim($data['patrimonio'] ?? '');
        $idInst = (int)($data['id_instituicao_responsavel'] ?? 0);
        $numSerie = trim($data['numero_serie'] ?? '');
        $estado = trim($data['estado'] ?? 'Funcionando');

        if (empty($nome) || empty($patrimonio) || !$idTipo || !$idInst) {
            return $this->json(['success' => false, 'message' => 'Preencha todos os campos obrigatórios (Nome, Tipo, Patrimônio e Instituição).']);
        }

        $payload = [
            'nome' => $nome,
            'id_tipo_recurso' => $idTipo,
            'patrimonio' => $patrimonio,
            'id_instituicao_responsavel' => $idInst,
            'numero_serie' => $numSerie,
            'estado' => $estado,
            'disponivel_agendamento' => ($estado === 'Funcionando')
        ];

        if ($id) {
            $updated = RecursoModel::update($id, $payload);
            return $this->json(['success' => true, 'message' => 'Recurso atualizado com sucesso!', 'data' => $updated]);
        } else {
            $payload['historico_movimentacao'] = "Cadastrado no sistema em " . date('d/m/Y H:i');
            $created = RecursoModel::create($payload);
            return $this->json(['success' => true, 'message' => 'Recurso cadastrado com sucesso!', 'data' => $created]);
        }
    }

    public function alterarEstado() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();

        $id = $data['id'] ?? null;
        $novoEstado = trim($data['estado'] ?? 'Funcionando');

        if (!$id) return $this->json(['success' => false, 'message' => 'ID do recurso não informado.']);

        $updated = RecursoModel::setEstado($id, $novoEstado);
        if ($updated) {
            return $this->json([
                'success' => true, 
                'message' => "Estado do recurso alterado para '{$novoEstado}'. " . ($novoEstado === 'Não Funcionando' ? 'Novos agendamentos foram BLOQUEADOS.' : 'Recurso DESBLOQUEADO para agendamento.')
            ]);
        }
        return $this->json(['success' => false, 'message' => 'Erro ao alterar estado do recurso.']);
    }

    public function delete() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();
        $id = $data['id'] ?? null;

        if (!$id) return $this->json(['success' => false, 'message' => 'ID inválido.']);

        $deleted = RecursoModel::delete($id);
        if ($deleted) {
            return $this->json(['success' => true, 'message' => 'Recurso removido com sucesso (Exclusão Lógica).']);
        }
        return $this->json(['success' => false, 'message' => 'Erro ao remover recurso.']);
    }
}
