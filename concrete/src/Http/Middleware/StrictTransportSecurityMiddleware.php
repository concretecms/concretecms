<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Utility\Service\Validation\Strings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StrictTransportSecurityMiddleware implements MiddlewareInterface
{
    /**
     * @var Repository
     */
    private $config;

    /**
     * @var Strings
     */
    private $stringValidator;

    public function __construct(Repository $config, Strings $stringValidator)
    {
        $this->config = $config;
        $this->stringValidator = $stringValidator;
    }

    /**
     * @param Request $request
     * @param DelegateInterface $frame
     *
     * @return Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $response = $frame->next($request);

        if ($response->headers->has('Strict-Transport-Security') === false) {
            $x_frame_options = $this->config->get('concrete.security.misc.strict_transport_security');
            if ($this->stringValidator->notempty($x_frame_options)) {
                $response->headers->set('Strict-Transport-Security', $x_frame_options);
            }
        }

        return $response;
    }
}
