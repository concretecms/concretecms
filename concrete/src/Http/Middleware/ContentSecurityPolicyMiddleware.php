<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Utility\Service\Validation\Strings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicyMiddleware implements MiddlewareInterface
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

        if ($response->headers->has('Content-Security-Policy') === false) {
            $csp = $this->config->get('concrete.security.misc.content_security_policy');
            if ((is_array($csp) && count($csp) > 0) || $this->stringValidator->notempty($csp)) {
                $response->headers->set('Content-Security-Policy', $csp);
            }
        }

        return $response;
    }
}
