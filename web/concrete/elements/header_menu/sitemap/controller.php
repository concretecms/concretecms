<?
defined('C5_EXECUTE') or die("Access Denied.");
class SitemapConcreteInterfaceMenuItemController extends ConcreteInterfaceMenuItemController {
	
	public function displayItem() {
		$u = new User();
		if ($u->isRegistered()) {
			if ($u->config('UI_SITEMAP')) {
				$sh = Loader::helper('concrete/dashboard/sitemap');
				return $sh->canRead();
			}
		}
		return false;
	}

}