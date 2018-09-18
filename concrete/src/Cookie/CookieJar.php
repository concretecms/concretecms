<?php

namespace Concrete\Core\Cookie;

use Concrete\Core\Http\Request;

/**
 * A class that holds operations performed on both request and response cookies.
 *
 * To work only on request cookies, use the Request class.
 * To work only on response cookies, use the ResponseCookieJar class.
 */
class CookieJar
{
    /**
     * The object containing the request cookies.
     *
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    /**
     * The object containing the response cookies.
     *
     * @var \Concrete\Core\Cookie\ResponseCookieJar
     */
    protected $responseCookies;

    /**
     * Initialize the instance.
     *
     * @param Request $request the object containing the request cookies
     * @param ResponseCookieJar $responseCookies the object containing the response cookies
     */
    public function __construct(Request $request, ResponseCookieJar $responseCookies)
    {
        $this->setRequest($request);
        $this->responseCookies = $responseCookies;
    }

    /**
     * Get the object containing the response cookies.
     *
     * @return \Concrete\Core\Cookie\ResponseCookieJar
     */
    public function getResponseCookies()
    {
        return $this->responseCookies;
    }

    /**
     * Does a cookie exist in the request or response cookies?
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        if (in_array($name, $this->responseCookies->getClearedCookies(), true)) {
            return false;
        }

        return $this->request->cookies->has($name) || $this->responseCookies->hasCookie($name);
    }

    /**
     * Get the value of a cookie (from response or from request) given its name.
     *
     * @param string $name The key the cookie
     * @param mixed $default The value to return if the cookie isn't set
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $responseCookie = $this->responseCookies->getCookieByName($name);
        if ($responseCookie !== null) {
            return $responseCookie->getValue();
        }
        if (in_array($name, $this->responseCookies->getClearedCookies(), true)) {
            return $default;
        }

        return $this->getRequest()->cookies->get($name, $default);
    }

    /**
     * Get a list of cookie names and values (both from response and from request).
     *
     * @return array array keys are the cookie names, array values are the cookie values
     */
    public function getAll()
    {
        $result = [];
        $clearedRequestCookies = $this->responseCookies->getClearedCookies();
        foreach ($this->request->cookies->all() as $name => $value) {
            if (!in_array($name, $clearedRequestCookies, true)) {
                $result[$name] = $value;
            }
        }
        foreach ($this->responseCookies->getCookies() as $cookie) {
            if ($cookie->getExpiresTime() !== 0 && $cookie->isCleared()) {
                unset($result[$cookie->getName()]);
            } else {
                $result[$cookie->getName()] = $cookie->getValue();
            }
        }

        return $result;
    }

    /**
     * Set the request for this cookie jar.
     *
     * @param \Concrete\Core\Http\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @deprecated Use ->getResponseCookies()->addCookie() or $app->make(ResponseCookieJar::class)->addCookie()
     *
     * @param string $name
     * @param string|null $value
     * @param int $expire
     * @param string $path
     * @param null|string $domain
     * @param bool $secure
     * @param bool $httpOnly
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function set($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
    {
        return $this->responseCookies->addCookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * @deprecated Use ->getResponseCookies()->addCookieObject() or $app->make(ResponseCookieJar::class)->addCookieObject()
     *
     * @param \Symfony\Component\HttpFoundation\Cookie $cookie
     */
    public function add($cookie)
    {
        $this->responseCookies->addCookieObject($cookie);
    }

    /**
     * @deprecated Use ->getResponseCookies()->clear() or $app->make(ResponseCookieJar::class)->clear()
     *
     * @param string $name
     */
    public function clear($name)
    {
        $this->responseCookies->clear($name);
    }

    /**
     * @deprecated Use ->getResponseCookies()->getCookies() or $app->make(ResponseCookieJar::class)->getCookies()
     *
     * @return \Symfony\Component\HttpFoundation\Cookie[]
     */
    public function getCookies()
    {
        return $this->responseCookies->getCookies();
    }

    /**
     * Get the request for this cookie jar.
     *
     * @return \Concrete\Core\Http\Request
     */
    protected function getRequest()
    {
        return $this->request;
    }
}
