<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/core/Notification.php';
require_once ROOT_PATH . '/app/Models/UsuarioModel.php';

class AuthController extends Controller {
    public function loginView() {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }
        require_once ROOT_PATH . '/views/auth/login.php';
    }

    public function loginSubmit() {
        $data = $this->getPostData();
        $email = trim($data['email'] ?? '');
        $senha = trim($data['senha'] ?? '');

        if (empty($email) || empty($senha)) {
            return $this->json(['success' => false, 'message' => 'Preencha o e-mail e a senha.']);
        }

        $res = Auth::login($email, $senha);
        return $this->json($res);
    }

    public function logout() {
        Auth::logout();
        $this->redirect('login');
    }

    public function forgotPassword() {
        $data = $this->getPostData();
        $email = trim($data['email'] ?? '');

        if (empty($email)) {
            return $this->json(['success' => false, 'message' => 'Informe seu e-mail cadastrado.']);
        }

        $user = UsuarioModel::findByEmail($email);
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'E-mail não encontrado no sistema.']);
        }

        // Gerar token de recuperação temporário
        $token = bin2hex(random_bytes(16));
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

        UsuarioModel::update($user['id'], [
            'reset_token' => $token,
            'reset_token_expires' => $expiration
        ]);

        $resetLink = BASE_URL . "/reset-password?token=" . $token;

        $corpoEmail = "<p>Olá <strong>{$user['nome']}</strong>,</p>
            <p>Você solicitou a recuperação de senha no AgendaAI.</p>
            <p>Clique no link a seguir para redefinir sua senha (válido por 1 hora):</p>
            <p><a href='{$resetLink}' style='background:#2563eb;color:#fff;padding:8px 16px;text-decoration:none;border-radius:4px;'>Redefinir Minha Senha</a></p>
            <p>Se você não solicitou esta alteração, desconsidere este e-mail.</p>";

        Notification::sendEmailSimulated($user['email'], $user['nome'], 'Recuperação de Senha - AgendaAI', $corpoEmail, 'Esqueci Minha Senha');

        return $this->json([
            'success' => true, 
            'message' => 'Instruções de redefinição e link temporário foram enviados para seu e-mail.',
            'debug_link' => $resetLink // Facilitador de testes
        ]);
    }

    public function resetView() {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            die("Token inválido ou expirado.");
        }
        $users = UsuarioModel::where(function ($u) use ($token) {
            return ($u['reset_token'] ?? '') === $token && strtotime($u['reset_token_expires'] ?? '') > time();
        });

        if (empty($users)) {
            die("Token inválido ou expirado. Por favor, solicite a recuperação novamente.");
        }

        $user = $users[0];
        require_once ROOT_PATH . '/views/auth/reset.php';
    }

    public function resetSubmit() {
        $data = $this->getPostData();
        $token = trim($data['token'] ?? '');
        $novaSenha = trim($data['nova_senha'] ?? '');

        if (empty($token) || strlen($novaSenha) < 6) {
            return $this->json(['success' => false, 'message' => 'A senha deve conter no mínimo 6 caracteres.']);
        }

        $users = UsuarioModel::where(function ($u) use ($token) {
            return ($u['reset_token'] ?? '') === $token && strtotime($u['reset_token_expires'] ?? '') > time();
        });

        if (empty($users)) {
            return $this->json(['success' => false, 'message' => 'Token expirado ou inválido.']);
        }

        $user = $users[0];
        $newHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        UsuarioModel::update($user['id'], [
            'senha' => $newHash,
            'reset_token' => null,
            'reset_token_expires' => null
        ]);

        Audit::log('Reset de Senha', 'funcionarios', null, ['email' => $user['email']], $user['id']);

        return $this->json(['success' => true, 'message' => 'Senha alterada com sucesso! Você já pode fazer login.']);
    }
}
