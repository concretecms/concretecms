<?
defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteInterfaceHelpHelper {

	public function getBlockTypes() {
		$blockTypes = array(
			'autonav' => array(t('Auto-nav is great!'), 'http://www.concrete5.org/documentation/editors-guide/in-page-editing/block-areas/add-block/auto-nav/'),
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