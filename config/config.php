<?php
// Define fuso horário padrão para America/Sao_Paulo (Brasília)
date_default_timezone_set('America/Sao_Paulo');

// Caminhos base da aplicação
define('ROOT_PATH', dirname(__DIR__));
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('DATA_PATH', STORAGE_PATH . '/data');
define('LOG_PATH', STORAGE_PATH . '/logs');
define('UPLOAD_PATH', STORAGE_PATH . '/uploads');

// Detecção dinâmica de Base URL para portabilidade (suporta localhost/agendaai2 ou php -S 0.0.0.0:80)
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = rtrim($scriptDir, '/');
if ($baseUrl === '' || $baseUrl === '/') {
    $baseUrl = '';
}
define('BASE_URL', $baseUrl);
define('APP_SECRET', 'SenacAgendaAI_Secret_Key_2026_Secure_Hash_v1');

require_once ROOT_PATH . '/core/Crypto.php';

// Inicia sessão com configurações seguras de cookie se ainda não iniciada
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Handler de log de erros em arquivo .txt
function logError($message, $context = []) {
    if (!is_dir(LOG_PATH)) {
        @mkdir(LOG_PATH, 0777, true);
    }
    $logFile = LOG_PATH . '/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' | Contexto: ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
    $entry = "[{$timestamp}] [ERRO] {$message}{$contextStr}" . PHP_EOL;
    @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}

// Configura handler global de exceções não capturadas
set_exception_handler(function ($exception) {
    logError("Exceção não capturada: " . $exception->getMessage(), [
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
});
