<?php

namespace Concrete\Core\Cookie;

use Concrete\Core\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class CookieJar
{
    /**
     * The list of newly set cookies.
     *
     * @var \Symfony\Component\HttpFoundation\Cookie[]
     */
    protected $cookies = [];

    /**
     * The names of the cookies to be cleared out.
     *
     * @var string[]
     */
    protected $clearedCookies = [];

    /**
     * The request for this cookie jar.
     *
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    /**
     * Initialize the instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->setRequest($request);
    }

    /**
     * Adds a Cookie object to the cookie pantry.
     *
     * @param string $name The cookie name
     * @param string|null $value The value of the cookie
     * @param int $expire The number of seconds until the cookie expires
     * @param string $path The path for the cookie
     * @param null|string $domain The domain the cookie is available to
     * @param bool $secure whether the cookie should only be transmitted over a HTTPS connection from the client
     * @param bool $httpOnly Whether the cookie will be made accessible only through the HTTP protocol
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function set(
        $name,
        $value = null,
        $expire = 0,
        $path = '/',
        $domain = null,
        $secure = false,
        $httpOnly = true
        ) {
        $cookie = new Cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
        $this->add($cookie);

        return $cookie;
    }

    /**
     * Adds a Cookie object to the array of cookies for the object.
     *
     * @param \Symfony\Component\HttpFoundation\Cookie $cookie
     */
    public function add($cookie)
    {
        $name = $cookie->getName();
        $isNew = true;
        foreach ($this->cookies as $index => $value) {
            if ($value->getName() === $name) {
                $this->cookies[$index] = $cookie;
                $isNew = false;
            }
        }
        if ($isNew) {
            $index = array_search($name, $this->clearedCookies, true);
            if ($index !== false) {
                unset($this->clearedCookies[$index]);
                $this->clearedCookies = array_values($this->clearedCookies);
            }
            $this->cookies[] = $cookie;
        }
    }

    /**
     * Used to determine if the cookie key exists in the pantry.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        $result = false;
        if (!in_array($name, $this->clearedCookies, true)) {
            foreach ($this->getNewCookies() as $cookie) {
                if ($cookie->getName() === $name) {
                    $result = true;
                    break;
                }
            }
            if ($result === false) {
                $result = $this->getRequest()->cookies->has($name);
            }
        }

        return $result;
    }

    /**
     * Remove a cookie from the paintry.
     *
     * @param string $name
     */
    public function clear($name)
    {
        if (!in_array($name, $this->clearedCookies, true)) {
            $this->clearedCookies[] = $name;
            foreach ($this->cookies as $index => $value) {
                if ($value->getName() === $name) {
                    unset($this->cookies[$index]);
                }
            }
            $this->cookies = array_values($this->cookies);
        }
    }

    /**
     * Get the value of a cookie given its name.
     *
     * @param string $name The key the cookie is stored under
     * @param mixed $default The value to return if the cookie isn't set
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if ($this->has($name)) {
            $found = false;
            foreach ($this->getNewCookies() as $cookie) {
                if ($cookie->getName() === $name) {
                    $result = $cookie->getValue();
                    $found = true;
                }
            }
            if (!$found) {
                $result = $this->getRequest()->cookies->get($name);
            }
        } else {
            $result = $default;
        }

        return $result;
    }

    /**
     * Get a list of currently set cookie names and values.
     *
     * @return array array keys are the cookie names, array values are the cookie values
     */
    public function getAllCookies()
    {
        $result = [];
        foreach ($this->request->cookies->all() as $name => $value) {
            if (!in_array($name, $this->clearedCookies, true)) {
                $result[$name] = $value;
            }
        }
        foreach ($this->cookies as $cookie) {
            $result[$cookie->getName()] = $cookie->getValue();
        }

        return $result;
    }

    /**
     * Get the list of newly set cookies.
     *
     * @return \Symfony\Component\HttpFoundation\Cookie[]
     */
    public function getNewCookies()
    {
        return $this->cookies;
    }

    /**
     * Get the names of the cookies to be cleared out.
     *
     * @return string[]
     */
    public function getClearedCookies()
    {
        return $this->clearedCookies;
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
     * @deprecated use the getNewCookies() method
     *
     * @return \Symfony\Component\HttpFoundation\Cookie[]
     */
    public function getCookies()
    {
        return $this->cookies;
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
