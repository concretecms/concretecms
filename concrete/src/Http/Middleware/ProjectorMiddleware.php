<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\Middleware\DelegateInterface;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Concrete\Core\Http\ResponseFactory;
use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Project items for output
 */
class ProjectorMiddleware implements MiddlewareInterface
{

    /**
     * @var \Concrete\Core\Application\Application
     */
    private $app;
    /**
     * @var \Concrete\Core\Http\ResponseFactory
     */
    private $factory;

    public function __construct(Application $app, ResponseFactory $factory)
    {
        $this->app = $app;
        $this->factory = $factory;
    }

    /**
     * Process the request and return a response
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param DelegateInterface $frame
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $result = $frame->next($request);

        if ($result instanceof ResourceInterface) {
            $fractal = $this->app->make(Manager::class);
            $data = $fractal->createData($result);

            /** @var \Symfony\Component\HttpFoundation\JsonResponse $result */
            $result = $this->factory->json($data->toArray(), 200);

            if (!$result) {
                return $this->factory->create('{}', 404);
            }

            if ($result === true) {
                return $this->factory->create('{}', 200);
            }

        }
        return $result;
    }
}
