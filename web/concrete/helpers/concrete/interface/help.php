<?
defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteInterfaceHelpHelper {

	public function getBlockTypes() {
		$blockTypes = array(
			'autonav' => t('Auto-nav is great!'),
			'content' => t('Content block is great!')
			
		);
		
		return $blockTypes;
	}
	
	public function getPages() {
		$pages = array(
			'/dashboard/composer/write' => t('Write some stuff for composer.'),
			'/dashboard/composer/drafts' => t('Composer drafts.')
		);
		return $pages;
	}

}