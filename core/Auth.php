<?php
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/core/Audit.php';

class Auth {
    public static function check() {
        return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
    }

    public static function user() {
        return $_SESSION['usuario'] ?? null;
    }

    public static function login($email, $senha) {
        $usuarios = JsonDatabase::findWhere('funcionarios', function ($u) use ($email) {
            return strtolower($u['email']) === strtolower($email) && ($u['ativo'] ?? true);
        });

        if (empty($usuarios)) {
            return ['success' => false, 'message' => 'E-mail ou senha inválidos.'];
        }

        $usuario = $usuarios[0];

        // Verificar senha com Criptografia
        $isValidPassword = Crypto::verifyPassword($senha, $usuario['senha']);
        if (!$isValidPassword && $senha === $usuario['senha']) {
            // Migração transparente caso a senha estivesse em texto puro
            $newHash = Crypto::hashPassword($senha);
            JsonDatabase::update('funcionarios', $usuario['id'], ['senha' => $newHash]);
            $usuario['senha'] = $newHash;
            $isValidPassword = true;
        }

        if ($isValidPassword) {
            // Re-hash automático se o algoritmo ou fator de custo mudou
            if (password_needs_rehash($usuario['senha'], PASSWORD_BCRYPT, ['cost' => 12])) {
                $newHash = Crypto::hashPassword($senha);
                JsonDatabase::update('funcionarios', $usuario['id'], ['senha' => $newHash]);
            }

            // Regenerar ID da sessão por segurança contra Session Fixation
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }

            // Buscar Grupo e Permissões
            $grupo = JsonDatabase::findById('grupos', $usuario['id_grupo'] ?? 1);
            $usuario['grupo_nome'] = $grupo['nome'] ?? 'Usuário';
            $usuario['permissoes'] = $grupo['permissoes'] ?? [
                'visualizar' => true,
                'consultar' => true,
                'editar' => false,
                'deletar' => false
            ];

            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email'],
                'foto' => $usuario['foto'] ?? null,
                'id_grupo' => $usuario['id_grupo'],
                'grupo_nome' => $usuario['grupo_nome'],
                'permissoes' => $usuario['permissoes'],
                'instituicoes_vinculadas' => $usuario['instituicoes_vinculadas'] ?? []
            ];

            Audit::log('Login', 'funcionarios', null, ['email' => $email], $usuario['id']);
            return ['success' => true, 'usuario' => $_SESSION['usuario']];
        }

        return ['success' => false, 'message' => 'E-mail ou senha inválidos.'];
    }

    public static function logout() {
        if (isset($_SESSION['usuario'])) {
            Audit::log('Logout', 'funcionarios', null, null, $_SESSION['usuario']['id']);
        }
        unset($_SESSION['usuario']);
        session_destroy();
    }

    public static function hasPermission($screen, $acao = 'visualizar') {
        if (!self::check()) return false;
        $user = self::user();
        if (($user['grupo_nome'] ?? '') === 'Administrador') {
            return true;
        }

        $permissoes = $user['permissoes'] ?? [];

        if (isset($permissoes[$screen]) && is_array($permissoes[$screen])) {
            return !empty($permissoes[$screen][$acao]);
        }

        if (isset($permissoes[$screen])) {
            return (bool)$permissoes[$screen];
        }

        return false;
    }

    public static function canViewScreen($screen) {
        return self::hasPermission($screen, 'visualizar');
    }

    public static function can($screen, $acao) {
        return self::hasPermission($screen, $acao);
    }
}
