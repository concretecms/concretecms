<?
defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteInterfaceHelpHelper {

	public function getBlockTypes() {
		$blockTypes = array(
			'autonav' => t('AutoNav is great!')
		);
		
		return $blockTypes;
	}

}