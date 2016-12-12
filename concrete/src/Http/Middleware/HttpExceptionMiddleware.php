<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Http\Exception\ForbiddenException;
use Concrete\Core\Http\Exception\RedirectException;
use Concrete\Core\Http\Exception\UserFacingException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class HttpExceptionMiddleware implements MiddlewareInterface
{

    /**
     * @var \Concrete\Core\Http\ResponseFactoryInterface
     */
    private $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
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
        // Attempt to load the next frame
        try {
            return $frame->next($request);
        } catch (RedirectException $e) {
            // Redirect
            return $this->handleRedirect($e);
        } catch (ForbiddenException $e) {
            // Forbidden
            return $this->handleForbidden($e, $request);
        } catch (UserFacingException $e) {
            // Generic error to output
            return $this->handleUserFacingException($e);
        }
    }

    private function handleRedirect(RedirectException $e)
    {
        return $this->factory->redirect($e->getRedirectUrl(), $e->getStatus(), $e->getHeaders());
    }

    private function handleForbidden(ForbiddenException $e, Request $request)
    {
        return $this->factory->forbidden($request->getRequestUri(), $e->getStatus(), $e->getHeaders());
    }

    private function handleUserFacingException(UserFacingException $e)
    {
        $error = (object) [
            'content' => $e->getMessage(),
            'title' => $e->getTitle()
        ];

        return $this->factory->error($error, $e->getStatus(), $e->getHeaders());
    }

}
