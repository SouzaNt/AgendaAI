<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/app/Models/FuncaoModel.php';

class FuncaoController extends Controller {
    public function store() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();

        $nome = trim($data['nome'] ?? '');
        if (empty($nome)) {
            return $this->json(['success' => false, 'message' => 'Informe o nome da função/cargo.']);
        }

        $created = FuncaoModel::create(['nome' => $nome]);
        return $this->json(['success' => true, 'message' => 'Função cadastrada com sucesso!', 'data' => $created]);
    }
}
