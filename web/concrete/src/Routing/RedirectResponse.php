<?php
namespace Concrete\Core\Routing;

use Request;

class RedirectResponse extends \Symfony\Component\HttpFoundation\RedirectResponse
{

    protected $request;

    public function setRequest(Request $r)
    {
        $this->request = $r;
    }

    public function send()
    {
        // First, we see if we have any cookies to send along
        $cl = \Cookie::getInstance();
        $cookies = $cl->getCookies();
        foreach ($cookies as $cookie) {
            $this->headers->setCookie($cookie);
        }
        parent::send();
    }

}
