<?
namespace Concrete\Controller\Panel;
use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Loader;
use PageType;
use Page;
use Permissions;
class Sitemap extends BackendInterfacePageController {

	protected $viewPath = '/panels/sitemap';
	protected $frequentPageTypes = array();
    protected $otherPageTypes = array();

	protected function canAccess() {
		return $this->canViewSitemap || count($this->pagetypes) > 0;
	}

	public function view() {
		$this->requireAsset('core/sitemap');
		$sh = Loader::helper('concrete/dashboard/sitemap');
		$this->canViewSitemap = $sh->canRead();

		$frequentlyUsed = PageType::getFrequentlyUsedList();
		foreach($frequentlyUsed as $pt) {
			$ptp = new Permissions($pt);
			if ($ptp->canAddPageType()) {
				$this->frequentPageTypes[] = $pt;
			}
		}

        $otherPageTypes = PageType::getInfrequentlyUsedList();
        foreach($otherPageTypes as $pt) {
            $ptp = new Permissions($pt);
            if ($ptp->canAddPageType()) {
                $this->otherPageTypes[] = $pt;
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

		$this->set('frequentPageTypes', $this->frequentPageTypes);
        $this->set('otherPageTypes', $this->otherPageTypes);
		$this->set('drafts', $mydrafts);
		$this->set('canViewSitemap', $this->canViewSitemap);
	}

}

