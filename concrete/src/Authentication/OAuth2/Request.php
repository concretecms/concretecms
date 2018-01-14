<?php

namespace Concrete\Core\Authentication\OAuth2;

use OAuth2\RequestInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * An oauth request implementation that simply wraps the core request
 */
class Request implements RequestInterface
{

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    public function __construct(SymfonyRequest $request)
    {
        $this->request = $request;
    }

    public function query($name, $default = null)
    {
        return $this->request->query->get($name, $default);
    }

    public function request($name, $default = null)
    {
        return $this->request->request->get($name, $default);
    }

    public function server($name, $default = null)
    {
        return $this->request->server->get($name, $default);
    }

    public function headers($name, $default = null)
    {
        return $this->request->headers->get($name, $default);
    }

    public function getAllQueryParameters()
    {
        return $this->request->query->all();
    }


}
