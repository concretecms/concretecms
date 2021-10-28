<?php

namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Localization\Localization;
use Symfony\Component\HttpFoundation\Session\Session;

abstract class AbstractNavigationCache
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \Concrete\Core\Localization\Localization
     */
    protected $localization;

    public function __construct(Session $session, Localization $localization)
    {
        $this->session = $session;
        $this->localization = $localization;
    }

    abstract public function getIdentifier(): string;

    public function has()
    {
        return $this->session->has($this->getSessionIdentifier());
    }

    public function set(Navigation $navigation): void
    {
        $this->session->set($this->getSessionIdentifier(), $navigation);
    }

    public function get()
    {
        return $this->session->get($this->getSessionIdentifier());
    }

    public function clear(): void
    {
        $prefix = $this->getIdentifier() . '@';
        foreach (array_keys($this->session->all()) as $sessionKey) {
            if (strpos($sessionKey, $prefix) === 0) {
                $this->session->remove($sessionKey);
            }
        }
    }

    protected function getSessionIdentifier(): string
    {
        return $this->getIdentifier() . '@' . $this->localization->getLocale();
    }
}
