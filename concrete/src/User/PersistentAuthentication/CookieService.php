<?php

namespace Concrete\Core\User\PersistentAuthentication;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Cookie\CookieJar;

class CookieService
{
    /**
     * The name of the cookie to be used to remember the currently logged in user.
     *
     * @var string
     */
    const COOKIE_NAME = 'ccmAuthUserHash';

    /**
     * @var \Concrete\Core\Cookie\CookieJar
     */
    protected $cookieJar;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Cookie\CookieJar $cookieJar
     * @param \Concrete\Core\Config\Repository\Repository $config
     */
    public function __construct(CookieJar $cookieJar, Repository $config)
    {
        $this->cookieJar = $cookieJar;
        $this->config = $config;
    }

    /**
     * Get the authentication data corrently contained in the cookie jar.
     *
     * @return \Concrete\Core\User\PersistentAuthentication\CookieValue|null
     */
    public function getCookie()
    {
        if (!$this->cookieJar->has(static::COOKIE_NAME)) {
            return null;
        }
        $rawValue = $this->cookieJar->get(static::COOKIE_NAME);

        return $this->unserializeCookieValue($rawValue);
    }

    /**
     * Set (or delete) the authentication cookie.
     *
     * @param \Concrete\Core\User\PersistentAuthentication\CookieValue|null $value
     */
    public function setCookie(CookieValue $value = null)
    {
        if ($value === null) {
            $this->deleteCookie();

            return;
        }
        $this->cookieJar->getResponseCookies()->addCookie(
            // $name
            static::COOKIE_NAME,
            // $value
            $this->serializeCookieValue($value),
            // $expire
            time() + (int) $this->config->get('concrete.session.remember_me.lifetime'),
            // $path
            DIR_REL . '/',
            // $domain
            $this->config->get('concrete.session.cookie.cookie_domain'),
            // $secure
            $this->config->get('concrete.session.cookie.cookie_secure'),
            // $httpOnly
            $this->config->get('concrete.session.cookie.cookie_httponly'),
            // $raw
            $this->config->get('concrete.session.cookie.cookie_raw'),
            // $sameSite
            $this->config->get('concrete.session.cookie.cookie_samesite')
        );
    }

    /**
     * Delete the authentication cookie.
     */
    public function deleteCookie()
    {
        $this->cookieJar->getResponseCookies()->clear(static::COOKIE_NAME);
    }

    /**
     * @param \Concrete\Core\User\PersistentAuthentication\CookieValue $value
     *
     * @return string
     */
    protected function serializeCookieValue(CookieValue $value)
    {
        return implode(':', [$value->getUserID(), $value->getAuthenticationTypeHandle(), $value->getToken()]);
    }

    /**
     * @param string|mixed $rawValue
     *
     * @return \Concrete\Core\User\PersistentAuthentication\CookieValue|null
     */
    protected function unserializeCookieValue($rawValue)
    {
        if (!is_string($rawValue)) {
            return null;
        }
        $chunks = explode(':', $rawValue);
        if (count($chunks) !== 3) {
            return null;
        }
        list($userID, $authenticationTypeHandle, $token) = $chunks;
        if (($userID = (int) $userID) < 1 || $authenticationTypeHandle === '') {
            return null;
        }

        return new CookieValue($userID, $authenticationTypeHandle, $token);
    }
}
