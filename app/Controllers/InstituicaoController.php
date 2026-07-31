<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/app/Models/InstituicaoModel.php';

class InstituicaoController extends Controller {
    public function index() {
        if (!Auth::check()) $this->redirect('login');

        $instituicoes = InstituicaoModel::getAll();

        $this->render('instituicoes/index', [
            'instituicoes' => $instituicoes
        ]);
    }

    public function store() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();

        $id = $data['id'] ?? null;
        $nome = trim($data['nome'] ?? '');
        $unidadePai = !empty($data['unidade_pai']) ? (int)$data['unidade_pai'] : null;
        $municipio = trim($data['municipio'] ?? '');
        $bairro = trim($data['bairro'] ?? '');
        $logradouro = trim($data['logradouro_completo'] ?? '');
        $numero = trim($data['numero'] ?? '');

        if (empty($nome)) {
            return $this->json(['success' => false, 'message' => 'Informe o nome da Instituição/Unidade.']);
        }

        $payload = [
            'nome' => $nome,
            'unidade_pai' => $unidadePai,
            'municipio' => $municipio,
            'bairro' => $bairro,
            'logradouro_completo' => $logradouro,
            'numero' => $numero
        ];

        if ($id) {
            $updated = InstituicaoModel::update($id, $payload);
            return $this->json(['success' => true, 'message' => 'Instituição atualizada com sucesso!', 'data' => $updated]);
        } else {
            $created = InstituicaoModel::create($payload);
            return $this->json(['success' => true, 'message' => 'Instituição cadastrada com sucesso!', 'data' => $created]);
        }
    }

    public function delete() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();
        $id = $data['id'] ?? null;

        if (!$id) return $this->json(['success' => false, 'message' => 'ID inválido.']);

        $deleted = InstituicaoModel::delete($id);
        if ($deleted) {
            return $this->json(['success' => true, 'message' => 'Instituição removida com sucesso.']);
        }
        return $this->json(['success' => false, 'message' => 'Erro ao remover instituição.']);
    }
}
