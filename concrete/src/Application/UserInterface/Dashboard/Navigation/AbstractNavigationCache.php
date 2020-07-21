<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Symfony\Component\HttpFoundation\Session\Session;

abstract class AbstractNavigationCache
{

    abstract public function getIdentifier(): string;

    /**
     * @var Session
     */
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function has()
    {
        return $this->session->has($this->getIdentifier());
    }

    public function set(Navigation $navigation)
    {
        $this->session->set($this->getIdentifier(), $navigation);
    }

    public function get()
    {
        return $this->session->get($this->getIdentifier());
    }

    public function clear()
    {
        $this->session->remove($this->getIdentifier());
    }

}
