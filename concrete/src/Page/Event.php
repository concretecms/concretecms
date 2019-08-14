<?php
namespace Concrete\Core\Page;

use Symfony\Component\EventDispatcher\GenericEvent;
use User;
use Symfony\Component\HttpFoundation\Request;
use Concrete\Core\Http\RequestEventInterface;

class Event extends GenericEvent implements RequestEventInterface
{
    protected $page;
    protected $user;
    /**
     * @since 5.7.4
     */
    protected $request;

    public function __construct(Page $c)
    {
        $this->page = $c;
    }

    public function setUser(User $u)
    {
        $this->user = $u;
    }

    /**
     * @param Request $request
     * @since 5.7.4
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /** @return \Symfony\Component\HttpFoundation\Request
     * @since 5.7.4
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function getPageObject()
    {
        return $this->page;
    }

    public function getUserObject()
    {
        return $this->user;
    }
}
