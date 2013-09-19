<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_ParentPageComposerTargetType extends ComposerTargetType {

	public function configureComposerTarget(Composer $cm, $post) {
		$configuration = new ParentPageComposerTargetConfiguration($this);
		$configuration->setParentPageID($post['cParentID']);
		return $configuration;
	}

	public function configureComposerTargetFromImport($txml) {
		$configuration = new ParentPageComposerTargetConfiguration($this);
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