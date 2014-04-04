<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Details_Page_Caching extends BackendInterfacePageController {

	protected $viewPath = '/system/panels/details/page/caching';

	protected function canAccess() {
		return $this->permissions->canEditPageSpeedSettings();
	}

	public function view() {

	}

	public function purge() {
		$cache = PageCache::getLibrary();
		$cache->purge($this->page);
		$r = new PageEditVersionResponse();
		$r->setPage($this->page);
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
			$r = new PageEditVersionResponse();
			$r->setPage($this->page);
			$r->setMessage(t('Cache settings saved.'));
			$r->outputJSON();
		}
	}


}