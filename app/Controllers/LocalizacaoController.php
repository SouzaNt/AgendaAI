<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/app/Models/LocalizacaoModel.php';

class LocalizacaoController extends Controller {
    public function index() {
        if (!Auth::check()) $this->redirect('login');

        $paises = LocalizacaoModel::getPaises();
        $estados = LocalizacaoModel::getEstados();
        $municipios = LocalizacaoModel::getMunicipios();
        $bairros = LocalizacaoModel::getBairros();
        $tiposLogradouro = LocalizacaoModel::getTiposLogradouro();
        $logradouros = LocalizacaoModel::getLogradouros();

        $this->render('localizacao/index', [
            'paises' => $paises,
            'estados' => $estados,
            'municipios' => $municipios,
            'bairros' => $bairros,
            'tiposLogradouro' => $tiposLogradouro,
            'logradouros' => $logradouros
        ]);
    }

    public function storePais() {
        if (!Auth::check()) return $this->json(['success' => false], 401);
        $data = $this->getPostData();
        $nome = trim($data['nome'] ?? '');
        if (!$nome) return $this->json(['success' => false, 'message' => 'Nome obrigatório']);
        $res = JsonDatabase::insert('paises', ['nome' => $nome]);
        Audit::log('Inclusão', 'paises', null, $res);
        return $this->json(['success' => true, 'data' => $res]);
    }

    public function storeEstado() {
        if (!Auth::check()) return $this->json(['success' => false], 401);
        $data = $this->getPostData();
        $nome = trim($data['nome'] ?? '');
        $uf = strtoupper(trim($data['uf'] ?? ''));
        if (!$nome || !$uf) return $this->json(['success' => false, 'message' => 'Nome e UF obrigatórios']);
        $res = JsonDatabase::insert('estados', ['nome' => $nome, 'uf' => $uf]);
        Audit::log('Inclusão', 'estados', null, $res);
        return $this->json(['success' => true, 'data' => $res]);
    }

    public function storeMunicipio() {
        if (!Auth::check()) return $this->json(['success' => false], 401);
        $data = $this->getPostData();
        $nome = trim($data['nome'] ?? '');
        $idEstado = (int)($data['id_estado'] ?? 0);
        if (!$nome || !$idEstado) return $this->json(['success' => false, 'message' => 'Nome e Estado obrigatórios']);
        $res = JsonDatabase::insert('municipios', ['nome' => $nome, 'id_estado' => $idEstado]);
        Audit::log('Inclusão', 'municipios', null, $res);
        return $this->json(['success' => true, 'data' => $res]);
    }

    public function storeBairro() {
        if (!Auth::check()) return $this->json(['success' => false], 401);
        $data = $this->getPostData();
        $nome = trim($data['nome'] ?? '');
        if (!$nome) return $this->json(['success' => false, 'message' => 'Nome obrigatório']);
        $res = JsonDatabase::insert('bairros', ['nome' => $nome]);
        Audit::log('Inclusão', 'bairros', null, $res);
        return $this->json(['success' => true, 'data' => $res]);
    }

    public function storeLogradouro() {
        if (!Auth::check()) return $this->json(['success' => false], 401);
        $data = $this->getPostData();
        $nome = trim($data['nome'] ?? '');
        $idTipo = (int)($data['id_tipo_logradouro'] ?? 0);
        if (!$nome || !$idTipo) return $this->json(['success' => false, 'message' => 'Nome e Tipo de Logradouro obrigatórios']);
        $res = JsonDatabase::insert('logradouros', ['nome' => $nome, 'id_tipo_logradouro' => $idTipo]);
        Audit::log('Inclusão', 'logradouros', null, $res);
        return $this->json(['success' => true, 'data' => $res]);
    }
}
