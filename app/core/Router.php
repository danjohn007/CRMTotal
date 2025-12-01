<?php
/**
 * Router Class
 * Handles URL routing with friendly URLs
 */
class Router {
    private array $routes = [];
    private array $params = [];
    
    public function add(string $route, array $params = []): void {
        // Convert route to regex
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9_-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        $route = '/^' . $route . '$/i';
        $this->routes[$route] = $params;
    }
    
    public function match(string $url): bool {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }
    
    public function dispatch(string $url): void {
        $url = $this->removeQueryString($url);
        
        if ($this->match($url)) {
            $controller = $this->params['controller'] ?? '';
            $controller = $this->convertToStudlyCaps($controller);
            $controller .= 'Controller';
            
            if (class_exists($controller)) {
                $controllerObj = new $controller($this->params);
                $action = $this->params['action'] ?? 'index';
                $action = $this->convertToCamelCase($action);
                
                if (is_callable([$controllerObj, $action])) {
                    $controllerObj->$action();
                } else {
                    throw new Exception("Method {$action} not found in controller {$controller}");
                }
            } else {
                throw new Exception("Controller class {$controller} not found");
            }
        } else {
            throw new Exception("Page not found", 404);
        }
    }
    
    protected function removeQueryString(string $url): string {
        if ($url !== '') {
            $parts = explode('?', $url, 2);
            $url = rtrim($parts[0], '/');
        }
        return $url;
    }
    
    protected function convertToStudlyCaps(string $string): string {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }
    
    protected function convertToCamelCase(string $string): string {
        return lcfirst($this->convertToStudlyCaps($string));
    }
    
    public function getParams(): array {
        return $this->params;
    }
}
