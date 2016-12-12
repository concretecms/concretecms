<?php
namespace Concrete\Controller\Backend\UserInterface;

use Exception;
use Page as ConcretePage;
use Permissions;

abstract class Page extends \Concrete\Controller\Backend\UserInterface
{

    /** @var ConcretePage A page object */
    protected $page;

    /** @var Permissions This page's permissions */
    protected $permissions;

    public function on_start()
    {
        $request = $this->request;
        $cID = $request->query->get('cID');
        if ($cID) {
            $page = ConcretePage::getByID($cID);
        }
        if (is_object($page) && !$page->isError()) {
            $this->setPageObject($page);
            $request->setCurrentPage($this->page);
        } else {
            throw new \Exception(t('Access Denied'));
        }
    }

    public function setPageObject(ConcretePage $c)
    {
        $this->page = $c;
        $this->permissions = new Permissions($this->page);
        $this->set('c', $this->page);
        $this->set('cp', $this->permissions);
    }

    public function action()
    {
        if ($this->page->getCollectionPointerOriginalID()) {
            $cID = $this->page->getCollectionPointerOriginalID();
        } else {
            $cID = $this->page->getCollectionID();
        }
        $url = call_user_func_array('parent::action', func_get_args());
        $url .= '&cID=' . $cID;
        return $url;
    }

}
