<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_ParentPageComposerTargetType extends ComposerTargetType {

	public function configureComposerTarget(Composer $cm, $post) {
		$configuration = new ParentPageComposerTargetConfiguration($this);
		$configuration->setParentPageID($post['cParentID']);
		return $configuration;
	}
	
}