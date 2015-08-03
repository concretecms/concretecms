<?php
namespace Concrete\Controller\Panel\Page;

use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Permissions;
use Page;
use stdClass;
use PermissionKey;
use PageCache;
use PageEditResponse;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;

class Caching extends BackendInterfacePageController
{

    protected $viewPath = '/panels/page/caching';

    public function canAccess()
    {
        return $this->permissions->canEditPageSpeedSettings();
    }

    public function view()
    {
        $blocks = $this->page->getBlocks();
        $blocks = array_merge($this->page->getGlobalBlocks(), $blocks);

        $this->set('blocks', $blocks);
    }

    public function purge() {
        $cache = PageCache::getLibrary();
        $cache->purge($this->page);
        $r = new PageEditResponse();
        $r->setPage($this->page);
        $r->setTitle(t('Page Updated'));
        $r->setMessage(t('This page has been purged from the full page cache.'));
        $r->outputJSON();
    }

    public function submit() {
        if ($this->validateAction()) {
            $data = array();
            $data['cCacheFullPageContent'] = $_POST['cCacheFullPageContent'];
            $data['cCacheFullPageContentLifetimeCustom'] = $_POST['cCacheFullPageContentLifetimeCustom'];
            $data['cCacheFullPageContentOverrideLifetime'] = $_POST['cCacheFullPageContentOverrideLifetime'];               
            $this->page->update($data);
            $r = new PageEditResponse();
            $r->setPage($this->page);
            $r->setTitle(t('Page Updated'));
            $r->setMessage(t('Full page caching settings saved.'));
            $r->outputJSON();
        }
    }
}
