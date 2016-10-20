<?php
namespace Concrete\Core\Routing;

use Core;
use League\Url\Url;
use League\Url\UrlInterface;
use Request;
use Page;

class Redirect
{
    /**
     * Actually sends a redirect.
     */
    protected static function createRedirectResponse($url, $code, $headers)
    {
        $r = new RedirectResponse($url, $code, $headers);
        $r->setRequest(Request::getInstance());

        return $r;
    }

    /**
     * Redirects to a concrete5 resource.
     */
    public static function to()
    {
        // Is what we're providing a URL object?
        $args = func_get_args();
        if (is_object($args[0]) && $args[0] instanceof UrlInterface) {
            $url = $args[0];
        } else {
            $url = call_user_func_array('\URL::to', func_get_args());
        }
        $r = static::createRedirectResponse((string) $url, 302, array());

        return $r;
    }

    /**
     * Redirect to a page.
     */
    public static function page(Page $c, $code = 302, $headers = array())
    {
        if ($c->getCollectionPath()) {
            $url = Core::make('helper/navigation')->getLinkToCollection($c, true);
        } else {
            $url = \URL::to($c);
        }
        $r = static::createRedirectResponse((string) $url, $code, $headers);

        return $r;
    }

    /**
     * Redirects to a URL.
     */
    public static function url($url, $code = 302, $headers = array())
    {
        $r = static::createRedirectResponse((string) $url, $code, $headers);

        return $r;
    }
}
