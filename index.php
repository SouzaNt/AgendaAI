<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Router.php';

$router = new Router();

// Rotas de Autenticação
$router->get('', 'AuthController', 'loginView');
$router->get('login', 'AuthController', 'loginView');
$router->post('login', 'AuthController', 'loginSubmit');
$router->get('logout', 'AuthController', 'logout');
$router->post('forgot-password', 'AuthController', 'forgotPassword');
$router->get('reset-password', 'AuthController', 'resetView');
$router->post('reset-password', 'AuthController', 'resetSubmit');

// Dashboard BI
$router->get('dashboard', 'DashboardController', 'index');

// Agenda e Agendamentos
$router->get('agenda', 'AgendaController', 'index');
$router->get('aprovacoes', 'AgendaController', 'aprovacoesView');
$router->get('api/agenda/events', 'AgendaController', 'getEvents');
$router->post('api/agenda/disponibilidade', 'AgendaController', 'checkDisponibilidade');
$router->post('api/agenda/store', 'AgendaController', 'store');
$router->post('api/agenda/massa', 'AgendaController', 'storeMassa');
$router->post('api/agenda/aprovar', 'AgendaController', 'aprovar');
$router->post('api/agenda/cancelar', 'AgendaController', 'cancelar');
$router->post('api/agenda/feedback', 'AgendaController', 'salvarFeedback');

// Recursos e Manutenção
$router->get('recursos', 'RecursoController', 'index');
$router->post('api/recursos/store', 'RecursoController', 'store');
$router->post('api/recursos/estado', 'RecursoController', 'alterarEstado');
$router->post('api/recursos/delete', 'RecursoController', 'delete');

// Salas de Aula
$router->get('salas', 'SalaController', 'index');
$router->post('api/salas/store', 'SalaController', 'store');
$router->post('api/salas/delete', 'SalaController', 'delete');

// Instituições
$router->get('instituicoes', 'InstituicaoController', 'index');
$router->post('api/instituicoes/store', 'InstituicaoController', 'store');
$router->post('api/instituicoes/delete', 'InstituicaoController', 'delete');

// Usuários e Perfis
$router->get('usuarios', 'UsuarioController', 'index');
$router->post('api/usuarios/store', 'UsuarioController', 'store');
$router->post('api/usuarios/reset-admin', 'UsuarioController', 'resetSenhaAdmin');
$router->post('api/usuarios/delete', 'UsuarioController', 'delete');

// Grupos e Permissões
$router->get('usuarios/grupos', 'GrupoController', 'index');
$router->post('api/grupos/store', 'GrupoController', 'store');
$router->post('api/grupos/delete', 'GrupoController', 'delete');

// Funções/Cargos
$router->post('api/funcoes/store', 'FuncaoController', 'store');

// Base Geográfica
$router->get('localizacao', 'LocalizacaoController', 'index');
$router->post('api/localizacao/pais', 'LocalizacaoController', 'storePais');
$router->post('api/localizacao/estado', 'LocalizacaoController', 'storeEstado');
$router->post('api/localizacao/municipio', 'LocalizacaoController', 'storeMunicipio');
$router->post('api/localizacao/bairro', 'LocalizacaoController', 'storeBairro');
$router->post('api/localizacao/logradouro', 'LocalizacaoController', 'storeLogradouro');

// Configurações Globais
$router->get('configuracoes', 'ConfiguracaoController', 'index');
$router->post('api/configuracoes/store', 'ConfiguracaoController', 'store');

// Relatórios BI
$router->get('relatorios', 'RelatorioController', 'index');
$router->get('relatorios/pdf', 'RelatorioController', 'exportarPDF');

// Logs e Auditoria
$router->get('auditoria', 'AuditController', 'index');

// Despacho da Rota
$route = $_GET['route'] ?? '';
$requestMethod = $_SERVER['REQUEST_METHOD'];

$router->dispatch($route, $requestMethod);
