<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Navigation\Navigation as BaseNavigation;
use Symfony\Component\HttpFoundation\Session\Session;

class NavigationCache
{

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
        return $this->session->has('dashboard_menu');
    }

    public function set(Navigation $navigation)
    {
        $this->session->set('dashboard_menu', $navigation);
    }

    public function get()
    {
        return $this->session->get('dashboard_menu');
    }


}
