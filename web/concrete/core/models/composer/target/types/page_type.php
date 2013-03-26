<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PageTypeComposerTargetType extends ComposerTargetType {


	public function configureComposerTarget(Composer $cm, $post) {
		$configuration = new PageTypeComposerTargetConfiguration();
		$configuration->setCollectionTypeID($post['ctID']);
		return $configuration;
	}

	
}