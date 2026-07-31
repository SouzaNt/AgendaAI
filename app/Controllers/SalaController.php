<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/app/Models/SalaModel.php';
require_once ROOT_PATH . '/app/Models/InstituicaoModel.php';

class SalaController extends Controller {
    public function index() {
        if (!Auth::check()) $this->redirect('login');

        $salas = SalaModel::getAll();
        $instituicoes = InstituicaoModel::getAll();

        $this->render('salas/index', [
            'salas' => $salas,
            'instituicoes' => $instituicoes
        ]);
    }

    public function store() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();

        $id = $data['id'] ?? null;
        $nome = trim($data['nome'] ?? '');
        $idInst = (int)($data['id_instituicao_vinculada'] ?? 0);

        if (empty($nome) || !$idInst) {
            return $this->json(['success' => false, 'message' => 'Informe o nome da sala e a instituição vinculada.']);
        }

        $payload = [
            'nome' => $nome,
            'id_instituicao_vinculada' => $idInst
        ];

        if ($id) {
            $updated = SalaModel::update($id, $payload);
            return $this->json(['success' => true, 'message' => 'Sala atualizada com sucesso!', 'data' => $updated]);
        } else {
            $created = SalaModel::create($payload);
            return $this->json(['success' => true, 'message' => 'Sala cadastrada com sucesso!', 'data' => $created]);
        }
    }

    public function delete() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();
        $id = $data['id'] ?? null;

        if (!$id) return $this->json(['success' => false, 'message' => 'ID inválido.']);

        $deleted = SalaModel::delete($id);
        if ($deleted) {
            return $this->json(['success' => true, 'message' => 'Sala removida com sucesso.']);
        }
        return $this->json(['success' => false, 'message' => 'Erro ao remover sala.']);
    }
}
