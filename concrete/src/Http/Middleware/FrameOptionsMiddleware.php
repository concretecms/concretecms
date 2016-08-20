<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Utility\Service\Validation\Strings;

class FrameOptionsMiddleware implements MiddlewareInterface
{

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    private $config;

    /**
     * @var \Concrete\Core\Utility\Service\Validation\Strings
     */
    private $stringValidator;

    public function __construct(Repository $config, Strings $stringValidator)
    {
        $this->config = $config;
        $this->stringValidator = $stringValidator;
    }

    public function process(Request $request, callable $next)
    {
        $response = $next($request);

        if ($response->headers->has('X-Frame-Options') === false) {
            $x_frame_options = $this->config->get('concrete.security.misc.x_frame_options');
            if ($this->stringValidator->notempty($x_frame_options)) {
                $this->headers->set('X-Frame-Options', $x_frame_options);
            }
        }

        return $response;
    }

}
