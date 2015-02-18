<?php
namespace Concrete\Controller\Panel\Detail\Page;
use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use PageEditResponse;
use PageCache;

class Caching extends BackendInterfacePageController {

	protected $viewPath = '/panels/details/page/caching';

	protected function canAccess() {
		return $this->permissions->canEditPageSpeedSettings();
	}

	public function view() {

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