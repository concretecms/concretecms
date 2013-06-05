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
		$cnt->addFooterItem(Loader::helper('html')->javascript('ccm.composer.js'));
		$cnt->addHeaderItem(Loader::helper('html')->css('ccm.composer.css'));
		$cnt->addHeaderItem(Loader::helper('html')->css('jquery.ui.css'));
		$cnt->addFooterItem(Loader::helper('html')->javascript('jquery.ui.js'));

		$list = ComposerControl::getList($cmp);
		foreach($list as $l) {
			$l->addAssetsToRequest($cnt);
		}
	}


}