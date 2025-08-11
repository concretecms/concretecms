<?php

namespace Concrete\Core\Http\Middleware;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Scope;
use League\Fractal\Serializer\DataArraySerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FractalNegotiatorMiddleware implements MiddlewareInterface
{

    protected $serializer;

    /**
     * @var \League\Fractal\Manager
     */
    private $fractal;

    public function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;
        $request = Request::createFromGlobals();
        if ($request->query->has('includes')) {
            $this->fractal->parseIncludes($request->query->get('includes'));
        }
    }

    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @param DataArraySerializer $serializer
     */
    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
    }

    private function getDefaultSerializer()
    {
        $url = rtrim(app('site')->getSite()->getSiteCanonicalURL(), '/');
        $serializer = new DataArraySerializer();
        return $serializer;
    }

    /**
     * Process the request and return a response
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param DelegateInterface $frame
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $response = $frame->next($request);

        $serializer = $this->getSerializer();
        if (!$serializer) {
            $serializer = $this->getDefaultSerializer();
        }
        $this->fractal->setSerializer($serializer);

        // Handle a Resource
        if ($response instanceof ResourceInterface) {
            $response = $this->fractal->createData($response);
        }

        // Handle outputting a scope
        if ($response instanceof Scope) {
            $json = $response->toJson();

            // Build a new Json response
            return JsonResponse::fromJsonString($json);
        }

        return $response;
    }
}
