<?php
require_once ROOT_PATH . '/core/Model.php';

class UsuarioModel extends Model {
    protected static $table = 'funcionarios';

    public static function findByEmail($email) {
        $users = self::where(function ($u) use ($email) {
            return strtolower($u['email']) === strtolower($email);
        }, true);
        return $users[0] ?? null;
    }

    public static function resetPasswordToDefault($id) {
        $defaultPassword = Crypto::hashPassword('123456');
        return self::update($id, ['senha' => $defaultPassword]);
    }
}
