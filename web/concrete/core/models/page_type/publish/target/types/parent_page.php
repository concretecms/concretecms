<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_ParentPagePageTypePublishTargetType extends PageTypePublishTargetType {

	public function configurePageTypePublishTarget(PageType $pt, $post) {
		$configuration = new ParentPagePageTypePublishTargetConfiguration($this);
		$configuration->setParentPageID($post['cParentID']);
		return $configuration;
	}

	public function configurePageTypePublishTargetFromImport($txml) {
		$configuration = new ParentPagePageTypePublishTargetConfiguration($this);
		$path = (string) $txml['path'];
		if (!$path) {
			$c = Page::getByID(HOME_CID);
		} else {
			$c = Page::getByPath($path);
		}
		$configuration->setParentPageID($c->getCollectionID());
		return $configuration;
	}
	
}