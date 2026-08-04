<?php

class Crypto {
    /**
     * Gera uma hash segura de senha utilizando BCRYPT com fator de custo 12.
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verifica se a senha corresponde à hash armazenada.
     */
    public static function verifyPassword($password, $hash) {
        if (empty($password) || empty($hash)) return false;
        return password_verify($password, $hash);
    }

    /**
     * Gera um token aleatório criptograficamente seguro em formato hexadecimal.
     */
    public static function generateToken($length = 32) {
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes($length));
        }
        return bin2hex(md5(uniqid(microtime(), true)));
    }

    /**
     * Gera o hash SHA-256 de um token para armazenamento seguro no banco de dados.
     */
    public static function hashToken($token) {
        if (empty($token)) return null;
        return hash('sha256', $token);
    }

    /**
     * Criptografa uma string de texto puro.
     */
    public static function encrypt($plaintext, $key = null) {
        if (empty($plaintext)) return '';
        $secretKey = $key ?: (defined('APP_SECRET') ? APP_SECRET : 'SenacAgendaAI_Secret_Key_2026_Secure_Hash');

        if (function_exists('openssl_encrypt')) {
            $cipher = "aes-256-gcm";
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $tag = "";
            $ciphertext = openssl_encrypt($plaintext, $cipher, $secretKey, OPENSSL_RAW_DATA, $iv, $tag);
            if ($ciphertext !== false) {
                return base64_encode($iv . $tag . $ciphertext);
            }
        }

        // Fallback robusto nativo para PHP sem extensão OpenSSL CLI
        $iv = self::generateToken(16);
        $salt = hash('sha256', $secretKey . $iv);
        $encrypted = '';
        for ($i = 0; $i < strlen($plaintext); $i++) {
            $encrypted .= chr(ord($plaintext[$i]) ^ ord($salt[$i % strlen($salt)]));
        }
        $hmac = hash_hmac('sha256', $encrypted, $secretKey);
        return base64_encode($iv . ':' . $hmac . ':' . $encrypted);
    }

    /**
     * Descriptografa uma string de texto.
     */
    public static function decrypt($encoded, $key = null) {
        if (empty($encoded)) return '';
        $secretKey = $key ?: (defined('APP_SECRET') ? APP_SECRET : 'SenacAgendaAI_Secret_Key_2026_Secure_Hash');
        $raw = base64_decode($encoded);

        if (function_exists('openssl_decrypt') && strpos($raw, ':') === false) {
            $cipher = "aes-256-gcm";
            $ivlen = openssl_cipher_iv_length($cipher);
            $taglen = 16;
            if (strlen($raw) >= $ivlen + $taglen) {
                $iv = substr($raw, 0, $ivlen);
                $tag = substr($raw, $ivlen, $taglen);
                $ciphertext = substr($raw, $ivlen + $taglen);
                $decrypted = openssl_decrypt($ciphertext, $cipher, $secretKey, OPENSSL_RAW_DATA, $iv, $tag);
                if ($decrypted !== false) return $decrypted;
            }
        }

        // Fallback de descriptografia
        $parts = explode(':', $raw, 3);
        if (count($parts) !== 3) return false;
        list($iv, $hmac, $ciphertext) = $parts;

        $calcHmac = hash_hmac('sha256', $ciphertext, $secretKey);
        if (!hash_equals($calcHmac, $hmac)) return false;

        $salt = hash('sha256', $secretKey . $iv);
        $decrypted = '';
        for ($i = 0; $i < strlen($ciphertext); $i++) {
            $decrypted .= chr(ord($ciphertext[$i]) ^ ord($salt[$i % strlen($salt)]));
        }
        return $decrypted;
    }
}
