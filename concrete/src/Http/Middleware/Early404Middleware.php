<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class Early404Middleware implements MiddlewareInterface
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    private $app;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    private $config;

    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Http\Middleware\MiddlewareInterface::process()
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        return $this->handle($request) ?? $frame->next($request);
    }

    private function handle(Request $request): ?Response
    {
        if (!$this->app->isInstalled()) {
            return null;
        }
        if (!$this->config->get('concrete.early404.enabled')) {
            return null;
        }
        $regexes = (string) $this->config->get('concrete.early404.regexes');
        if (!$regexes) {
            return null;
        }
        $pi = $request->getPathInfo();
        foreach (preg_split('/[\r\n]+/', $regexes, -1, \PREG_SPLIT_NO_EMPTY) as $regex) {
            if (preg_match($regex, $pi)) {
                return $this->buildEarly404Response($request);
            }
        }

        return null;
    }

    private function buildEarly404Response(Request $request): Response
    {
        return $this->app->make(ResponseFactoryInterface::class)->create('404', Response::HTTP_NOT_FOUND, ['Content-Type' => 'text/plain']);
    }
}
