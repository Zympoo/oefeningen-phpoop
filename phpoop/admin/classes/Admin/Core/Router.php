<?php
declare(strict_types=1);

namespace Admin\Core;

class Router
{
    /**
     * $routes
     *
     * Doel:
     * Bewaart alle routes per HTTP method.
     * Elke route bevat:
     * - pattern: regex pattern om URL te matchen
     * - handler: callable die uitgevoerd wordt
     */
    private array $routes = [];

    /**
     * $notFoundHandler
     *
     * Doel:
     * Bewaart een callback die uitgevoerd wordt als geen enkele route matcht.
     * Als deze niet is ingesteld, gebruiken we een simpele standaard 404.
     */
    private $notFoundHandler = null;

    /**
     * setNotFoundHandler()
     *
     * Doel:
     * Laat de applicatie bepalen wat er moet gebeuren bij een 404.
     *
     * Werking:
     * - Je geeft een callable mee (bv. ErrorController->notFound(...)).
     * - De router bewaart die callable en voert ze later uit in notFound().
     */
    public function setNotFoundHandler(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    /**
     * get()
     *
     * Doel:
     * Registreert een GET-route en bewaart deze intern.
     */
    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * post()
     *
     * Doel:
     * Registreert een POST-route en bewaart deze intern.
     */
    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * addRoute()
     *
     * Doel:
     * Slaat een route op met:
     * - gecompileerde regex pattern
     * - handler callback
     */
    private function addRoute(string $method, string $path, callable $handler): void
    {
        $pattern = $this->compilePattern($path);

        $this->routes[$method][] = [
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    /**
     * dispatch()
     *
     * Doel:
     * Zoekt de juiste route voor de huidige URI en HTTP method.
     *
     * Werking:
     * 1) Normaliseert de URI zodat paden consistent zijn.
     * 2) Loopt door alle routes voor deze HTTP method.
     * 3) Matcht via preg_match().
     * 4) Geeft parameters uit de URL door aan de handler.
     * 5) Als niets matcht: notFound().
     */
    public function dispatch(string $uri, string $method): void
    {
        $uri = $this->normalize($uri);

        if (!isset($this->routes[$method])) {
            $this->notFound($uri);
            return;
        }

        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                call_user_func_array($route['handler'], $matches);
                return;
            }
        }

        $this->notFound($uri);
    }

    /**
     * compilePattern()
     *
     * Doel:
     * Zet routes met {id} om naar een regex pattern.
     *
     * Voorbeeld:
     * - /posts/{id} -> #^/posts/(\d+)$#
     *
     * Beperking in deze les:
     * - {id} matcht enkel cijfers.
     */
    private function compilePattern(string $path): string
    {
        $path = $this->normalize($path);

        $pattern = preg_replace('/\{(\w+)\}/', '(\d+)', $path);

        return '#^' . $pattern . '$#';
    }

    /**
     * normalize()
     *
     * Doel:
     * Zorgt dat elk pad dezelfde vorm heeft:
     * - altijd leading slash
     * - geen trailing slash
     * - lege string wordt "/"
     */
    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }

    /**
     * notFound()
     *
     * Doel:
     * Wordt uitgevoerd wanneer geen route matcht.
     *
     * Werking:
     * - Als er een notFoundHandler is ingesteld: roep die aan.
     * - Anders: toon een simpele 404 (fallback).
     */
    private function notFound(string $requestedUri): void
    {
        if (is_callable($this->notFoundHandler)) {
            call_user_func($this->notFoundHandler, $requestedUri);
            return;
        }

        http_response_code(404);
        echo '<h1>404 - Pagina niet gevonden</h1>';
    }
}
