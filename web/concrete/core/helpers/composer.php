<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Composer {

	public function display(PageType $pagetype, $page = false) {
		Loader::element('page_types/composer/form/output/form', array(
			'pagetype' => $pagetype,
			'page' => $page
		));
	}

	public function displayButtons(PageType $pagetype, $page = false) {
		Loader::element('page_types/composer/form/output/buttons', array(
			'pagetype' => $pagetype,
			'page' => $page
		));
	}

	public function addAssetsToRequest(PageType $pt, Controller $cnt) {
		$r = Request::get();
		$r->requireAsset('core/composer');
		$list = PageTypeComposerControl::getList($pt);
		foreach($list as $l) {
			$l->addAssetsToRequest($cnt);
		}
	}


}