<?php
namespace Concrete\Core\Routing;

class RedirectResponse extends \Symfony\Component\HttpFoundation\RedirectResponse
{
    protected $request;

    public function __construct($url, $status = 302, $headers = array())
    {
        $url = (string) $url; // sometimes we get an object.
        parent::__construct($url, $status, $headers);
    }

}
