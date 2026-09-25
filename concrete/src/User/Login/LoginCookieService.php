<?php

namespace Concrete\Core\User\Login;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Cookie\CookieJar;

defined('C5_EXECUTE') or die('Access Denied.');

class LoginCookieService
{

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var CookieJar
     */
    protected $cookieJar;

    public function __construct(CookieJar $cookieJar, Repository $config)
    {
        $this->cookieJar = $cookieJar;
        $this->config = $config;
    }

    public function hasLoginCookie(): bool
    {
        $loginCookie = sprintf('%s_LOGIN', $this->config->get('concrete.session.name'));
        return $this->cookieJar->has($loginCookie) && $this->cookieJar->get($loginCookie) === '1';
    }

}
