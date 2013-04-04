<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Composer_Form {

	public function display(Composer $composer, $draft = false) {
		Loader::element('composer/form/output/form', array(
			'composer' => $composer,
			'draft' => $draft
		));
	}

	public function displayButtons() {
		Loader::element('composer/form/output/buttons');
	}

}