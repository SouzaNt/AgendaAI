<?php

abstract class Controller {
    protected function render($viewPath, array $data = []) {
        extract($data);
        $fullViewPath = ROOT_PATH . "/views/{$viewPath}.php";
        
        if (!file_exists($fullViewPath)) {
            logError("View não encontrada: {$viewPath}");
            die("View não encontrada: {$viewPath}");
        }

        require_once ROOT_PATH . "/views/layouts/header.php";
        require_once $fullViewPath;
        require_once ROOT_PATH . "/views/layouts/footer.php";
    }

    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    protected function redirect($url) {
        $fullUrl = (strpos($url, 'http') === 0) ? $url : BASE_URL . '/' . ltrim($url, '/');
        header("Location: {$fullUrl}");
        exit;
    }

    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type, // success, danger, warning, info
            'message' => $message
        ];
    }

    protected function getPostData() {
        $json = file_get_contents('php://input');
        if (!empty($json)) {
            $data = json_decode($json, true);
            if (is_array($data)) {
                return $data;
            }
        }
        return $_POST;
    }
}
