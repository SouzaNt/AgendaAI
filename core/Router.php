<?php

class Router {
    private $routes = [];

    public function add($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => trim($path, '/'),
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function get($path, $controller, $action) {
        $this->add('GET', $path, $controller, $action);
    }

    public function post($path, $controller, $action) {
        $this->add('POST', $path, $controller, $action);
    }

    public function dispatch($uri, $requestMethod) {
        // Trata query strings
        $uriParts = explode('?', $uri);
        $cleanUri = trim($uriParts[0], '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === strtoupper($requestMethod) && $route['path'] === $cleanUri) {
                $controllerName = $route['controller'];
                $actionName = $route['action'];

                $controllerFile = ROOT_PATH . "/app/Controllers/{$controllerName}.php";
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    $controller = new $controllerName();
                    if (method_exists($controller, $actionName)) {
                        $controller->$actionName();
                        return;
                    }
                }
            }
        }

        // Rota 404 se não for encontrada
        http_response_code(404);
        if ($this->isJsonRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Rota não encontrada (404).']);
        } else {
            echo "<h1>404 - Página não encontrada</h1><p>A rota solicitada '{$cleanUri}' não existe.</p><a href='" . BASE_URL . "/'>Voltar para o Início</a>";
        }
    }

    private function isJsonRequest() {
        return (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
            || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && stristr($_SERVER['HTTP_X_REQUESTED_WITH'], 'xmlhttprequest'));
    }
}
