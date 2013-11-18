<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Sitemap extends BackendInterfacePageController {

	protected $viewPath = '/system/panels/sitemap';
	protected $pagetypes = array();

	protected function canAccess() {
		return $this->canViewSitemap || count($this->pagetypes) > 0;
	}

	public function view() {
		$this->requireAsset('core/sitemap');
		$sh = Loader::helper('concrete/dashboard/sitemap');
		$this->canViewSitemap = $sh->canRead();

		$pagetypes = PageType::getList();
		foreach($pagetypes as $pt) {
			$ptp = new Permissions($pt);
			if ($ptp->canComposePageType()) {
				$this->pagetypes[] = $pt;
			}
		}

		$drafts = Page::getDrafts();
		$mydrafts = array();
		foreach($drafts as $d) {
			$dp = new Permissions($d);
			if ($dp->canEditPageContents()) {
				$mydrafts[] = $d;
			}
		}

		$this->set('pagetypes', $this->pagetypes);
		$this->set('drafts', $mydrafts);
		$this->set('canViewSitemap', $this->canViewSitemap);
	}

}

