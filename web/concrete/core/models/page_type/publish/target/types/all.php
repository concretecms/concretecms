<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_AllPageTypePublishTargetType extends PageTypePublishTargetType {

	public function configurePageTypePublishTarget(PageType $pt, $post) {
		$configuredTarget = new AllPageTypePublishTargetConfiguration($this);
		return $configuredTarget;
	}

	public function configurePageTypePublishTargetFromImport($txml) {
		return new AllPageTypePublishTargetConfiguration($this);
	}

}