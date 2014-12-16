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
        $cleared = \Cookie::getClearedCookies();
        foreach($cleared as $cookie) {
            $this->headers->clearCookie($cookie);
        }
        // First, we see if we have any cookies to send along
        $cookies = \Cookie::getCookies();
        foreach ($cookies as $cookie) {
            $this->headers->setCookie($cookie);
        }
        parent::send();
    }

}
