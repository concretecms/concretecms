<?php
namespace Concrete\Controller\Backend\UserInterface;

use Page as ConcretePage;
use Permissions;
use Concrete\Core\Area\Area;

abstract class Page extends \Concrete\Controller\Backend\UserInterface
{
    /** @var ConcretePage A page object */
    protected $page;

    /** @var Permissions This page's permissions */
    protected $permissions;

    /**
     * Stack permissions (only if the current "page" is a stack).
     *
     * @var \Concrete\Core\Permission\Checker|null
     */
    protected $stackPermissions;

    public function on_start()
    {
        $request = $this->request;
        $cID = $request->query->get('cID');
        if (!$cID) {
            $cID = $request->request->get('cID');
        }
        if ($cID) {
            $page = ConcretePage::getByID($cID);
        } else {
            $page = null;
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
        if (strpos($c->getCollectionPath(), STACKS_PAGE_PATH . '/') === 0) {
            $this->stackPermissions = new Permissions(Area::get($this->page, STACKS_AREA_NAME));
        } else {
            $this->stackPermissions = null;
        }
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
