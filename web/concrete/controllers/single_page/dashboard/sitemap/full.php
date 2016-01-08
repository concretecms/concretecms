<?php
namespace Concrete\Controller\SinglePage\Dashboard\Sitemap;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
class Full extends DashboardPageController {

	public function view() {
		$this->requireAsset('core/sitemap');
		$dh = Loader::helper('concrete/dashboard/sitemap');
		$this->set('includeSystemPages', $dh->includeSystemPages());

	}
}