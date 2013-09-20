<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Composer_Form {

	public function display(Composer $composer, $draft = false) {
		Loader::element('composer/form/output/form', array(
			'composer' => $composer,
			'draft' => $draft
		));
	}

	public function displayButtons(Composer $composer, $draft = false) {
		Loader::element('composer/form/output/buttons', array(
			'composer' => $composer,
			'draft' => $draft
		));
	}

	public function addAssetsToRequest(Composer $cmp, Controller $cnt) {
		$r = Request::get();
		$r->requireAsset('core/composer');
		$list = ComposerControl::getList($cmp);
		foreach($list as $l) {
			$l->addAssetsToRequest($cnt);
		}
	}


}