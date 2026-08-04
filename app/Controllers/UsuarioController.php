<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/app/Models/UsuarioModel.php';
require_once ROOT_PATH . '/app/Models/GrupoModel.php';
require_once ROOT_PATH . '/app/Models/FuncaoModel.php';
require_once ROOT_PATH . '/app/Models/InstituicaoModel.php';

class UsuarioController extends Controller {
    public function index() {
        if (!Auth::check()) $this->redirect('login');

        $usuarios = UsuarioModel::getAll();
        $grupos = GrupoModel::getAll();
        $funcoes = FuncaoModel::getAll();
        $instituicoes = InstituicaoModel::getAll();

        $this->render('usuarios/index', [
            'usuarios' => $usuarios,
            'grupos' => $grupos,
            'funcoes' => $funcoes,
            'instituicoes' => $instituicoes
        ]);
    }

    public function store() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();

        $id = $data['id'] ?? null;
        $nome = trim($data['nome'] ?? '');
        $email = trim($data['email'] ?? '');
        $senha = trim($data['senha'] ?? '');
        $idFuncao = (int)($data['id_funcao'] ?? 0);
        $idGrupo = (int)($data['id_grupo'] ?? 0);
        $instVinculadas = isset($data['instituicoes_vinculadas']) && is_array($data['instituicoes_vinculadas']) ? array_map('intval', $data['instituicoes_vinculadas']) : [];
        $recebeEmail = !empty($data['recebe_email']);

        if (empty($nome) || empty($email) || !$idFuncao || !$idGrupo) {
            return $this->json(['success' => false, 'message' => 'Preencha Nome, E-mail, Função e Grupo de Acesso.']);
        }

        $payload = [
            'nome' => $nome,
            'email' => $email,
            'id_funcao' => $idFuncao,
            'id_grupo' => $idGrupo,
            'instituicoes_vinculadas' => $instVinculadas,
            'recebe_email' => $recebeEmail
        ];

        if ($id) {
            if (!empty($senha)) {
                $payload['senha'] = Crypto::hashPassword($senha);
            }
            $updated = UsuarioModel::update($id, $payload);
            return $this->json(['success' => true, 'message' => 'Funcionário atualizado com sucesso!', 'data' => $updated]);
        } else {
            $senhaInicial = !empty($senha) ? $senha : '123456';
            $payload['senha'] = Crypto::hashPassword($senhaInicial);
            $created = UsuarioModel::create($payload);
            return $this->json(['success' => true, 'message' => 'Funcionário cadastrado com sucesso!', 'data' => $created]);
        }
    }

    public function resetSenhaAdmin() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();
        $id = $data['id'] ?? null;

        if (!$id) return $this->json(['success' => false, 'message' => 'ID de funcionário não informado.']);

        $updated = UsuarioModel::resetPasswordToDefault($id);
        if ($updated) {
            Audit::log('Reset de Senha pelo Admin', 'funcionarios', null, ['id' => $id, 'nova_senha' => '123456']);
            return $this->json(['success' => true, 'message' => 'Senha redefinida com sucesso para o padrão "123456".']);
        }
        return $this->json(['success' => false, 'message' => 'Erro ao redefinir senha.']);
    }

    public function delete() {
        if (!Auth::check()) return $this->json(['success' => false, 'message' => 'Não autorizado.'], 401);
        $data = $this->getPostData();
        $id = $data['id'] ?? null;

        if (!$id) return $this->json(['success' => false, 'message' => 'ID inválido.']);

        $deleted = UsuarioModel::delete($id);
        if ($deleted) {
            return $this->json(['success' => true, 'message' => 'Funcionário desativado com sucesso (Exclusão Lógica).']);
        }
        return $this->json(['success' => false, 'message' => 'Erro ao remover funcionário.']);
    }
}
