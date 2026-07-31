<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/app/Models/GrupoModel.php';

class GrupoController extends Controller {
    public function index() {
        if (!Auth::check()) $this->redirect('login');

        $grupos = GrupoModel::getAll();
        $this->render('usuarios/grupos', ['grupos' => $grupos]);
    }

    public function store() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();

        $id = $data['id'] ?? null;
        $nome = trim($data['nome'] ?? '');
        $permissoes = isset($data['permissoes']) && is_array($data['permissoes']) ? $data['permissoes'] : [];

        if (empty($nome)) {
            return $this->json(['success' => false, 'message' => 'Informe o nome do Grupo de Acesso.']);
        }

        $payload = [
            'nome' => $nome,
            'permissoes' => $permissoes
        ];

        if ($id) {
            $updated = GrupoModel::update($id, $payload);
            return $this->json(['success' => true, 'message' => 'Grupo atualizado com sucesso!', 'data' => $updated]);
        } else {
            $created = GrupoModel::create($payload);
            return $this->json(['success' => true, 'message' => 'Grupo cadastrado com sucesso!', 'data' => $created]);
        }
    }

    public function delete() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();
        $id = $data['id'] ?? null;

        if (!$id) return $this->json(['success' => false, 'message' => 'ID inválido.']);

        $deleted = GrupoModel::delete($id);
        if ($deleted) {
            return $this->json(['success' => true, 'message' => 'Grupo removido com sucesso.']);
        }
        return $this->json(['success' => false, 'message' => 'Erro ao remover grupo.']);
    }
}
