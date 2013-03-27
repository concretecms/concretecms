<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PageTypeComposerTargetType extends ComposerTargetType {


	public function configureComposerTarget(Composer $cm, $post) {
		$configuration = new PageTypeComposerTargetConfiguration($this);
		$configuration->setCollectionTypeID($post['ctID']);
		return $configuration;
	}

	
}