<?
namespace Concrete\Controller\Panel;
use \Concrete\Controller\Backend\UI\Page as BackendInterfacePageController;
use Loader;
use PageType;
use Page;
use Permissions;
class Sitemap extends BackendInterfacePageController {

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

