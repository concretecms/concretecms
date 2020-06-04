<?php

namespace Concrete\Core\Cookie;

use Symfony\Component\HttpFoundation\Cookie;

class ResponseCookieJar
{
    /**
     * The list of new cookies to be added to the response.
     *
     * @var \Symfony\Component\HttpFoundation\Cookie[]
     */
    protected $cookies = [];

    /**
     * The names of the request cookies to be cleared out in response.
     *
     * @var string[]
     */
    protected $clearedCookies = [];

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
     * @param bool $raw Whether the cookie value should be sent with no url encoding
     * @param string|null $sameSite Whether the cookie will be available for cross-site requests
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function addCookie($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true, $raw = false, $sameSite = null)
    {
        $cookie = new Cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
        $this->addCookieObject($cookie);

        return $cookie;
    }

    /**
     * Adds a Cookie object to the array of cookies for the object.
     *
     * @param \Symfony\Component\HttpFoundation\Cookie $cookie
     *
     * @return $this
     */
    public function addCookieObject(Cookie $cookie)
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

        return $this;
    }

    /**
     * The list of new cookies to be added to the response.
     *
     * @return \Symfony\Component\HttpFoundation\Cookie[]
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Get the response cookie given its name.
     *
     * @param string $name The key the cookie is stored under
     *
     * @return \Symfony\Component\HttpFoundation\Cookie|null
     */
    public function getCookieByName($name)
    {
        foreach ($this->cookies as $cookie) {
            if ($cookie->getName() === $name) {
                return $cookie;
            }
        }

        return null;
    }

    /**
     * There's a cookie with the specific name in the response cookies?
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasCookie($name)
    {
        return $this->getCookieByName($name) !== null;
    }

    /**
     * The names of the request cookies to be cleared out in response.
     *
     * @return string[]
     */
    public function getClearedCookies()
    {
        return $this->clearedCookies;
    }

    /**
     * Clear a cookie.
     *
     * @param string $name
     *
     * @return $this
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
}
