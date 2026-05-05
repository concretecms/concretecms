<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Cache\Level\ExpensiveCache;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Early404Middleware implements MiddlewareInterface, ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var \Concrete\Core\Routing\Router
     */
    private $router;

    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    private $connection;

    /**
     * @var ExpensiveCache
     */
    private $cache;

    public function __construct(ExpensiveCache $cache, Router $router, Connection $connection)
    {
        $this->cache = $cache;
        $this->router = $router;
        $this->connection = $connection;
    }

    public function process(Request $request, DelegateInterface $frame)
    {
        $pathInfo = rawurldecode($request->getPathInfo());
        if ($this->containsPathTraversal($pathInfo)) {
            return $frame->next($request);
        }

        $firstSegment = $this->getFirstSegment($pathInfo);
        if ($firstSegment === null) {
            return $frame->next($request);
        }

        if ($this->hasPotentialRouteForFirstSegment($firstSegment)) {
            return $frame->next($request);
        }

        if ($this->hasPotentialPagePathForFirstSegment($firstSegment)) {
            return $frame->next($request);

        }

        // Note: I would love to typehint the ResponseFactoryInterface into this class,
        // but doing so causes problems because it builds this class too early, and
        // for some reason doesn't include the header/footer when outputting the 404 (?!?)
        return $this->app->make(ResponseFactoryInterface::class)->cachedNotFound();
    }

    private function containsPathTraversal(string $path): bool
    {
        return substr($path, 0, 3) === '../'
            || substr($path, -3) === '/..'
            || strpos($path, '/../') !== false
            || substr($path, 0, 3) === '..\\'
            || substr($path, -3) === '\\..'
            || strpos($path, '\\..\\') !== false;
    }

    private function getFirstSegment(string $pathInfo): ?string
    {
        $pathInfo = trim($pathInfo, '/');
        if ($pathInfo === '') {
            return null;
        }

        $segments = explode('/', $pathInfo, 2);

        return '/' . $segments[0];
    }

    private function hasPotentialRouteForFirstSegment(string $firstSegment): bool
    {
        $pattern = '#^' . preg_quote($firstSegment, '#') . '(?:/|$)#';
        foreach ($this->router->getRoutes()->all() as $route) {
            $routePath = $route->getPath();
            if (strpos($routePath, '/{') === 0) {
                continue;
            }
            if (preg_match($pattern, $routePath) === 1) {
                return true;
            }
        }

        return false;
    }

    private function hasPotentialPagePathForFirstSegment(string $firstSegment): bool
    {
        $result = $this->connection->fetchOne(
            'select 1 from PagePaths where cPath = ? or cPath like ? limit 1',
            [$firstSegment, $firstSegment . '/%']
        );

        return $result !== false && $result !== null;
    }
}
